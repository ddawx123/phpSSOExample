<?php
/**
 * Created by PhpStorm.
 * User: 89745
 * Date: 2016/12/28
 * Time: 13:19
 */

require_once './Proxy.php';

$proxy = new Proxy();
$proxy->attach();

if (!empty($_GET['logout'])) {
    $proxy->logout();
    header('Location:login.php', true, 307);
    exit();
} elseif ($proxy->userinfo() || ($_SERVER['REQUEST_METHOD']) === 'POST' && $proxy->login($_POST['username'], $_POST['password'])) {
    header('Location:index.php', true, 302);
    exit();
}
?>

<!doctype html>
<html>
<head>
    <title><?= $broker->broker ?> | Login (Single Sign-On demo)</title>
    <link href="//cdn.bootcss.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">

    <style>
        h1 {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
<div class="container">

    <h1>登录.......</h1>
    <div class="alert alert-warning" role="alert"><?php echo $GLOBALS['msg']; ?></div>
    <form class="form-horizontal" action="login.php" method="post">
        <div class="form-group">
            <label for="inputUsername" class="col-sm-2 control-label">Username</label>
            <div class="col-sm-10">
                <input type="text" name="username" class="form-control" id="inputUsername">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword" class="col-sm-2 control-label">Password</label>
            <div class="col-sm-10">
                <input type="password" name="password" class="form-control" id="inputPassword">
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-default">Login</button>
            </div>
        </div>
    </form>
</div>
</body>
</html>



