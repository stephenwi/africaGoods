<?php

namespace App\Normalizer;

use League\OAuth2\Client\Provider\GoogleUser;

class GoogleNormalizer extends Normalizer
{

    public function normalize($object, string $format = null, array $context = []): array
    {
        return [
            'email' => $object->getEmail(),
            'google_id' => $object->getId(),
            'type' => 'Google',
            'username' => $object->getName(),
        ];
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof GoogleUser;
    }
}