<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * NicknameTransformer is a ...
 */
class NicknameTransformer implements DataTransformerInterface
{

    /**
     * From client to server
     *
     * @param string $value
     */
    public function reverseTransform($value)
    {
        return $value;
    }

    public function transform($value)
    {
        $value = iconv('UTF-8', 'ASCII//TRANSLIT', $value);  // warning: erratic on windows

        return preg_replace('#[^-a-z0-9]#', '-', strtolower($value));
    }

}
