<?php
/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */

class RoboFile extends \Robo\Tasks
{
    use Robo\Task\Base\loadShortcuts;

    /**
     * Complete all Project Setup tasks
     */
    function setup() {
        $this->_exec('vendor/bin/robo clone:files');
        $this->_exec('vendor/bin/codecept build');
    }

    /**
     * Duplicate the Example configuration files used to customize the Project for customization
     */
    function cloneFiles() {
        $this->_exec('cp -vn .env.example .env');
        $this->_exec('cp -vn codeception.dist.yml codeception.yml');
        $this->_exec('cp -vn tests/acceptance.suite.dist.yml tests/acceptance.suite.yml');
    }

    /**
     * Build the Codeception project
     */
    function buildProject() {
        $this->cloneFiles();
        $this->_exec('vendor/bin/codecept build');
    }

    /**
     * Generate all Tests
     */
    function generateTests()
    {
        require 'tests/acceptance/_bootstrap.php';
        \Magento\AcceptanceTestFramework\Util\TestGenerator::getInstance()->createAllCestFiles();
        $this->say("Generate Tests Command Run");
    }

    /**
     * Run all Acceptance tests using the Chrome environment
     */
    function chrome()
    {
        $this->_exec('codecept run acceptance --env chrome --skip-group skip');
        $this->allureReport();
    }

    /**
     * Run all Acceptance tests using the FireFox environment
     */
    function firefox()
    {
        $this->_exec('codecept run acceptance --env firefox --skip-group skip');
        $this->allureReport();
    }

    /**
     * Run all Acceptance tests using the PhantomJS environment
     */
    function phantomjs()
    {
        $this->_exec('codecept run acceptance --env phantomjs --skip-group skip');
        $this->allureReport();
    }

    /**
     * Run all Tests with the specified @group tag, excluding @group 'skip', using the Chrome environment
     */
    function group($args = '')
    {
        $this->taskExec('codecept run acceptance --verbose --steps --env chrome --skip-group skip --group')->args($args)->run();
    }

    /**
     * Run all Acceptance tests located under the Directory Path provided using the Chrome environment
     */
    function folder($args = '')
    {
        $this->taskExec('codecept run acceptance --env chrome')->args($args)->run();
    }

    /**
     * Run all Tests marked with the @group tag 'example', using the Chrome environment
     */
    function example()
    {
        $this->_exec('codecept run --env chrome --group example --skip-group skip');
        $this->allureReport();
    }

    /**
     * Generate the HTML for the Allure report based on the Test XML output
     */
    function allureGenerate()
    {
        return $this->_exec('allure generate tests/_output/allure-results/ -o tests/_output/allure-report/');
    }

    /**
     * Open the HTML Allure report
     */
    function allureOpen()
    {
        $this->_exec('allure report open --report-dir tests/_output/allure-report/');
    }

    /**
     * Generate and open the HTML Allure report
     */
    function allureReport()
    {
        $result1 = $this->allureGenerate();

        if ($result1->wasSuccessful()) {
            $this->allureOpen();
        }
    }
}
