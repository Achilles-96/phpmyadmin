<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Holds ServerEnginesControllerTest class
 *
 * @package PhpMyAdmin-test
 */

use PhpMyAdmin\StorageEngine;
use PMA\libraries\Theme;
use PMA\libraries\controllers\server\ServerEnginesController;
use PMA\libraries\URL;

require_once 'libraries/database_interface.inc.php';

require_once 'test/PMATestCase.php';

/**
 * Tests for ServerEnginesController class
 *
 * @package PhpMyAdmin-test
 */
class ServerEnginesControllerTest extends PMATestCase
{
    /**
     * Prepares environment for the test.
     *
     * @return void
     */
    public function setUp()
    {
        //$_REQUEST
        $_REQUEST['log'] = "index1";
        $_REQUEST['pos'] = 3;

        //$GLOBALS
        $GLOBALS['server'] = 0;
        $GLOBALS['table'] = "table";

        //$_SESSION
    }

    /**
     * Tests for _getHtmlForAllServerEngines() method
     *
     * @return void
     */
    public function testGetHtmlForAllServerEngines()
    {
        $class = new ReflectionClass('\PMA\libraries\controllers\server\ServerEnginesController');
        $method = $class->getMethod('_getHtmlForAllServerEngines');
        $method->setAccessible(true);

        $ctrl = new ServerEnginesController();
        $html = $method->invoke($ctrl);

        //validate 1: Item header
        $this->assertContains(
            '<th>Storage Engine</th>',
            $html
        );
        $this->assertContains(
            '<th>Description</th>',
            $html
        );

        //validate 2: FEDERATED
        $this->assertContains(
            '<td>Federated MySQL storage engine</td>',
            $html
        );
        $this->assertContains(
            'FEDERATED',
            $html
        );
        $this->assertContains(
            'server_engines.php?engine=FEDERATED',
            $html
        );

        //validate 3: dummy
        $this->assertContains(
            '<td>dummy comment</td>',
            $html
        );
        $this->assertContains(
            'dummy',
            $html
        );
        $this->assertContains(
            'server_engines.php?engine=dummy',
            $html
        );
    }

    /**
     * Tests for _getHtmlForServerEngine() method
     *
     * @return void
     */
    public function testGetHtmlForServerEngine()
    {
        $_REQUEST['engine'] = "Pbxt";
        $_REQUEST['page'] = "page";
        //Mock DBI
        $dbi = $this->getMockBuilder('PhpMyAdmin\DatabaseInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $GLOBALS['dbi'] = $dbi;

        $class = new ReflectionClass('\PMA\libraries\controllers\server\ServerEnginesController');
        $method = $class->getMethod('_getHtmlForServerEngine');
        $method->setAccessible(true);

        $engine_plugin = StorageEngine::getEngine("Pbxt");
        $ctrl = new ServerEnginesController();
        $html = $method->invoke($ctrl, $engine_plugin);

        //validate 1: Engine title
        $this->assertContains(
            htmlspecialchars($engine_plugin->getTitle()),
            $html
        );

        //validate 2: Engine Mysql Help Page
        $this->assertContains(
            PMA\libraries\Util::showMySQLDocu($engine_plugin->getMysqlHelpPage()),
            $html
        );

        //validate 3: Engine Comment
        $this->assertContains(
            htmlspecialchars($engine_plugin->getComment()),
            $html
        );

        //validate 4: Engine Info Pages
        $this->assertContains(
            __('Variables'),
            $html
        );
        $this->assertContains(
            URL::getCommon(
                array('engine' => $_REQUEST['engine'], 'page' => "Documentation")
            ),
            $html
        );

        //validate 5: other items
        $this->assertContains(
            URL::getCommon(array('engine' => $_REQUEST['engine'])),
            $html
        );
        $this->assertContains(
            $engine_plugin->getSupportInformationMessage(),
            $html
        );
        $this->assertContains(
            'There is no detailed status information available for this '
            . 'storage engine.',
            $html
        );
    }
}
