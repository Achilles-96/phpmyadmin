<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Front controller for config view / download and clear
 *
 * @package PhpMyAdmin-Setup
 */
use PhpMyAdmin\Config\FormDisplay;
use PhpMyAdmin\Setup\ConfigGenerator;
use PhpMyAdmin\Core;
use PhpMyAdmin\Url;
use PhpMyAdmin\Response;

/**
 * Core libraries.
 */
define('PMA_PATH_TO_BASEDIR', realpath(dirname(__FILE__) . '/..'));
require './lib/common.inc.php';

require './libraries/config/setup.forms.php';

$form_display = new FormDisplay($GLOBALS['ConfigFile']);
$form_display->registerForm('_config.php', $forms['_config.php']);
$form_display->save('_config.php');

$response = Response::getInstance();

if (isset($_POST['eol'])) {
    $_SESSION['eol'] = ($_POST['eol'] == 'unix') ? 'unix' : 'win';
}
if (isset($_POST['apply_config'])) {
    $config_file_name = PMA_PATH_TO_BASEDIR . DIRECTORY_SEPARATOR . CONFIG_FILE;
    if (is_writable(PMA_PATH_TO_BASEDIR) || is_writable($config_file_name)) {
        $config_file = fopen($config_file_name, "w") or die("Unable to open config file!");
        $content = $_POST['textconfig'];
        fwrite($config_file, $content);
        fclose($config_file);
        $response->addHTML(PhpMyAdmin\Message::success(__('Updated config successfully')));
    }
}

if (Core::ifSetOr($_POST['submit_clear'], '')) {
    //
    // Clear current config and return to main page
    //
    $GLOBALS['ConfigFile']->resetConfigData();
    // drop post data
    $response->generateHeader303('index.php' . Url::getCommonRaw());
    exit;
} elseif (Core::ifSetOr($_POST['submit_download'], '')) {
    //
    // Output generated config file
    //
    Core::downloadHeader('config.inc.php', 'text/plain');
    $response->disable();
    echo ConfigGenerator::getConfigFile($GLOBALS['ConfigFile']);
    exit;
} else {
    //
    // Show generated config file in a <textarea>
    //
    $response->generateHeader303('index.php' . Url::getCommonRaw(array('page' => 'config')));
    exit;
}
