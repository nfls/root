<?php
namespace App\Model;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ApiResponse {
    function response($data,$code = 200){
        $json = new JsonResponse();
        $array = array("code"=>$code,"data"=>$data);
        $json->setData($array);
        return $json;
    }
    function responseEntity($data,$code = 200){
        $encoder = new JsonEncoder();
        $nomalizer = new ObjectNormalizer();
        $nomalizer->setCircularReferenceLimit(2);
        $serializer = new Serializer([$nomalizer],[$encoder]);
        $data = json_decode($serializer->serialize($data,"json"),TRUE);
        $json = new JsonResponse();
        $array = array("code"=>$code,"data"=>$data);
        $json->setData($array);
        return $json;
    }
    function isSerializable ($value) {
        $return = true;
        $arr = array($value);

        array_walk_recursive($arr, function ($element) use (&$return) {
            if (is_object($element) && get_class($element) == 'Closure') {
                $return = false;
            }
        });

        return $return;
    }

}