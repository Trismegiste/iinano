<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Form\Oauth;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * KeyPairTransformer is a ...
 */
class KeyPairTransformer implements DataTransformerInterface
{

    public function reverseTransform($value)
    {
        if (empty($value['client_id']) && empty($value['secret_id'])) {
            return null;
        }

        return $value;
    }

    public function transform($value)
    {
        return $value;
    }

}
