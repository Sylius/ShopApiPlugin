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

namespace Sylius\ShopApiPlugin\Provider;

use Sylius\Component\Core\Model\ProductReviewerInterface;

final class ProductReviewerProvider implements ProductReviewerProviderInterface
{
    /** @var CustomerProviderInterface */
    private $customerProvider;

    public function __construct(CustomerProviderInterface $customerProvider)
    {
        $this->customerProvider = $customerProvider;
    }

    public function provide(string $email): ProductReviewerInterface
    {
        return $this->customerProvider->provide($email);
    }
}
