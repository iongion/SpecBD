<?php
$root = dirname(__DIR__);

use Sami\Sami;
use Sami\RemoteRepository\GitHubRemoteRepository;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->exclude('Resources')
    ->exclude('Tests')
    ->in($root . '/src/classes');

return new Sami($iterator, array(
    'title'     => 'Specification API library',
    'build_dir' => $root.'/doc/generated',
    'cache_dir' => $root.'/doc/cache'
));
