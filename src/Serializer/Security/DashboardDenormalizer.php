<?php

namespace App\Serializer\Security;

use App\Domain\Vendors;
use App\Model\Component;
use App\Model\Report;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;

class DashboardDenormalizer implements ContextAwareDenormalizerInterface
{
    public function supportsDenormalization($data, string $type, string $format = null, array $context = [])
    {
        return Report::class === $type
            && 'json' === $format
            && !empty($context)
            && !empty($context['vendor'])
            && Vendors::SECURITY === $context['vendor']
            && !empty($context['action'])
            && 'dashboard' === $context['action'];
    }

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        $report = (new Report())
            ->setView('component/security/dashboard.html.twig')
            ->addComponent(
                (new Component())
                    ->setName('security_check')
                    ->setLabel('security check')
                    ->setData($data)
                    ->setOptions(
                        [
                            'class' => empty($data) ? 'border-success' : 'border-danger',
                            'icon' => empty($data) ? 'fas fa-shield-alt text-success' : 'fas fa-shield-alt text-danger',
                            'file' => $context['file'],
                        ]
                    )
            );

        return $report;
    }
}
