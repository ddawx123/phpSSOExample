<?php
    // 模拟数据库
    // abc 用户有 20000 余额
    // hacker 用户有 20 余额
    file_put_contents('/tmp/abc',20000);
    file_put_contents('/tmp/hacker',20);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>XXX Bank</title>
    <link href="//cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="panel panel-default">
                <div class="panel-heading">XXX 银行登录</div>
                <div class="panel-body">
                    <form action="http://www.bank.com/login.php" method="post">
                        <div class="form-group">
                            <input type="text" class="form-control" name="name"
                                   placeholder="用户名为 : 111">
                        </div>
                        <div class="form-group ">
                            <input type="password" class="form-control" name="password"
                                   placeholder="密码为 : 111">
                        </div>

                        <input type="button" onclick="submit()" class="btn btn-info btn-block btn-lg" value="登录">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
