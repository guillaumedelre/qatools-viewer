<?php

namespace App\Serializer\Phpdcd;

use App\Domain\Vendors;
use App\Model\Component;
use App\Model\Report;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;

class DashboardDenormalizer implements ContextAwareDenormalizerInterface
{
    public function supportsDenormalization($data, string $type, string $format = null, array $context = [])
    {
        return Report::class === $type
            && 'txt' === $format
            && !empty($context)
            && !empty($context['vendor'])
            && Vendors::PHPDCD === $context['vendor']
            && !empty($context['action'])
            && 'dashboard' === $context['action'];
    }

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        $report = (new Report())
            ->setView('component/phpdcd/dashboard.html.twig')
            ->addComponent(
                (new Component())
                    ->setName('global_dcd')
                    ->setLabel('dead code detector')
                    ->setData($data)
                    ->setOptions(
                        [
                            'icon' => 'fas fa-skull text-' . (empty($data) ? 'success' : 'danger'),
                            'class' => empty($data) ? 'border-success' : 'border-danger',
                            'file' => $context['file'],
                        ]
                    )
            )
        ;

        return $report;
    }
}
