<?php

namespace App\Serializer\Pdepend;

use App\Domain\Vendors;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Serializer\Encoder\ContextAwareDecoderInterface;

class FinderDecoder implements ContextAwareDecoderInterface
{
    public function decode(string $data, string $format, array $context = [])
    {
        $decoded = [];

        /** @var Finder $finder */
        $finder = unserialize($data);

        /** @var SplFileInfo $file */
        foreach ($finder->files() as $file) {
            $decoded[] = [
                'label' => $file->getFilenameWithoutExtension(),
                'data' => $file->getContents(),
            ];
        }

        return $decoded;
    }

    public function supportsDecoding(string $format, array $context = [])
    {
        return 'svg' === $format
            && !empty($context)
            && !empty($context['vendor'])
            && Vendors::PDEPEND === $context['vendor'];
    }
}
