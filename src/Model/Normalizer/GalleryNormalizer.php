<?php
/**
 * Created by PhpStorm.
 * User: hqy
 * Date: 04/03/2018
 * Time: 6:55 PM
 */

namespace App\Model\Normalizer;


use App\Entity\Media\Gallery;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\scalar;

class GalleryNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = array())
    {
        $photoNormalizer = new PhotoNormalizer();
        $timeNormalizer = new DateTimeNormalizer();
        /** @var Gallery $object */
        $return = array(
            "id"=>$object->getId(),
            "title"=>$object->getTitle(),
            "description"=>$object->getDescription(),
            "originCount"=>$object->getOriginCount(),
            "photoCount"=>$object->getPhotoCount(),
            "visible"=>$object->isVisible(),
            "public"=>$object->isPublic(),
            "time"=>$timeNormalizer->normalize($object->getTime())
        );
        if(is_null($object->getCover()))
            $return["cover"] = null;
        else
            $return["cover"] = $photoNormalizer->normalize($object->getCover());
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Gallery;
    }

}