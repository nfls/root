<?php
namespace App\Model;
use App\Model\Normalizer\UserNormalizer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ApiResponse {
    private $serializer;
    private $userSerializer;
    public function __construct()
    {
        $encoder = new JsonEncoder();
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(2);
        $normalizer->setCircularReferenceHandler(function ($object) {
            return null;
        });
        $dateNormalizer = new DateTimeNormalizer();
        $userNormalizer = new UserNormalizer();
        $this->serializer = new Serializer([$dateNormalizer,$userNormalizer,$normalizer],[$encoder]);
        $this->userSerializer = new Serializer([$dateNormalizer,$normalizer],[$encoder]);
    }

    public function response($data,$code = 200){
        $json = new JsonResponse();
        $array = array("code"=>$code,"data"=>$data);
        $json->setData($array);
        return $json;
    }
    public function responseRowEntity($data,$count,$code){
        $data = json_decode($this->serializer->serialize($data,"json"),TRUE);
        $json = new JsonResponse();
        //$array = array("code"=>$code,"data"=>$data);
        $json->setData(array("rows"=>$data,"total"=>$count));
        return $json;
    }
    function responseEntity($data,$code = 200){
        $data = json_decode($this->serializer->serialize($data,"json"),TRUE);
        $json = new JsonResponse();
        $array = array("code"=>$code,"data"=>$data);
        $json->setData($array);
        return $json;
    }
    function responseUser($data,$code = 200){
        $data = json_decode($this->userSerializer->serialize($data,"json"),TRUE);
        $json = new JsonResponse();
        $array = array("code"=>$code,"data"=>$data);
        $json->setData($array);
        return $json;
    }

}