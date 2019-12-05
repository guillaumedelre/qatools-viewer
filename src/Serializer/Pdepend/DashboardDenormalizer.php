<?php

namespace App\Serializer\Pdepend;

use App\Domain\Vendors;
use App\Model\Component;
use App\Model\Report;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;

class DashboardDenormalizer implements ContextAwareDenormalizerInterface
{
    public function supportsDenormalization($data, string $type, string $format = null, array $context = [])
    {
        return !empty($data) && Report::class === $type && 'xml' === $format && !empty($context) && !empty($context['vendor']) && Vendors::PDEPEND === $context['vendor'] && !empty($context['action']) && 'dashboard' === $context['action'];
    }

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        $report = (new Report())
            ->setView('component/pdepend/dashboard.html.twig')
            ->addComponent(
                (new Component())
                    ->setName('file_list')
                    ->setLabel('Files')
                    ->setData($data['files']['file'])
                    ->setOptions(
                        [
                            'icon' => 'fas fa-folder text-info',
                            'class' => 'border-info',
                            'file' => $context['file'],
                        ]
                    )
            )
            ->addComponent(
                (new Component())
                    ->setName('package_list')
                    ->setLabel('Packages')
                    ->setData($data['package'])
                    ->setOptions(
                        [
                            'icon' => 'fas fa-box text-info',
                            'class' => 'border-info',
                            'file' => $context['file'],
                        ]
                    )
            )
        ;

        return $report;
    }
}
