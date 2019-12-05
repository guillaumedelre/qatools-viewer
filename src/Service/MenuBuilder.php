<?php

namespace App\Service;

use App\Model\MenuItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class MenuBuilder
{
    /** @var RouterInterface */
    private $router;

    /** @var array */
    private $collection = [];

    public function __construct(RouterInterface $router, array $menuCollection)
    {
        $this->router = $router;
        $this->collection = $menuCollection;
    }

    public function build(Request $request, array $collection = null): array
    {
        $menu = [];
        $data = null === $collection ? $this->collection : $collection;
        foreach ($data as $item) {
            $menu[] = (new MenuItem())
                ->setLabel($item['label'])
                ->setIcon($item['icon'] ?? null)
                ->setUrl(
                    $this->router->generate($item['route'], $item['parameters'] ?? [])
                )
                ->setEnabled($item['enabled'])
                ->setActive($this->isActive($request, $item) || $this->hasActiveChildren($request, $item))
                ->setChildren(
                    $this->build($request, $item['children'] ?? [])
                )
            ;
        }

        return $menu;
    }

    private function isActive(Request $request, array $item): bool
    {
        foreach ($item['parameters'] as $key => $value) {
            if ($value !== $request->attributes->get($key)) {
                return false;
            }
        }

        return true;
    }

    private function hasActiveChildren(Request $request, array $item): bool
    {
        foreach ($item['children'] ?? [] as $child) {
            if ($this->isActive($request, $child)) {
                return true;
            }
        }

        return false;
    }
}
