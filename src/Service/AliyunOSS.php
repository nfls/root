<?php
namespace App\Service;
//use AliyunSts\Core;

use Nexmo\Client\Exception\Exception;
use Sts\AssumeRoleRequest;
use Sts\Core\AcsRequest;
use Sts\Core\DefaultAcsClient;
use Sts\Core\Exception\ClientException;
use Sts\Core\Profile\DefaultProfile;

class AliyunOSS {
    /**
     * @var DefaultAcsClient
     */
    private $client;

    const DOWNLOAD_ROLE = "acs:ram::1978482396280799:role/past-papers";
    const UPLOAD_ROLE = "acs:ram::1978482396280799:role/upload-users";
    const HOST = "http://nfls-public.oss-cn-shanghai.aliyuncs.com";
    const UPLOAD_DIR = "uploads/";

    private $key_id;
    private $key_secret;


    public function __construct()
    {
        $this->key_id = $_SERVER["ALIYUN_KEY_ID"];
        $this->key_secret = $_SERVER["ALIYUN_KEY_SECRET"];
        $profile = DefaultProfile::getProfile("cn-hangzhou",$this->key_id,$this->key_secret);
        $this->client = new DefaultAcsClient($profile);
    }
    public function getDownloadListToken($id){
        $request = new AssumeRoleRequest();
        $request->setRoleSessionName($id);
        $request->setRoleArn(self::DOWNLOAD_ROLE);
        $request->setDurationSeconds(3600);
        try{
            $response = $this->client->doAction($request);
            return json_decode($response->getBody(),true)["Credentials"];
        }catch(ClientException $e){
            return false;
        }
    }
    public function getUploadToken(){
        $request = new AssumeRoleRequest();
        $request->setRoleSessionName("api");
        $request->setRoleArn(self::UPLOAD_ROLE);
        $request->setDurationSeconds(3600);
        try{
            $response = $this->client->doAction($request);
            return json_decode($response->getBody(),true)["Credentials"];
        }catch(ClientException $e){
            return false;
        }
    }
    public function getSignature(){
        $token = $this->getUploadToken();
        $now = time();
        $expire = 30; //设置该policy超时时间是10s. 即这个policy过了这个有效时间，将不能访问
        $end = $now + $expire;
        $expiration = $this->gmt_iso8601($end);

        $condition = array(0=>'content-length-range', 1=>0, 2=>1048576000);
        $conditions[] = $condition;

        $start = array(0=>'starts-with', 1=>'$key', 2=>self::UPLOAD_DIR);
        $conditions[] = $start;

        $arr = array('expiration'=>$expiration,'conditions'=>$conditions);
        $policy = json_encode($arr);
        $base64_policy = base64_encode($policy);
        $string_to_sign = $base64_policy;
        $signature = base64_encode(hash_hmac('sha1', $string_to_sign, $this->key_secret, true));

        $response = array();
        $response['accessid'] = $this->key_id;
        $response['host'] = self::HOST;
        $response['policy'] = $base64_policy;
        $response['signature'] = $signature;
        $response['expire'] = $end;
        $response['dir'] = self::UPLOAD_DIR;
        return $response;
    }

    private function gmt_iso8601($time) {
        $dtStr = date("c", $time);
        $mydatetime = new \DateTime($dtStr);
        $expiration = $mydatetime->format(\DateTime::ISO8601);
        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        return $expiration."Z";
    }
}