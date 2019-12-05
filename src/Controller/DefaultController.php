<?php

namespace App\Controller;

use App\EventListener\MenuListener;
use App\EventListener\ReportListener;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class DefaultController extends BaseController
{
    /**
     * @Route("/", name="default_index")
     */
    public function index(Request $request)
    {
        return $this->render(
            'default/index.html.twig',
            [
                'menu' => $request->attributes->get(MenuListener::ATTR_MENU)
            ]
        );
    }

    /**
     * @Route("/{vendor}/{action}", name="default_action")
     */
    public function action(Request $request, string $vendor, string $action)
    {
        $items = [];
        $reports = [];
        /** @var ParameterBag $reportBag */
        $reportBag = $request->attributes->get(ReportListener::ATTR_REPORTS);
        if (!empty($reportBag)) {
            $reports = $reportBag->all();
        }

        foreach ($reports as $reportFilename => $reportObject) {
            try {
                $items[] = $this->get('serializer')->serialize($reportObject, 'html');
            } catch (ExceptionInterface $e) {
                /** @var FlashBagInterface $flashBag */
                $flashBag = $this->get('session')->getFlashBag();
                $flashBag->add(
                    'danger',
                    [
                        'heading' => 'Reports html conversion failed',
                        'icon'    => 'fas fa-exclamation-circle',
                        'message' => $e->getMessage(),
                        'trace'   => $e->getTraceAsString(),
                    ]
                );
            }
        }

        return $this->render(
            'default/index.html.twig',
            [
                'items' => $items,
                'menu' => $request->attributes->get(MenuListener::ATTR_MENU),
            ]
        );
    }
}
