<?php
namespace Specification;

use Specification;
use Specification\Exception as SpecificationException;

use JsonSchema\Uri\UriRetriever;
use JsonSchema\Validator as JsonSchemaValidator;
use JsonSchema\Exception\JsonDecodingException;

class Controller
{

    protected $specifications = array();
    protected $serviceSpecificationMap = array();

    public function __construct($loaders = null)
    {
        if (!(empty($loaders['classes']))) {
            $this->loadFromClassesArray($loaders['classes']);
        }
        if (!(empty($loaders['namespaces']))) {
            $this->loadFromNamespacesPrefix($loaders['namespaces']);
        }
    }

    public function loadFromClassesArray($classes)
    {
        foreach ($classes as $class) {
            $spec = $this->parseClass($class);
            $this->serviceSpecificationMap[$spec['name']] = $spec;
            $this->specifications[$class] = $spec;
        }
    }

    public function loadFromNamespacesPrefix($namespaces)
    {
        // TODO: implement easy of use
    }

    /**
     * @throws SpecificationException::SPEC_UNAVAILABLE
     * @throws SpecificationException::SPEC_REQUIRED
     * @throws SpecificationException::SPEC_INVALID
     * @throws SpecificationException::PROPERTY_SPEC_MISSING
     */
    public function parseClass($class)
    {
        try {
            $rc = new \ReflectionClass($class);
            $source = $rc->getStaticPropertyValue('specification', null);
        } catch (\Exception $ex) {
            $detail = array('class' => $class);
            if (!class_exists($class, false)) {
                $detail['reason'] = 'class missing or wrong namespace';
            }
            throw new SpecificationException(SpecificationException::SPEC_UNAVAILABLE, $detail, $ex);
        }
        if (empty($source)) {
            throw new SpecificationException(SpecificationException::SPEC_REQUIRED, array('class' => $class));
        }
        $spec = json_decode($source, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new SpecificationException(SpecificationException::SPEC_INVALID, array('class' => $class));
        }
        $required = array('name', 'category', 'description', 'request');
        foreach ($required as $property) {
            if (!isset($spec[$property])) {
                throw new SpecificationException(SpecificationException::PROPERTY_SPEC_MISSING, array('class' => $class, 'property' => $property));
            }
        }
        if (!isset($spec['request']['path'])) {
            throw new SpecificationException(SpecificationException::PROPERTY_SPEC_MISSING, array('class' => $class, 'property' => 'request.path'));
        }
        if (!empty($spec['request']['parameters']['schema'])) {
            $spec['request']['parameters']['schema'] = $this->parseSchema('request-parameters', $spec['request']['parameters']['schema'], $spec);
        }
        if (!empty($spec['request']['body']['schema'])) {
            $spec['request']['body']['schema'] = $this->parseSchema('request-body', $spec['request']['body']['schema'], $spec);
        }
        if (!empty($spec['response']['body']['schema'])) {
            $spec['response']['body']['schema'] = $this->parseSchema('response-body', $spec['response']['body']['schema'], $spec);
        }
        return $spec;
    }

    public function getSpecifications()
    {
        return $this->specifications;
    }

    public function getServiceSpecificationMap()
    {
        return $this->serviceSpecificationMap;
    }

    /**
     * Parse schema - populate with default values if they are not specified.
     *
     * - $schema
     * - title
     * - description
     * - type
     * - properties
     * - required
     */
    public function parseSchema($title, $schema, $specification)
    {
        if (is_array($schema)) {
            if (empty($schema['$schema'])) {
                $schema['$schema'] = sprintf(
                    'local://schema.service/%s/%s#%s',
                    $specification['category'],
                    $specification['name'],
                    $title
                );
            }
            if (empty($schema['title'])) {
                $schema['title'] = $specification['name'];
            }
            if (empty($schema['description'])) {
                $schema['description'] = $specification['description'];
            }
            if (empty($schema['type'])) {
                $schema['type'] = 'object';
            }
            // if (empty($schema['properties'])) $schema['properties'] = array();
            // if (empty($schema['required'])) $schema['required'] = array();
        }
        return $schema;
    }

    /**
     * @throws SpecificationException::SCHEMA_ERROR
     */
    public function decodeDataWithSchemaArray($data, $schema)
    {
        if (empty($schema['properties'])) {
            return $data;
        }
        foreach ($schema['properties'] as $property => $definition) {
            if (empty($definition['type'])) {
                throw new SpecificationException(SpecificationException::SCHEMA_ERROR, array('schema' => $schema['$schema'], 'error' => 'missing property type'));
            }
            // cast received values
            if (!isset($data[$property])) {
                continue;
            }
            switch ($definition['type']) {
                case 'number':
                    $ival = intval($data[$property]);
                    $fval = floatval($data[$property]);
                    if ($ival == $fval) {
                        $data[$property] = $ival;
                    } else {
                        $data[$property] = $fval;
                    }
                    break;
                case 'boolean':
                    $data[$property] = $data[$property] ? true : false;
                    break;
                case 'null':
                    $data[$property] = null;
                // for the cases bellow $data[$property] is not changed
                case 'string':
                case 'object':
                case 'array':
                case 'any':
                case 'choice':
                default:
                    break;
            }
        }
        return $data;
    }

    /**
     * @throws SpecificationException::SCHEMA_MISSING
     * @throws SpecificationException::SCHEMA_ERROR
     */
    public function decodeDataWithSchemaFile($data, $path)
    {
        if (!file_exists($path)) {
            throw new SpecificationException(SpecificationException::SCHEMA_MISSING, array('schema' => $path));
        }
        $contents = file_get_contents($path);
        $schema = json_decode($contents);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new SpecificationException(SpecificationException::SCHEMA_ERROR, array('schema' => $path));
        }
        $data = $this->decodeDataWithSchemaArray($data, $schema);
        return $data;
    }

    /**
     * @throws SpecificationException::SCHEMA_MISSING
     * @throws SpecificationException::SCHEMA_ERROR
     * @throws SpecificationException::SCHEMA_VIOLATION
     */
    public function validateDataWithSchemaFile($data, $path)
    {
        if (!file_exists($path)) {
            throw new SpecificationException(SpecificationException::SCHEMA_MISSING, array('schema' => $path));
        }
        try {
            $retriever = new UriRetriever;
            $schema = $retriever->retrieve('file://' . realpath($path));
            $this->validateDataWithSchemaArray($data, $schema);
        } catch (JsonDecodingException $ex) {
            throw new SpecificationException(SpecificationException::SCHEMA_ERROR, array('schema' => $path), $ex);
        }
        return $data;
    }

    /**
     * @throws SpecificationException::SCHEMA_MISSING
     * @throws SpecificationException::SCHEMA_ERROR
     * @throws SpecificationException::SCHEMA_VIOLATION
     */
    public function validateDataWithSchemaArray($data, $schema)
    {
        if (empty($schema)) {
            throw new SpecificationException(SpecificationException::SCHEMA_MISSING, array('schema' => $schema));
        }
        try {
            $validator = new JsonSchemaValidator();
            $schema = is_object($schema) ? $schema : json_decode(json_encode($schema, true));
            if ($schema->type === 'object') {
                $payload = json_decode(json_encode($data, true));
            } else {
                // this is a scalar - wrap it in an object to make json validator work
                // replace with dummy schema for scalars
                $schema = (object)array(
                    'type' => 'object',
                    'properties' => (object) array(
                        'body' => $schema
                    )
                );
                if (!empty($schema->required)) {
                    $dummy->required = array('body');
                }
                $payload = (object) array('body' => $data);
            }
            // actual validation
            $validator->check($payload, $schema);
            if (!$validator->isValid()) {
                $error = current($validator->getErrors());
                $detail = empty($error['property']) ? $error['message'] : ($error['property'].' '.$error['message']);
                throw new SpecificationException(SpecificationException::SCHEMA_VIOLATION, array('schema' => $schema, 'error' => $detail, 'data' => $data));
            }
        } catch (\ErrorException $ex) {
            throw new SpecificationException(SpecificationException::SCHEMA_ERROR, array('schema' => $schema), $ex);
        }
        return $data;
    }
}
