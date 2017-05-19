<?php

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Product\Model\ProductAttributeValueInterface;

final class LimitedProductAttributeValuesViewFactory implements ProductAttributeValuesViewFactoryInterface
{
    /**
     * @var ProductAttributeValueViewFactoryInterface
     */
    private $productAttributeValueViewFactory;

    /**
     * @var string[]
     */
    private $allowedAttributesCodes;

    /**
     * @param ProductAttributeValueViewFactoryInterface $productAttributeValueViewFactory
     * @param string[] $allowedAttributesCodes
     */
    public function __construct(ProductAttributeValueViewFactoryInterface $productAttributeValueViewFactory, array $allowedAttributesCodes)
    {
        $this->productAttributeValueViewFactory = $productAttributeValueViewFactory;
        $this->allowedAttributesCodes = $allowedAttributesCodes;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $attributeValues)
    {
        $attributeValuesView = [];

        /** @var ProductAttributeValueInterface $attributeValue */
        foreach ($attributeValues as $attributeValue) {
            if (!in_array($attributeValue->getCode(), $this->allowedAttributesCodes, true)) {
                continue;
            }

            $attributeValuesView[] = $this->productAttributeValueViewFactory->create($attributeValue);
        }

        return $attributeValuesView;
    }
}
