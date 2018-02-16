<?php
namespace App\Model\Normalizer;

use App\Entity\User\User;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\scalar;

class UserNormalizer implements NormalizerInterface{

    private $realname;

    /**
     * UserNormalizer constructor.
     * @param $realname boolean
     */
    public function __construct(bool $realname = false)
    {
        $this->realname = $realname;
    }



    /**
     * @param User $object
     * @param null $format
     * @param array $context
     * @return array
     */
    public function normalize($object, $format = null, array $context = array())
    {
        /** @var $object User */
        $info = array(
            "id" => $object->getId(),
            "username" => $object->getUsername(),
            "point" => $object->getPoint(),
            "admin" => $object->isAdmin(),
            "verified" => $object->isVerified()
        );
        if($this->realname){
            $info["htmlUsername"] = $object->htmlUsername ?? $info["username"];
        }
        if($info["admin"]){
            $info["htmlUsername"] = "<span style='background-color: #F57EB6'>管理员</span>&nbsp;".$info["htmlUsername"];
        }
        return $info;

    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof User;
    }

}