<?php
/**
 * Created by PhpStorm.
 * User: 89745
 * Date: 2016/12/28
 * Time: 13:16
 */


require_once './Proxy.php';


$proxy = new Proxy();
$proxy->attach();

$user = $proxy->userinfo();
if (!$user) {
    header('Location:login.php', true, 307);
    exit();
}
?>
<!doctype html>
<html>
<head>
    <title><?= $broker->broker ?> (Single Sign-On demo)</title>
    <link href="//cdn.bootcss.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h1><?= $proxy->getProxy() ?>
        <small>(Single Sign-On demo)</small>
    </h1>
    <h3>Logged in</h3>

    <pre><?= json_encode($user, JSON_PRETTY_PRINT); ?></pre>

    <a id="logout" class="btn btn-default" href="login.php?logout=1">Logout</a>
</div>
</body>
</html>