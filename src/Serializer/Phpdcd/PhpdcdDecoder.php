<?php

namespace App\Serializer\Phpdcd;

use App\Domain\Vendors;
use Symfony\Component\Serializer\Encoder\ContextAwareDecoderInterface;

class PhpdcdDecoder implements ContextAwareDecoderInterface
{
    public function decode(string $data, string $format, array $context = [])
    {
        $decoded = [];

        preg_match_all(
            '/ {2,}- (?P<method>(.*))\n {4,}LOC: \d+, declared in \/project\/(?P<filename>(.*)):(?P<line>(\d+))/im',
            $data,
            $matches
        );

        if (!empty($matches['filename'])) {
            // start at 1 because we do not want Sebastian Bergmann signature
            for ($i = 1; $i < count($matches['filename']); $i++) {
                if (empty(trim($matches['filename'][$i]))
                    && empty($matches['method'][$i])
                    && empty($matches['line'][$i])
                ) {
                    continue;
                }

                $decoded[trim($matches['filename'][$i])][] = [
                    'method'   => $matches['method'][$i],
                    'line' => $matches['line'][$i],
                ];
            }
        }

        return $decoded;
    }

    public function supportsDecoding(string $format, array $context = [])
    {
        return 'txt' === $format
            && !empty($context)
            && !empty($context['vendor'])
            && Vendors::PHPDCD === $context['vendor'];
    }
}
