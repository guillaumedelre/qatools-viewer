<?php

namespace App\EventListener;

use App\Domain\Vendors;
use App\Service\ReportBuilder;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

class ReportListener implements SerializerAwareInterface
{
    use SerializerAwareTrait;

    public const ATTR_REPORTS = '_reports';

    /** @var ReportBuilder */
    protected $reportBuilder;

    /** @var SessionInterface */
    protected $session;

    public function __construct(ReportBuilder $reportBuilder, SessionInterface $session)
    {
        $this->reportBuilder = $reportBuilder;
        $this->session = $session;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        if (!$request->attributes->has('vendor')
            || !$request->attributes->has('action')
            || !in_array($request->attributes->get('vendor'), Vendors::toStaticArray())
        ) {
            return;
        }

        try {
            $reports = $this->reportBuilder->build(
                $request->attributes->get('vendor'),
                $request->attributes->get('action')
            );
            $request->attributes->set(self::ATTR_REPORTS, $reports);
        } catch (\Exception $e) {
            /** @var FlashBagInterface $flashBag */
            $flashBag = $this->session->getFlashBag();
            $flashBag->add(
                'danger',
                [
                    'heading' => 'Reports build failed',
                    'icon'    => 'fas fa-exclamation-circle',
                    'message' => $e->getMessage(),
                    'trace'   => $e->getTraceAsString(),
                ]
            );
        }
    }
}
