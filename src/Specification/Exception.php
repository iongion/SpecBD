<?php
namespace Specification;

class Exception extends \Exception
{

    public $description;

    const PARSER_ERROR = 'PARSER_ERROR';

    const SPEC_UNAVAILABLE = 'SPEC_UNAVAILABLE';
    const SPEC_REQUIRED = 'SPEC_REQUIRED';
    const SPEC_INVALID = 'SPEC_INVALID';

    const SCHEMA_MISSING = 'SCHEMA_MISSING';
    const SCHEMA_ERROR = 'SCHEMA_ERROR';
    const SCHEMA_VIOLATION = 'SCHEMA_VIOLATION';

    const PROPERTY_SPEC_MISSING = 'PROPERTY_SPEC_MISSING';

    const REQUEST_CASTING_FAILED = 'REQUEST_CASTING_FAILED';
    const REQUEST_VALIDATION_FAILED = 'REQUEST_VALIDATION_FAILED';

    public function __construct($type, $description, \Exception $previous = null)
    {
        parent::__construct($type, 0, $previous);
        $this->description = $description;
    }

    public function getType()
    {
        return $this->message;
    }

    public function getDescription()
    {
        return $this->description;
    }
}
