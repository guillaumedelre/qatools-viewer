<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class CodeExtension extends AbstractExtension
{
    protected $kernelProjectDir;

    public function __construct(string $kernelProjectDir)
    {
        $this->kernelProjectDir = $kernelProjectDir;
    }

    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('extractCode', [$this, 'extractCode']),
            new TwigFilter('prettify', [$this, 'prettify']),
            new TwigFilter('extractFileExtension', [$this, 'extractFileExtension']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('extractCode', [$this, 'extractCode']),
            new TwigFunction('prettify', [$this, 'prettify']),
            new TwigFunction('extractFileExtension', [$this, 'extractFileExtension']),
        ];
    }

    public function extractFileExtension(string $filename)
    {
        if (empty($filename)) {
            return '';
        }

        $filepath = realpath("{$this->kernelProjectDir}/$filename");

        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }

    public function extractCode(string $filename, int $line, int $offset)
    {
        if (empty($filename)) {
            return '';
        }

        $filepath = realpath("{$this->kernelProjectDir}/$filename");

        return implode(
            "\n",
            array_slice(
                explode("\n", file_get_contents($filepath)),
                $line - $offset,
                2 * $offset
            )
        );
    }

    public function prettify(string $filename)
    {
        $start = strpos($filename, 'src');
        $filename = substr($filename, $start, strlen($filename));
        $filepath = realpath("{$this->kernelProjectDir}/$filename");

        if (!$filepath) {
            return "Oups, file not found:
            $filename";
        }

        return file_get_contents($filepath);
    }
}
