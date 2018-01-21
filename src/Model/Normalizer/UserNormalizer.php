<?php
namespace App\Model\Normalizer;

use App\Entity\User\User;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\scalar;

class UserNormalizer implements NormalizerInterface{
    /**
     * @param User $object
     * @param null $format
     * @param array $context
     * @return array
     */
    public function normalize($object, $format = null, array $context = array())
    {
        return array(
            "id" => $object->getId(),
            "username" => $object->getUsername(),
            "point" => $object->getPoint(),
            "admin" => false
        );
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof User;
    }

}