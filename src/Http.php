<?php
namespace iapi;


/**
 * 发送请求
 */
class Http
{
    private   $url;
    protected $playload = [];


    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * 设置发送数据
     * @param $data
     * @param bool $append
     * @return $this
     */
    public function playload($data, $append = true)
    {
        if ($append) {
            $this->playload = array_merge($this->playload, $data);
        } else {
            $this->playload = $data;
        }
        return $this;
    }

    /**
     * 获取要发送的数据
     * @return array
     */
    public function getPlayload()
    {
        $data = $this->playload;
        return http_build_query($data);
    }

    /**
     * 发送get请求
     * @return \Httpful\Response
     */
    public function get()
    {
        return $this->curl($this->url,$this->getPlayload());
    }

    /**
     * 发送post请求
     *
     * @return \Httpful\Response
     */
    public function post()
    {
        return $this->curl($this->url,$this->getPlayload(),"POST");
    }

    /**
     * 发起HTTP请求
     * @param $url
     * @param null $formdata
     * @param string $method
     * @param int $timeout
     * @return mixed
     */
    public function curl($url, $formdata = null, $method = 'GET', $timeout = 10)
    {
        $method = strtoupper($method);
        $ch = curl_init();
        if ($method == "POST") {
            curl_setopt($ch, CURLOPT_POST, true);
        } elseif ($method == 'GET') {
            if($formdata){
                $url .= strpos($url ,'?') == false ? '?'.$formdata : '&'.$formdata;
            }
        } else {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Length: ' . strlen($formdata)));
        }
        if ($method != "GET" && $formdata) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $formdata);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}