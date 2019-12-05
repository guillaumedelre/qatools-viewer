<?php

namespace App\Serializer\Phpcpd;

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
            && Vendors::PHPCPD === $context['vendor']
            && !empty($context['action'])
            && 'dashboard' === $context['action'];
    }

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        $report = (new Report())
            ->setView('component/phpcpd/dashboard.html.twig')
            ->addComponent(
                (new Component())
                    ->setName('global_cpd')
                    ->setLabel('copy paste detector')
                    ->setData($data)
                    ->setOptions(
                        [
                            'icon' => 'fas fa-clone text-' . (!isset($data['cpd']) ? 'success' : 'danger'),
                            'class' => !isset($data['cpd']) ? 'border-success' : 'border-danger',
                            'file' => $context['file'],
                        ]
                    )
            )
        ;

        return $report;
    }
}
