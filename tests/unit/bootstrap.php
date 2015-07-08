<?php
$root = dirname(dirname(__DIR__));
require $root . '/vendor/autoload.php';
putenv(sprintf('PHPUNIT_PROJECT_ROOT=%s', realpath($root)));
