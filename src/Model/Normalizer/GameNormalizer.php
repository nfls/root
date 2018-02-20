<?php

namespace App\Model\Normalizer;

use App\Entity\Game\Game;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\scalar;

class GameNormalizer implements NormalizerInterface
{
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
            "title" => $object->getTitle(),
            "thumb" => $object->getThumb(),
            "subTitle" => $object->getSubtitle()
        );
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Game;
    }

}