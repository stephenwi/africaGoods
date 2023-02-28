<?php

namespace App\Normalizer;

use League\OAuth2\Client\Provider\GithubResourceOwner;

class GithubNormalizer extends Normalizer
{

    public function normalize($object, string $format = null, array $context = []): array
    {
        return [
            'email' => $object->getEmail(),
            'github_id' => $object->getId(),
            'type' => 'Github',
            'username' => $object->getNickname(),
        ];
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof GithubResourceOwner;
    }
}