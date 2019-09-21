<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Product;

use Pagerfanta\Pagerfanta;
use Sylius\ShopApiPlugin\View\Product\PageView;
use Symfony\Component\Routing\RouterInterface;

final class PageViewFactory implements PageViewFactoryInterface
{
    /** @var RouterInterface */
    private $router;

    /** @var string */
    private $pageViewClass;

    /** @var string */
    private $pageLinksViewClass;

    public function __construct(
        RouterInterface $router,
        string $pageViewClass,
        string $pageLinksViewClass
    ) {
        $this->router = $router;
        $this->pageViewClass = $pageViewClass;
        $this->pageLinksViewClass = $pageLinksViewClass;
    }

    public function create(Pagerfanta $pagerfanta, string $route, array $parameters): PageView
    {
        /** @var PageView $page */
        $page = new $this->pageViewClass();
        $page->page = $pagerfanta->getCurrentPage();
        $page->limit = $pagerfanta->getMaxPerPage();
        $page->pages = $pagerfanta->getNbPages();
        $page->total = $pagerfanta->getNbResults();
        $page->links = new $this->pageLinksViewClass();
        $page->links->self = $this->router->generate($route, array_merge($parameters, [
            'page' => $pagerfanta->getCurrentPage(),
            'limit' => $pagerfanta->getMaxPerPage(),
        ]));
        $page->links->first = $this->router->generate($route, array_merge($parameters, [
            'page' => 1,
            'limit' => $pagerfanta->getMaxPerPage(),
        ]));
        $page->links->last = $this->router->generate($route, array_merge($parameters, [
            'page' => $pagerfanta->getNbPages(),
            'limit' => $pagerfanta->getMaxPerPage(),
        ]));
        $page->links->next = $this->router->generate($route, array_merge($parameters, [
            'page' => ($pagerfanta->getCurrentPage() < $pagerfanta->getNbPages()) ? $pagerfanta->getCurrentPage() + 1 : $pagerfanta->getCurrentPage(),
            'limit' => $pagerfanta->getMaxPerPage(),
        ]));

        return $page;
    }
}
