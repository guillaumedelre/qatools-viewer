<?php

namespace App\Serializer\Lint;

use App\Domain\Vendors;
use App\Model\Component;
use App\Model\Report;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;

class YamlDenormalizer implements ContextAwareDenormalizerInterface
{
    public function supportsDenormalization($data, string $type, string $format = null, array $context = [])
    {
        return Report::class === $type
            && 'json' === $format
            && !empty($context)
            && !empty($context['vendor'])
            && Vendors::LINT === $context['vendor']
            && !empty($context['action'])
            && 'yaml' === $context['action'];
    }

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        $errors = array_filter($data, function ($item) {return false === $item['valid'];});
        $report = (new Report())
            ->setView('component/lint/yaml.html.twig')
            ->addComponent(
                (new Component())
                    ->setName('lint_yaml')
                    ->setLabel('lint yaml')
                    ->setData($data)
                    ->setOptions(
                        [
                            'class' => empty($errors) ? 'border-success' : 'border-danger',
                            'icon' => empty($errors) ? 'fas fa-spell-check text-success' : 'fas fa-spell-check text-danger',
                            'file' => $context['file'],
                        ]
                    )
            );

        return $report;
    }
}
