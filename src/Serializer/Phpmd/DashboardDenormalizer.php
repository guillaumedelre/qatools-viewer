<?php

namespace App\Serializer\Phpmd;

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
            && Vendors::PHPMD === $context['vendor']
            && !empty($context['action'])
            && 'dashboard' === $context['action'];
    }

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        $report = (new Report())
            ->setView('component/phpmd/dashboard.html.twig')
            ->addComponent(
                (new Component())
                    ->setName('file_list')
                    ->setLabel('mess detector')
                    ->setData($data['files'])
                    ->setOptions(
                        [
                            'class' => count($data['files']) > 0 ? 'border-danger' : 'border-success',
                            'icon'  => count($data['files']) > 0 ? 'fas fa-snowplow text-danger' : 'fas fa-snowplow text-success',
                            'file' => $context['file'],
                        ]
                    )
            )
        ;

        return $report;
    }
}
