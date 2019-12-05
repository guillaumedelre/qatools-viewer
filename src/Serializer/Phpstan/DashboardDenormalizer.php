<?php

namespace App\Serializer\Phpstan;

use App\Domain\Vendors;
use App\Model\Component;
use App\Model\Report;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;

class DashboardDenormalizer implements ContextAwareDenormalizerInterface
{
    public function supportsDenormalization($data, string $type, string $format = null, array $context = [])
    {
        return !empty($data)
            && Report::class === $type
            && 'json' === $format
            && !empty($context)
            && !empty($context['vendor'])
            && Vendors::PHPSTAN === $context['vendor']
            && !empty($context['action'])
            && 'dashboard' === $context['action'];
    }

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        $report = (new Report())
            ->setView('component/phpstan/dashboard.html.twig')
            ->addComponent(
                (new Component())
                    ->setName('error_list')
                    ->setLabel('static analysis errors')
                    ->setData($data['errors'])
                    ->setOptions(
                        [
                            'class' => count($data['errors']) > 0 ? 'border-danger' : 'border-success',
                            'icon' => count($data['errors']) > 0 ? 'fas fa-cogs text-danger' : 'fas fa-cogs text-success',
                            'file' => $context['file'],
                        ]
                    )
            )
            ->addComponent(
                (new Component())
                    ->setName('file_list')
                    ->setLabel('static analysis file errors')
                    ->setData($data['files'])
                    ->setOptions(
                        [
                            'class' => count($data['files']) > 0 ? 'border-danger' : 'border-success',
                            'icon' => count($data['files']) > 0 ? 'fas fa-cogs text-danger' : 'fas fa-cogs text-success',
                            'file' => $context['file'],
                        ]
                    )
            );

        return $report;
    }
}
