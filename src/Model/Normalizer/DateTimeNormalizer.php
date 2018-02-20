<?php

namespace App\Model\Normalizer;


use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\scalar;

class DateTimeNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = array())
    {
        return $object->format(\DateTime::ATOM);
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof \DateTime;
    }

}