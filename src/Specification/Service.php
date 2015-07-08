<?php
namespace Specification;

/**
 * Specification service
 *
 * @package Specification
 * @abstract
 */
abstract class Service
{
    /**
     * Service specification text, a custom JSON based DSL.
     * The behavior or the service is controlled by the specification.
     * Behavior is split between request and response.
     *
     * @access public
     * @static string
     */
    public static $specification = null;

    /**
     * Reference to currently instantiated Slim framework application.
     * @link http://dev.slimframework.com/phpdocs/classes/Slim.Slim.html
     *
     * @access public
     * @var Slim\Slim Slim application
     */
    public $app = null;

    /**
     * This method gets executed when router selects its matching side.
     *
     * @abstract
     * @access public
     * @param array $params - GET parameters
     * @param array $input  - Request body
     * @return mixed
     */
    abstract public function execute($params = array(), $input = null);
}
