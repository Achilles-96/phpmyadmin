<?php

/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Front controller for setup script
 *
 * @package PhpMyAdmin-Setup
 * @license https://www.gnu.org/licenses/gpl.html GNU GPL 2.0
 */
define('PMA_PATH_TO_BASEDIR', realpath(dirname(__FILE__) . '/..'));

require './lib/common.inc.php';

if (@file_exists(CONFIG_FILE) && ! $cfg['DBG']['demo']) {
    if (!(isset($cfg['SetupPassword']) && $cfg['SetupPassword'] !== '')) {
        header('Location: index.php');
        exit;
    }
    if (isset($_POST['SetupPassword']) && $_POST['SetupPassword'] === $cfg['SetupPassword']) {
        $_SESSION['SetupAuthenticated'] = true;
        header('Location: index.php');
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}

?>


<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="utf-8" />
<title>phpMyAdmin setup</title>
<link href="../favicon.ico" rel="icon" type="image/x-icon" />
<link href="../favicon.ico" rel="shortcut icon" type="image/x-icon" />
<link href="styles.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../js/vendor/jquery/jquery.min.js"></script>
<script type="text/javascript" src="../js/vendor/jquery/jquery-ui.min.js">
</script>
<script type="text/javascript" src="ajax.js"></script>
<script type="text/javascript" src="../js/config.js"></script>
<script type="text/javascript" src="scripts.js"></script>
<script type="text/javascript" src="../js/messages.php"></script>
</head>
<body>
<h1><span class="blue">php</span><span class="orange">MyAdmin</span>  setup</h1>
<div id="page">
<h3> Please enter setup password </h3>
<form method="post" action="setupauth.php">
<input type="hidden" name="token" value="<?php echo $_SESSION[' PMA_token ']; ?>">
<input type="password" name="SetupPassword">
<input type="submit" value="submit">
</form>
</div>
</body>
</html>
