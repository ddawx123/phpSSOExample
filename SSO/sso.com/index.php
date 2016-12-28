<?php
/**
 * Created by PhpStorm.
 * User: 89745
 * Date: 2016/12/27
 * Time: 21:01
 */

require_once './SSOServer.php';

$sso = new SSOServer();

$command = $_GET['command'];

if (is_null($command) || !method_exists($sso, $command)) {
    echo "no method $command";
    exit();
}

$sso->$command();