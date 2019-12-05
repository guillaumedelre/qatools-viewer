<?php

namespace App\Serializer\Phploc;

use App\Domain\Vendors;
use Symfony\Component\Serializer\Encoder\ContextAwareDecoderInterface;

class PhplocDecoder implements ContextAwareDecoderInterface
{
    public function decode(string $data, string $format, array $context = [])
    {
        $decoded = [];

        preg_match_all(
            '/(?P<label>.+) {2,}(?P<value>(\d+(\.\d*)?|\.\d+)?)( \((?P<percent>.*)%\))?|(?P<section>.+)/im',
            $data,
            $matches
        );

        $sections = array_filter($matches['section']);
        foreach ($matches['label'] as &$match) {
            if (empty($match)) {
                $match = array_shift($sections);
            }
            $match = rtrim($match);
        }

        // start at 1 because we do not want Sebastian Bergmann signature
        for ($i = 1; $i < count($matches['label']); $i++) {
            if (empty(trim($matches['label'][$i]))
            && empty($matches['value'][$i])
            && empty($matches['percent'][$i])
            ) {
                continue;
            }

            $spaceCounter = mb_substr_count($matches['label'][$i], ' ') - (str_word_count($matches['label'][$i]) - 1);
            $level = $spaceCounter > 0 ? $spaceCounter / 2 : 0;
            $decoded[] = [
                'label'   => trim($matches['label'][$i]),
                'value'   => (int) is_numeric($matches['value'][$i]) ? (int) $matches['value'][$i] : '',
                'percent' => (float) $matches['percent'][$i],
                'level'   => $level,
            ];
        }

        return $decoded;
    }

    public function supportsDecoding(string $format, array $context = [])
    {
        return 'txt' === $format
            && !empty($context)
            && !empty($context['vendor'])
            && Vendors::PHPLOC === $context['vendor'];
    }
}
