<?php

namespace App\Serializer\Phploc;

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
            && 'txt' === $format
            && !empty($context)
            && !empty($context['vendor'])
            && Vendors::PHPLOC === $context['vendor']
            && !empty($context['action'])
            && 'dashboard' === $context['action'];
    }

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        $report = (new Report())
            ->setView('component/phploc/dashboard.html.twig')
            ->addComponent(
                (new Component())
                    ->setName('global_loc')
                    ->setLabel('line of code overview')
                    ->setData($data)
                    ->setOptions(
                        [
                            'icon' => 'fas fa-sitemap text-info',
                            'class' => 'border-info',
                            'file' => $context['file'],
                        ]
                    )
            )
        ;

        return $report;
    }
}
