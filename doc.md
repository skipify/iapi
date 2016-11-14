接口验证程序

签名：



生成规则  md5($timestamp.$secret)



数据加密： AES 256加密

使用方法：

请求get接口

```
//初始化apiserver
$apiServer = new ApiServer([
  "secret" => "asdasdasd8AJDB",//签名秘钥
  "aeskey" => "HSG7687Hjhujkg8768876JBSDGKSGFT75765hagvjdhavsd",//不小于32位的AES加密秘钥
  "expire" => "10" //过期时间，单位为秒
]);

发送get请求

$ret = $apiServer->get("url",["username"=>name])->send(); // 此处如果有需要传递给接口的数据尽量使用get方法的第二个参数来指定，这样数据会被加密安全性高
//$ret 为接口返回值 
```

获取上述请求的数据

```
//初始化apiserver
$apiServer = new ApiServer([
  "secret" => "asdasdasd8AJDB",//签名秘钥
  "aeskey" => "HSG7687Hjhujkg8768876JBSDGKSGFT75765hagvjdhavsd",//不小于32位的AES加密秘钥
  "expire" => "10" //过期时间，单位为秒
]);
try{
	$data = $apiServer->data();
} catch(Exception $e){
  //异常情况
  echo $e->getMessage();
}

//给接口发送返回值
$apiServer->sendBack([
  "status" => 1,
  "msg" => "数据接收成功"
]);
```





请求post接口

```
//初始化apiserver
$apiServer = new ApiServer([
  "secret" => "asdasdasd8AJDB",//签名秘钥
  "aeskey" => "HSG7687Hjhujkg8768876JBSDGKSGFT75765hagvjdhavsd",//不小于32位的AES加密秘钥
  "expire" => "10" //过期时间，单位为秒
]);

发送get请求

$ret = $apiServer->post("url",["username"=>name])->send();
//$ret 为接口返回值 
```

获取上述请求的数据

```
//初始化apiserver
$apiServer = new ApiServer([
  "secret" => "asdasdasd8AJDB",//签名秘钥
  "aeskey" => "HSG7687Hjhujkg8768876JBSDGKSGFT75765hagvjdhavsd",//不小于32位的AES加密秘钥
  "expire" => "10" //过期时间，单位为秒
]);
try{
	$data = $apiServer->data();
} catch(Exception $e){
  //异常情况
  echo $e->getMessage();
}

//给接口发送返回值
$apiServer->sendBack([
  "status" => 1,
  "msg" => "数据接收成功"
]);
```

