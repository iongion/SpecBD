<?php
namespace Specification\Service;

class Exception extends \Exception
{

    public function __construct($message)
    {
        parent::__construct($message, 0);
    }
}
