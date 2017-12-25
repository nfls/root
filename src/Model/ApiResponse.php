<?php
namespace App\Model;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiResponse {
    function response($data,$code = 200){
        $json = new JsonResponse();
        $array = array("code"=>$code,"data"=>$data);
        $json->setData($array);
        return $json;
    }
}