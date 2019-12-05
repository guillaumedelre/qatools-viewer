<?php

namespace App\Serializer\Pdepend;

use App\Domain\Vendors;
use App\Model\Component;
use App\Model\Report;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;

class SvgDenormalizer implements ContextAwareDenormalizerInterface
{
    public function supportsDenormalization($data, string $type, string $format = null, array $context = [])
    {
        return !empty($data)
            && Report::class === $type
            && 'svg' === $format
            && !empty($context)
            && !empty($context['vendor'])
            && Vendors::PDEPEND === $context['vendor']
            && !empty($context['action'])
            && 'dashboard' === $context['action'];
    }

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        $report = (new Report())
            ->setView('component/pdepend/graphs.html.twig');

        /** @var SplFileInfo $file */
        foreach ($data as $item) {
            $report->addComponent(
                (new Component())
                    ->setName('graph_list')
                    ->setLabel($item['label'])
                    ->setData($item['data'])
            );
        }

        return $report;
    }
}
