<?php

namespace App\Serializer;

use Symfony\Component\Serializer\Encoder\ContextAwareEncoderInterface;

class HtmlEncoder implements ContextAwareEncoderInterface
{
    public function encode($data, string $format, array $context = [])
    {
        return $data;
    }

    public function supportsEncoding(string $format, array $context = [])
    {
        return 'html' === $format;
    }
}
