<?php
/**
 * Created by PhpStorm.
 * User: 89745
 * Date: 2016/12/27
 * Time: 10:25
 */

$name = $_POST['name'];
$password = $_POST['password'];

if ('abc' == $name && 111 == $password) {
    if(session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    // 表示用户已经登录
    $_SESSION[$name] = 1;
    header("Location:admin.html",true,302);
} else {
    header("Location:index.html", true, 302);
}
