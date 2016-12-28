<?php

/**
 * Created by PhpStorm.
 * User: 89745
 * Date: 2016/12/27
 * Time: 20:08
 */
class SSOServer
{
    private $_proxyId;

    private $_token;


    // 模拟的用户信息,正常应该保存在数据库中
    public $user = [
        'tom' => [
            'password' => '111111',
            'info' => 'this si some information about tom'
        ],
        'jay' => [
            'password' => '222222',
            'info' => 'the best singer in china !'
        ]
    ];

    private $dir = '/tmp';

    /**
     * @var array
     * proxy 跟 sso server 商定好的secret key.
     */
    public $proxy = [
        'proxyA' => ['secretKey' => 'abc'],
        'proxyB' => ['secretKey' => 'xyz']
    ];

    /**
     * 进行session 传递用
     */
    public function attach()
    {
        if (!isset($_GET['proxyId'])) {
            echo 'no proxy id';
            exit();
        }

        if (!isset($_GET['token'])) {
            echo 'no token in url';
            exit();
        }

        if (!isset($_GET['checksum'])) {
            echo 'no checksum in url';
            exit();
        }

        $this->_proxyId = $_GET['proxyId'];
        $this->_token = $_GET['token'];
        $checksum = $_GET['checksum'];
        $retUrl = $_GET['returnUrl'];

        $newChecksum = $this->generateChecksum(__FUNCTION__);

        if ($newChecksum != $checksum) {
            echo '校验失败 !';
            exit();
        }

        $this->startSession();
        $fileName = $this->generateFileName();
        $this->saveSessionId($fileName, session_id());

        header("Location:{$retUrl}", true, 307);
    }

    public function userinfo()
    {
        if (!$this->checkHeader()) {
            echo 'authorization header fail';
            exit();
        }

        $fileName = $this->generateFileName();

        $sessionId = $this->getSessionId($fileName);
        session_id($sessionId);
        $this->startSession();

        $user = null;
        if (isset($_SESSION[$this->_proxyId])) {
            $username = $_SESSION[$this->_proxyId];
            if ($username) {
                $user = $this->user[$username];
                $user['username'] = $username;
            }
        }

        header('Content-type:application/json; charset=UTF-8');
        echo json_encode($user);
    }

    /**
     * 重新生成 checksum
     */
    public function generateChecksum($fun)
    {
        $secretKey = $this->getSecretKey();
        $checksum = hash('sha256', $fun . $this->_token . $secretKey);
        return $checksum;
    }


    /**
     * 登录
     */
    public function login()
    {

        if (!$this->checkHeader()) {
            echo json_encode([
                'code' => -1,
                'msg' => 'header authorization fail'
            ]);
            exit();
        }

        $username = $_POST['username'];
        $password = $_POST['password'];

        if (!$this->authorization($username, $password)) {
            echo json_encode([
                'code' => 0,
                'msg' => 'login fail maybe username or password wrong'
            ]);
            exit();
        }

        $userinfo = $this->user[$username];
        $userinfo['username'] = $username;
        echo json_encode([
            'code' => 1,
            $userinfo
        ], true);
        exit();
    }


    private function checkHeader()
    {
        $headers = getallheaders();

        if (!isset($headers['Authorization'])) {
            return false;
        }
        $authorization = $headers['Authorization'];

        if (!preg_match('/^SSO-(\w*)-(\w*)-([a-z0-9]*)$/', $authorization, $matches)) {
            return false;
        }

        $this->_proxyId = $matches[1];
        $this->_token = $matches[2];
        $checksum = $matches[3];


        if ($this->generateChecksum('session') != $checksum) {
            return false;
        }
        return true;
    }

    /**
     *
     * 获取当前子系统的 secret key
     */
    public function getSecretKey()
    {
        if (!isset($this->_proxyId)) {
            return null;
        }
        return $this->proxy[$this->_proxyId]['secretKey'];
    }

    /**
     * 退出
     */
    public function logout()
    {
        if (!$this->checkHeader()) {
            echo 'header authorization fail';
            exit();
        }

        $fileName = $this->generateFileName();
        session_id($this->getSessionId($fileName));
        $this->startSession();

        session_unset();
    }

    // 应该在数据库进行校验
    public function authorization($username, $password)
    {
        if (is_null($username) || is_null($password)) {
            return false;
        }

        if (!key_exists($username, $this->user)) {
            return false;
        }
        if ($this->user[$username]['password'] != $password) {
            return false;
        }

        $fileName = $this->generateFileName();
        session_id($this->getSessionId($fileName));
        $this->startSession();

        $_SESSION[$this->_proxyId] = $username;
        return true;
    }


    /**
     *
     */
    public function getSessionId($fileName)
    {
        $path = $this->dir . DIRECTORY_SEPARATOR . $fileName;
        $res = file_get_contents($path);
        return $res;
    }

    /**
     * 开启session
     */
    private function startSession()
    {
        if (PHP_SESSION_ACTIVE !== session_status()) {
            session_start();
        }
    }

    /**
     * @param $token
     * @return string
     *
     */
    private function generateFileName()
    {
        $secretKey = $this->getSecretKey();

        return "SSO-{$this->_proxyId}-{$this->_token}-" . $this->generateChecksum('session');
    }

    /**
     * @param $fileName
     * @param $session_id
     * 把session id 保存到
     */
    private function saveSessionId($fileName, $session_id)
    {
        file_put_contents($this->dir . DIRECTORY_SEPARATOR . $fileName, $session_id);
    }
}