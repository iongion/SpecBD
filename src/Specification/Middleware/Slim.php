<?php
namespace Specification\Middleware;

use Slim\Middleware as SlimMiddleware;

use Specification\Controller;
use Specification\Service;
use Specification\Exception as SpecificationException;
use Specification\Service\Exception as ServiceException;

class Slim extends SlimMiddleware
{

    public $settings;
    public $routeServiceMap = array();
    public $controller;

    public function __construct($settings)
    {
        $this->settings = $settings;
        $this->controller = new Controller($settings['specification']['services']);
    }

    public function call()
    {
        $this->app->hook('slim.before.router', array($this, 'onBeforeRouter'));
        $this->app->hook('slim.before.dispatch', array($this, 'onBeforeDispatch'));
        $this->app->hook('slim.after.router', array($this, 'onAfterRouter'));
        $this->next->call();
    }

    public function forwardFrameworkResponse($response, $details)
    {
        $code = $response['code'];
        // send the headers
        foreach ($response['headers'] as $hkey => $hval) {
            $this->app->response->headers[$hkey] = $hval;
        }
        $isSuccess = ($code >= 200) && ($code < 300);
        // send the body
        if ($isSuccess) {
            $this->app->response->status($response['code']);
            $this->app->response->setBody($response['body']);
        } else {
            $this->app->halt($code, $response['body']);
        }
    }

    public function convertExceptionToResponse($ex, $type = 'unknown', $extra = '')
    {
        $details = $this->getCurrentRouteDetails();
        $message = $ex->getMessage();
        $service = null;
        $body = array(
            'status' => 'error',
            'message' => $message,
            'type' => $type,
            'service' => $details['specification']['name'],
            'extra' => $extra
        );
        $code = 403;
        if (!empty($details['specification']['response']['status'])) {
            $mappings = $details['specification']['response']['status'];
            $errorMessageStatusCodeMap = array();
            foreach ($mappings as $mapping) {
                $errorMessageStatusCodeMap[$mapping['when']] = isset($mapping['code']) ? $mapping['code'] : $code;
            }
            $code = isset($errorMessageStatusCodeMap[$message]) ? $errorMessageStatusCodeMap[$message] : $code;
        }
        return array('body' => $body, 'code' => $code);
    }

    // routes are to be loaded here
    public function onBeforeRouter()
    {
        try {
            // Mount all routes
            $specifications = $this->controller->getSpecifications();
            foreach ($specifications as $class => $specification) {
                $this->mount($class, $specification);
            }
        } catch (SpecificationException $ex) {
            // TODO: handle gracefully for the developer
            switch ($ex->getType()) {
                # Before router
                case SpecificationException::SPEC_UNAVAILABLE:
                    break;
                case SpecificationException::SPEC_REQUIRED:
                    break;
                case SpecificationException::SPEC_INVALID:
                    break;
                case SpecificationException::PROPERTY_SPEC_MISSING:
                    break;
                default:
                    break;
            }
            throw new \Exception($ex->getMessage().' - service '.print_r($ex->getDescription(), true), 0, $ex);
        }
    }

    public function onBeforeDispatch()
    {
        try {
            $route = $this->app->router()->getCurrentRoute();
            $details = $this->getCurrentRouteDetails();
            $this->processRequest($route, $details);
        } catch (SpecificationException $ex) {
            $description = $ex->getDescription();
            $extra = '';
            switch ($ex->getType()) {
                # Before dispatch
                case SpecificationException::SCHEMA_MISSING:
                    $extra = 'schema is missing';
                    break;
                case SpecificationException::SCHEMA_ERROR:
                    $extra = 'schema error';
                    break;
                case SpecificationException::SCHEMA_VIOLATION:
                    $extra = $description['error'];
                    break;
                default:
                    break;
            }
            $details['data']['response'] = $this->convertExceptionToResponse($ex, 'specification', $extra);
            $response = $this->prepareResponse($route, $details);
            $this->forwardFrameworkResponse($response, $details);
        }
    }

    public function onAfterRouter()
    {
        try {
            $route = $this->app->router()->getCurrentRoute();
            $details = $this->getCurrentRouteDetails();
            $response = $this->prepareResponse($route, $details);
            $this->forwardFrameworkResponse($response, $details);
        } catch (SpecificationException $ex) {
            switch ($ex->getType()) {
                # After router
                case SpecificationException::SCHEMA_MISSING:
                    break;
                case SpecificationException::SCHEMA_ERROR:
                    break;
                case SpecificationException::SCHEMA_VIOLATION:
                    break;
                default:
                    break;
            }
            $this->convertExceptionToResponse($ex, 'specification');
        } catch (ServiceException $ex) {
            $this->convertExceptionToResponse($ex, 'runtime.service');
        } catch (\Exception $ex) {
            $this->convertExceptionToResponse($ex, 'runtime.non-service');
        }
    }

    public function mount($class, $spec)
    {
        $middleware = $this;
        $app = $this->app;
        $request = $spec['request'];
        // actual route execution
        $route = $app->map($request['path'], function () use ($app, $spec, $middleware) {
            try {
                $rmap = &$middleware->routeServiceMap[$spec['name']];
                $inst = new $rmap['class'];
                $inst->app = $app;
                $rmap['data']['response']['body'] = $inst->execute($rmap['data']['request.parameters'], $rmap['data']['request.body']);
            } catch (ServiceException $ex) {
                $rmap['data']['response'] = $middleware->convertExceptionToResponse($ex, 'runtime.service');
            } catch (\Exception $ex) {
                $rmap['data']['response'] = $middleware->convertExceptionToResponse($ex, 'runtime.non-service');
            }
        });
        $route->via($request['methods']);
        $route->setName($spec['name']);
        // store a relation between route and spec details through name
        $this->routeServiceMap[$spec['name']] = array(
            'class' => $class,
            'specification' => $spec,
            'data' => array(
              'request.parameters' => null,
              'request.body' => null,
              'response' => array('body' => null, 'code' => 200)
            )
        );
    }
    
    public function getCurrentRouteDetails()
    {
        $route = $this->app->router()->getCurrentRoute();
        if (empty($route)) {
            throw new \Exception('unexpected-empty-route');
        }
        return $this->routeServiceMap[$route->getName()];
    }

    // Decoding

    /**
     * @throws SpecificationException::SCHEMA_ERROR
     */
    public function decodeRequest($route, $details)
    {
        $spec = $details['specification'];
        $service = $spec['name'];
        $params = $route->getParams();
        $body = $this->app->request()->getBody();
        if (!empty($spec['request']['parameters'])) {
            $params = $this->decodeRequestParameters($service, $spec['request']['parameters'], $params);
        }
        if (!empty($spec['request']['body'])) {
            $body = $this->decodeRequestBody($service, $spec['request']['body'], $body);
        }
        $this->routeServiceMap[$service]['data']['request.parameters'] = $params;
        $this->routeServiceMap[$service]['data']['request.body'] = $body;
    }

    /**
     * @throws SpecificationException::SCHEMA_ERROR
     */
    public function decodeRequestParameters($service, $spec, $data)
    {
        if (empty($spec['schema'])) {
        } else {
            $schema = $spec['schema'];
            if (is_array($schema)) {
                $data = $this->decodeServiceDataWithSchemaArray($service, $data, $schema);
            } elseif (is_string($schema)) {
                $data = $this->decodeServiceDataWithSchemaFile($service, $data, $schema);
            }
        }
        return $data;
    }

    public function decodeRequestBody($service, $spec, $data)
    {
        $body = json_decode($this->app->request()->getBody());
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new SpecificationException(SpecificationException::REQUEST_CASTING_FAILED, 'invalid request body');
        }
        return $body;
    }

    /**
     * @throws SpecificationException::SCHEMA_ERROR
     */
    protected function decodeServiceDataWithSchemaArray($service, $data, $schema)
    {
        return $this->controller->decodeDataWithSchemaArray($data, $schema);
    }

    /**
     * @throws SpecificationException::SCHEMA_ERROR
     */
    protected function decodeServiceDataWithSchemaFile($service, $data, $schema)
    {
        $schemaPath = $this->settings['specification']['schemaRoot'] . '/' . $service . '-' . $schema;
        return $this->controller->decodeDataWithSchemaFile($data, $schemaPath);
    }

    // Validation

    /**
     * Validates current request according to the specification.
     *
     * Performs validation and clean-up(type-casting)
     * - validate path: implicit, done by Slim framework
     * - validate request methods: implicit, done by Slim framework
     * - validate request parameters: done by middleware according to the parameters specification
     * - validate request body: done by the middleware according by the JSON document schema
     *
     * @param Slim\Route $route the current route
     * @param mixed $details details of current route's service specification
     * @throws SpecificationException
     */
    public function validateRequest($route, $details)
    {
        $spec = $details['specification'];
        $name = $spec['name'];
        if (!empty($spec['request']['parameters'])) {
            if (empty($this->routeServiceMap[$name]['data']['request.parameters'])) {
                throw new SpecificationException(SpecificationException::REQUEST_VALIDATION_FAILED, 'empty-parameters');
            }
            $params = $this->routeServiceMap[$name]['data']['request.parameters'];
            $this->validateWithSpecSchema($name, $spec['request']['parameters'], $params);
        }
        if (!empty($spec['request']['body'])) {
            if (empty($this->routeServiceMap[$name]['data']['request.body'])) {
                throw new SpecificationException(SpecificationException::REQUEST_VALIDATION_FAILED, 'empty-body');
            }
            $body = $this->routeServiceMap[$name]['data']['request.body'];
            $this->validateWithSpecSchema($name, $spec['request']['body'], $body);
        }
    }

    public function validateWithSpecSchema($name, $spec, $data)
    {
        if (empty($spec['schema'])) {
        } else {
            if (is_array($spec['schema'])) {
                $data = $this->validateServiceDataWithSchemaArray($name, $data, $spec['schema']);
            } elseif (is_string($spec['schema'])) {
                $data = $this->validateServiceDataWithSchemaFile($name, $data, $spec['schema']);
            }
        }
        return $data;
    }

    protected function validateServiceDataWithSchemaArray($name, $data, $schema)
    {
        return $this->controller->validateDataWithSchemaArray($data, $schema);
    }

    protected function validateServiceDataWithSchemaFile($name, $data, $schema)
    {
        $schemaPath = $this->settings['specification']['schemaRoot'] . '/' . $name . '-' . $schema;
        return $this->controller->validateDataWithSchemaFile($data, $schemaPath);
    }

    public function processRequest($route, $details)
    {
        $this->decodeRequest($route, $details);
        $this->validateRequest($route, $details);
    }

    // Response
    
    public function validateResponse($route, $details)
    {
        if (empty($details['specification']['response'])) {
            // do nothing - response specification is optional
        } else {
            $data = $details['data']['response']['body'];
            $name = $details['specification']['name'];
            $spec = $details['specification']['response'];
            if (!empty($details['specification']['response']['body']['schema'])) {
                return $this->validateServiceDataWithSchemaFile($name, $data, $details['specification']['response']['body']['schema']);
            }
        }
    }
    
    public function sanitizeResponse($route, $details)
    {
        $body = $details['data']['response']['body'];
        $spec = $details['specification'];
        // TODO: sanitize according to response body schema specification
        return array(
            'headers' => array(),
            'code' => empty($details['data']['response']['code']) ? 200 : $details['data']['response']['code'],
            'body' => $body
        );
    }

    public function prepareResponse($route, $details)
    {
        $this->validateResponse($route, $details);
        $sanitized = $this->sanitizeResponse($route, $details);
        return $this->formatResponse($sanitized);
    }
    
    public function formatResponse($response)
    {
        $details = $this->getCurrentRouteDetails();
        $spec = $details['specification'];
        $default = $this->getDefaultResponse();
        // override default with spec
        if (!empty($spec['response']['headers']) && is_array($spec['response']['headers'])) {
            $headers = array_merge($default['headers'], $spec['response']['headers']);
            $default['headers'] = array_merge($headers, $spec['response']['headers']);
        }
        $response = array_merge_recursive($default, $response);
        $code = $response['code'];
        $headers = $response['headers'];
        // serialize the body based on spec and content type
        $body = $response['body'];
        $contentType = $headers['Content-Type'];
        switch ($contentType) {
            case 'application/json':
                $body = json_encode($body);
                break;
            case 'text/html':
            case 'text/plain':
                if (!is_string($body)) {
                    // TODO: check if it is an error and format it properly
                    $body = 'Complex response: '.json_encode($body);
                }
                break;
            default:
                break;
        }
        return array('body' => $body, 'headers' => $headers, 'code' => $code);
    }
    
    public function getDefaultResponse()
    {
        $resp = array(
          'headers' => array(
            'Content-Type' => empty($this->settings['transport']) ? 'text/plain' : $this->settings['transport']
          )
        );
        return $resp;
    }
}
