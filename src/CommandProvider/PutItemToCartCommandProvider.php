<?php

/**
 * This file is part of the Sylius package.
 *
 *  (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\CommandProvider;

use Sylius\ShopApiPlugin\Command\CommandInterface;
use Sylius\ShopApiPlugin\Request\Cart\PutOptionBasedConfigurableItemToCartRequest;
use Sylius\ShopApiPlugin\Request\Cart\PutSimpleItemToCartRequest;
use Sylius\ShopApiPlugin\Request\Cart\PutVariantBasedConfigurableItemToCartRequest;
use Sylius\ShopApiPlugin\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PutItemToCartCommandProvider implements CommandProviderInterface
{
    /** @var ValidatorInterface */
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(Request $httpRequest, array $constraints = null, array $groups = null): ConstraintViolationListInterface
    {
        return $this->validator->validate($this->transformHttpRequest($httpRequest));
    }

    public function getCommand(Request $httpRequest): CommandInterface
    {
        return $this->transformHttpRequest($httpRequest)->getCommand();
    }

    private function transformHttpRequest(Request $httpRequest): RequestInterface
    {
        $hasVariantCode = $httpRequest->request->has('variantCode');
        $hasOptionCode = $httpRequest->request->has('options');

        if (!$hasVariantCode && !$hasOptionCode) {
            return PutSimpleItemToCartRequest::fromHttpRequest($httpRequest);
        }

        if ($hasVariantCode && !$hasOptionCode) {
            return PutVariantBasedConfigurableItemToCartRequest::fromHttpRequest($httpRequest);
        }

        if (!$hasVariantCode && $hasOptionCode) {
            return PutOptionBasedConfigurableItemToCartRequest::fromHttpRequest($httpRequest);
        }

        throw new NotFoundHttpException('Variant not found for given configuration');
    }
}
