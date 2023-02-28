<?php

namespace App\Normalizer;

use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

abstract class Normalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }

    abstract public function normalize($object, string $format = null, array $context = []): array;

    abstract public function supportsNormalization($data, string $format = null, array $context = []):bool;
}