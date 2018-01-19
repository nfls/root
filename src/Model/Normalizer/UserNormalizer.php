<?php
namespace App\Model\Normalizer;

use App\Entity\User\User;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\scalar;

class UserNormalizer implements NormalizerInterface{
    public function normalize($object, $format = null, array $context = array())
    {
        return array(
            "id" => $object->getId(),
            "username" => $object->getUsername()
        );
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof User;
    }

}