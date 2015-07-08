<?php
use Robo\Tasks as RoboTasks;
/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends RoboTasks
{
    
    public function test($tags='')
    {
        $this->testUnit();
    }
    
    public function testUnit($filter='')
    {
        $this->localExec('phpunit -c config/phpunit.xml --stop-on-error --testsuite unit' . (empty($filter) ? '' : " --filter $filter"));
    }
    
    public function styleCheck()
    {
        $this->localExec('phpcs -n -p -v --standard=PSR2 src/classes tests/helpers tests/unit/classes');
    }
    
    public function styleFix()
    {
        $this->localExec('phpcbf -n -p -v --standard=PSR2 src/classes tests/helpers tests/unit/classes');
    }
    
    public function docUpdate()
    {
        $this->localExec('sami.php update config/sami.config.php -v');
    }

    protected function localExec($command)
    {
        $parts = explode(' ', $command);
        if (empty($_SERVER['ComSpec']) || (!empty($_SERVER['term']) && ($_SERVER['term'] === 'cygwin'))) {
            $program = sprintf('sh bin/%s', $parts[0]);
        } else {
            $program = sprintf('bin\\%s.bat', $parts[0]);
        }
        $this->taskExec($program.' '.implode(' ', array_slice($parts, 1)))
             ->run();
    }
}
