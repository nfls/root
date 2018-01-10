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
    function responseRowEntity($data,$count,$code){
        $encoder = new JsonEncoder();
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(2);
        $normalizer->setCircularReferenceHandler(function ($object) {
            return null;
        });
        $serializer = new Serializer([$normalizer],[$encoder]);
        $data = json_decode($serializer->serialize($data,"json"),TRUE);
        $json = new JsonResponse();
        //$array = array("code"=>$code,"data"=>$data);
        $json->setData(array("rows"=>$data,"total"=>$count));
        return $json;
    }
    function responseEntity($data,$code = 200){
        $encoder = new JsonEncoder();
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(2);
        $normalizer->setCircularReferenceHandler(function ($object) {
            return null;
        });
        $serializer = new Serializer([$normalizer],[$encoder]);
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