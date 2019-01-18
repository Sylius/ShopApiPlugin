<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class PutItemToCartRequest implements CommandRequestInterface
{
    /** @var boolean */
    protected $hasVariantCode;

    /** @var boolean */
    protected $hasOptions;

    /** @var Request */
    private $request;

    public function populateData(Request $request): void
    {
        $this->hasVariantCode = $request->request->has('variantCode');
        $this->hasOptions = $request->request->has('options');
        $this->request = $request;
    }

    public function getCommand(): object
    {
        if (!$this->hasVariantCode && !$this->hasOptions) {
            return PutSimpleItemToCartRequest::fromRequest($this->request)->getCommand();
        }

        if ($this->hasVariantCode && !$this->hasOptions) {
            return PutVariantBasedConfigurableItemToCartRequest::fromRequest($this->request)->getCommand();
        }

        if (!$this->hasVariantCode && $this->hasOptions) {
            return PutOptionBasedConfigurableItemToCartRequest::fromRequest($this->request)->getCommand();
        }

        throw new NotFoundHttpException('Variant not found for given configuration');
    }
}
