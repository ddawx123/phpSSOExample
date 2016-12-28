<?php

/**
 * Created by PhpStorm.
 * User: 89745
 * Date: 2016/12/27
 * Time: 21:04
 */
class Proxy
{
    private $_ssoUrl = 'http://www.sso.com';

    private $_proxyId = 'proxyB';

    private $_secret = 'xyz';

    public $_token;

    private $_userinfo;

    public function __construct()
    {
        if (isset($_COOKIE[$this->getCookieName()])) {
            $this->_token = $_COOKIE[$this->getCookieName()];
        }
    }

    public function getProxy()
    {
        return $this->_proxyId;
    }

    public function getCookieName()
    {
        return 'sso_token_' . $this->_proxyId;
    }


    public function attach()
    {
        if (isset($this->_token)) {
            return;
        }
        $this->generateToken();

        $returnUrl = $this->generateReturnUrl();
        $checksum = $this->generateChecksum(__FUNCTION__);

        $query = [
                'command' => __FUNCTION__,
                'proxyId' => $this->_proxyId,
                'token' => $this->_token,
                'checksum' => $checksum,
                'returnUrl' => $returnUrl
            ] + $_GET;

        $redirectUrl = $this->_ssoUrl . '?' . http_build_query($query);
        header("Location:$redirectUrl", true, 307);
        exit();
    }

    /**
     * @param $fun
     * @return string
     * 生成校验和
     */
    public function generateChecksum($fun)
    {
        return hash('sha256', $fun . $this->_token . $this->_secret);
    }

    /**
     * 生成 token 值
     */
    public function generateToken()
    {
        if (isset($this->_token)) {
            return;
        }

        $this->_token = base_convert(md5(uniqid(rand(), true)), 16, 36);
        //第三个参数意味着在1个小时之后,就得重新登录
        setcookie($this->getCookieName(), $this->_token, time() + 3600, '/');
    }

    /**
     * @return string
     * sso 将重定向的url
     */
    private function generateReturnUrl()
    {
        $protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';
        return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }


    public function userinfo()
    {
        if (!isset($this->_userinfo)) {
            $this->_userinfo = $this->curl('GET', 'userinfo');
        }
        return $this->_userinfo;
    }

    /**
     * @param $method
     * @param $command
     * @param null $data
     * @return mixed|null
     * 向 sso 发起请求
     */
    public function curl($method, $command, $data = null)
    {
        if (!isset($this->_token)) {
            echo 'no token !';
            exit();
        }

        $url = $this->getRequestUrl($command);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        // 这里请求头携带一个自定义参数  authorization 包含 sso 服务中的文件名
        // 从这个文件名中可以获取到当前client的session.
        // 同时这个authorization字段可以对当前的请求进行验证.
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept:application/json', 'Authorization:' . $this->generateFileName()]);

        if ('POST' === $method && !is_null($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        $response = curl_exec($ch);

        $data = json_decode($response, true);

        if ('POST' == $method && $data['code'] != 1) {
            $GLOBALS['msg'] = $data['msg'];
            return null;
        }

        return $data;
    }


    private function generateFileName()
    {
        $checksum = $this->generateChecksum('session');
        return "SSO-$this->_proxyId-$this->_token-$checksum";
    }


    private function getRequestUrl($command)
    {
        $query = [
            'command' => $command
        ];
        return $this->_ssoUrl . '?' . http_build_query($query);
    }

    public function logout()
    {
        $res = $this->curl('GET', 'logout');
        $this->_userinfo = null;
    }

    public function login($username, $password)
    {
        if (!isset($username) || !isset($password)) {
            return null;
        }

        $data = compact('username', 'password');
        $this->_userinfo = $this->curl('POST', 'login', $data);
        return $this->_userinfo;
    }
}