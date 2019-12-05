<?php

namespace App\EventListener;

use App\Service\MenuBuilder;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class MenuListener
{
    public const ATTR_MENU = '_menu';

    /** @var MenuBuilder */
    protected $menuBuilder;

    public function __construct(MenuBuilder $menuBuilder)
    {
        $this->menuBuilder = $menuBuilder;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        $request->attributes->set(self::ATTR_MENU, $this->menuBuilder->build($request));
    }
}
