<?php
namespace iapi;
use vitex\core\Exception;
use vitex\helper\Utils;

/**
 * 接口服务
 */
class ApiServer
{
    /**
     * [
     *    secret => '',//秘钥
     *    expire => '',//过期时间
     *    aeskey => ''
     * ]
     * @param $option
     */
    /**
     * @var Sign
     */
    protected $sign;
    protected $aeskey;
    protected $useAesEncode = true;//是否使用aes加密数据
    protected $options;

    private $apiurl;
    private $apidata;
    private $apimethod;


    public function __construct($option)
    {
        if (!$option['secret']) {
            throw new Exception('秘钥不得为空');
        }
        if (!$option['aeskey']) {
            $option['aeskey'] = $option['secret'] . $option . ['secret'] . $option['secret'];
        }

        $this->aeskey = $option['aeskey'];

        $this->sign = new Sign($option['secret']);
        $this->options = $option;
    }

    /**
     * 设置数据是否使用aes加密
     * @param $bool
     * @return $this
     */
    public function useAesEncode($bool)
    {
        $this->useAesEncode = $bool;
        return $this;
    }

    /**
     * 获取接口来的数据
     * @return mixed
     * @throws Exception
     */
    public function data()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if (!$this->_checkSign()) {
            throw new Exception('签名错误');
        }
        $data = $method == 'GET' ? $_GET['data'] : $_POST['data'];
        if ($this->useAesEncode && $data) {
            $data = Utils::decrypt($data, $this->aeskey);
        }
        $dataArr = json_decode($data, true);
        return $dataArr;
    }

    /**
     * 发送给接口返回值
     * @param array $data
     * @throws Exception
     */
    public function sendBack($data)
    {
        list($time, $sign) = $this->sign->get();
        $ret = [
            'timestamp' => $time,
            'sign' => $sign,
            'data' => $data
        ];
        echo json_encode($ret, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 验证签名
     * @return bool
     */
    private function _checkSign($sign = '', $timestamp = '')
    {
        $sign = $sign ?: $_GET['sign'];
        $timestamp = $timestamp ?: $_GET['timestamp'];
        $expire = false;
        if ($this->options['expire']) {
            $expire = true;
            $this->sign->setExpire($this->options['expire']);
        }
        return $this->sign->check($timestamp, $sign, $expire);
    }

    /**
     * 发送post数据
     * @param $url
     * @param array $data
     * @return $this
     */
    public function post($url, array $data = [])
    {
        $this->apiurl = $url;
        $this->apidata = $data;
        $this->apimethod = 'POST';
        return $this;
    }

    /**
     * 发送GET数据
     * @param $url
     * @param array $data
     * @return $this
     */
    public function get($url, array $data = [])
    {
        $this->apiurl = $url;
        $this->apidata = $data;
        $this->apimethod = 'GET';
        return $this;
    }


    /**
     * 发送信息
     * @throws Exception
     */
    public function send()
    {
        list($time, $sign) = $this->sign->get();
        $url = strpos($this->apiurl, '?') === false ? $this->apiurl . '?' : $this->apiurl . '&';
        $url = $url . 'timestamp=' . $time . '&sign=' . $sign;

        $http = new Http($url);
        $data = json_encode($this->apidata, JSON_UNESCAPED_UNICODE);
        if ($this->useAesEncode) {
            $data = Utils::encrypt($data, $this->aeskey);
        }
        $http->playload(['data' => $data]);
        if ($this->apimethod == 'GET') {
            $ret = $http->get();
        } else {
            $ret = $http->post();
        }
        $retArr = json_decode($ret, true);
        if (!$retArr) {
            throw new Exception('返回值无法解析');
        }
        $sign = $retArr['sign'];
        $timestamp = $retArr['timestamp'];

        if (!$this->_checkSign($sign, $timestamp)) {
            throw new Exception('签名错误,无法验证来源');
        }
        return $retArr['data'];
    }

}