<?php

session_start();
$from = $_POST['from'];
$to = $_POST['to'];
$money = $_POST['money'];

//通过session进行身份验证.
if ($_SESSION[$from]) {
    $moneyFrom = file_get_contents('/tmp/abc');
    if ($money < $moneyFrom) {
        
        //验证通过,进行转账的业务操作.
        $moneyFrom -= $money;
        file_put_contents('/tmp/abc', $moneyFrom);

        $moneyTo = file_get_contents('/tmp/hacker');
        $moneyTo += $money;
        file_put_contents('/tmp/hacker', $moneyTo);
    }
}	 
