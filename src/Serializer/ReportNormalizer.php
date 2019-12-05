<?php

namespace App\Serializer;

use App\Model\Report;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Twig\Environment;

class ReportNormalizer implements ContextAwareNormalizerInterface
{
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return $data instanceof Report;
    }

    /**
     * @param Report      $object
     * @param string|null $format
     * @param array       $context
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        return $this->twig->render($object->getView(), ['components' => $object->getComponents()]);
    }

}
