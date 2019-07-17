<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\CommandProvider;

use Sylius\ShopApiPlugin\Command\Cart\AddCoupon;
use Sylius\ShopApiPlugin\Request\Cart\AddCouponRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AddCouponCommandProvider implements CommandProviderInterface
{
    /** @var ValidatorInterface */
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }
    protected function transformRequest(Request $request): AddCouponRequest
    {
        return new AddCouponRequest($request);
    }

    protected function transformCommand(AddCouponRequest $addCouponRequest): AddCoupon
    {
        return new AddCoupon($addCouponRequest->getToken(), $addCouponRequest->getCoupon());
    }

    final public function validate(Request $request): ConstraintViolationListInterface
    {
        return $this->validator->validate($this->transformRequest($request));
    }

    final public function getCommand(Request $request): object
    {
        return $this->transformCommand($this->transformRequest($request));
    }
}
