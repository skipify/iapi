<?php
namespace iapi;
use vitex\core\Exception;

/**
 * 生成签名
 */


class Sign
{
    /**
     * 秘钥
     * @var string
     */
    private $secret;
    /**
     * 签名有效期
     * @var int
     */
    private $expire = 30;

    public function __construct($secret)
    {
        $this->secret = $secret;
    }

    /**
     * 设置签名过期时间,单位为秒
     * @param $expire int 秒
     * @return $this
     */
    public function setExpire($expire){
        $this->expire = intval($expire);
        return $this;
    }
    /**
     * 获取签名
     * 签名的规则:
     * 2. 取当前时间戳 time
     * 3. 取得秘钥 secret
     * 4. md5(time.secret)
     * @return array
     * @throws Exception
     */
    public function get($time = 0)
    {
        if($this->secret == ''){
            throw new Exception("秘钥不得为空");
        }
        $time = $time ? : time();
        $sign = md5($time.$this->secret);
        return [$time,$sign];
    }

    /**
     * 验证签名是否正确
     * @param $time
     * @param $sign
     * @param bool $expire
     * @return bool
     * @throws Exception
     */
    public function check($time,$sign,$expire=true)
    {
        list($t,$checkSign) = $this->get($time);
        if($sign != $checkSign){
            return false;
        }
        if($expire && (time() - $time) > $this->expire){
            return false;
        }
        return true;
    }
}