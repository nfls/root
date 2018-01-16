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

    const DOWNLOAD_ROLE = "acs:ram::1978482396280799:role/download-users";
    const UPLOAD_ROLE = "acs:ram::1978482396280799:role/upload-users";
    const SECRET_ID = "LTAIrn88mOuMssDn";
    const SECRET_KEY = "Y5La4e2Fcvnm4z58ylKONfwI18Qobr";


    public function __construct()
    {
        $profile = DefaultProfile::getProfile("cn-hangzhou",self::SECRET_ID,self::SECRET_KEY);
        $this->client = new DefaultAcsClient($profile);
    }
    public function getDownloadListToken($id){
        $request = new AssumeRoleRequest();
        $request->setRoleSessionName($id);
        $request->setRoleArn(self::DOWNLOAD_ROLE);
        $request->setDurationSeconds(3600);
        try{
            $response = $this->client->doAction($request);
            return json_decode($response->getBody(),true)["Credentials"]["SecurityToken"];
        }catch(ClientException $e){
            return false;
        }
    }
    public function getUploadToken($id){
        $request = new AssumeRoleRequest();
        $request->setRoleSessionName($id);
        $request->setRoleArn(self::UPLOAD_ROLE);
        $request->setDurationSeconds(3600);
        try{
            $response = $this->client->doAction($request);
            return json_decode($response->getBody(),true)["Credentials"]["SecurityToken"];
        }catch(ClientException $e){
            return false;
        }
    }
}