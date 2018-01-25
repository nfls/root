<?php

namespace App\Model\Normalizer;

use App\Entity\Media\Photo;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\scalar;

class PhotoNormalizer implements NormalizerInterface{
    /**
     * @param Photo $object
     * @param null $format
     * @param array $context
     * @return array
     */
    public function normalize($object, $format = null, array $context = array())
    {
        return array(
            "id" => $object->getId(),
            "msrc" => $object->getThumb(),
            "src" => $object->getHd(),
            "osrc" => $object->getOrigin(),
            "h" => $object->getHeight(),
            "w" => $object->getWidth()
        );
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Photo;
    }

}