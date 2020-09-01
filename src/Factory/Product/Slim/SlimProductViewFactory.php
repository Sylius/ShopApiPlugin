<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Product\Slim;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTranslationInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\ShopApiPlugin\Exception\ViewCreationException;
use Sylius\ShopApiPlugin\Factory\ImageViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Product\ProductAttributeValuesViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Product\ProductVariantViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Product\ProductViewFactoryInterface;
use Sylius\ShopApiPlugin\Transformer\Transformer;
use Sylius\ShopApiPlugin\View\Product\ProductView;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Entity\User\ShopUser;

class SlimProductViewFactory implements ProductViewFactoryInterface
{

    use Transformer;

    public $defaultIncludes = [
        'code',
        'isFavorite',
        'name',
        'slug',
        'createdAt',
        'updatedAt',
        'images',
        'attributes',
        'sortedVariants',
        'wildberriesLink',
    ];

    /** @var ImageViewFactoryInterface */
    private $imageViewFactory;

    /** @var ProductAttributeValuesViewFactoryInterface */
    private $attributeValuesViewFactory;

    /** @var string */
    private $productTaxonViewClass;

    /** @var string */
    private $fallbackLocale;

    /** @var ProductVariantViewFactoryInterface */
    private $variantViewFactory;
    private $tokenStorage;
    private $locale;
    private $channel;

    public function __construct(
        ImageViewFactoryInterface $imageViewFactory,
        ProductAttributeValuesViewFactoryInterface $attributeValuesViewFactory,
        string $productViewClass,
        string $productTaxonViewClass,
        string $fallbackLocale,
        ProductVariantViewFactoryInterface $variantViewFactory,
        TokenStorageInterface $tokenStorage
    ) {
        $this->imageViewFactory           = $imageViewFactory;
        $this->attributeValuesViewFactory = $attributeValuesViewFactory;
        $this->viewClass                  = $productViewClass;
        $this->productTaxonViewClass      = $productTaxonViewClass;
        $this->fallbackLocale             = $fallbackLocale;
        $this->variantViewFactory         = $variantViewFactory;
        $this->tokenStorage               = $tokenStorage;
    }

    /** {@inheritdoc} */
    public function create(ProductInterface $product, ChannelInterface $channel, string $locale): ProductView
    {
        $this->locale  = $locale;
        $this->channel = $channel;
        /** @var ProductView $productView */
        $productView = $this->generate($product);

        return $productView;
    }

    public function getCode(ProductInterface $product, $productView)
    {
        $productView->code = $product->getCode();

        return $productView;
    }

    public function getCreatedAt(ProductInterface $product, $productView)
    {
        $productView->createdAt = $product->getCreatedAt();

        return $productView;
    }

    public function getUpdatedAt(ProductInterface $product, $productView)
    {
        $productView->updatedAt = $product->getUpdatedAt();

        return $productView;
    }

    public function getName(ProductInterface $product, $productView)
    {
        /** @var ProductTranslationInterface $translation */
        $translation       = $product->getTranslation($this->locale);
        $productView->name = $translation->getName();

        return $productView;
    }

    public function getSlug(ProductInterface $product, $productView)
    {
        /** @var ProductTranslationInterface $translation */
        $translation       = $product->getTranslation($this->locale);
        $productView->slug = $translation->getSlug();

        return $productView;
    }

    public function getWildberriesLink(ProductInterface $product, $productView)
    {
        $productView->wildberriesLink = $product->getWildberriesLink();

        return $productView;
    }

    public function getIsFavorite(ProductInterface $product, $productView)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $productView->isFavorite = false;

        if ($user instanceof ShopUser && $user->isFavorite($product)) {
            $productView->isFavorite = true;
        }

        return $productView;
    }

    public function getImages(ProductInterface $product, $productView)
    {
        /** @var ProductImageInterface $image */
        foreach ($product->getImages() as $image) {
            $imageView             = $this->imageViewFactory->create($image);
            $productView->images[] = $imageView;
        }

        return $productView;
    }

    public function getAttributes(ProductInterface $product, $productView)
    {
        $productView->attributes =
            $this->attributeValuesViewFactory->create($product->getAttributesByLocale($this->locale,
                $this->fallbackLocale
            )->toArray(),
                $this->locale
            );

        return $productView;
    }

    public function getSortedVariants(ProductInterface $product, $productView)
    {
        $this->variantViewFactory->setDefaultIncludes($this->defaultIncludes['sortedVariants'] ?? null);

        /** @var ProductVariantInterface $variant */
        foreach ($product->getSortedVariants() as $variant) {
            if($variant->isEnabled()){
                try {
                    $productView->variants[$variant->getCode()] =
                        $this->variantViewFactory->create($variant, $this->channel, $this->locale);
                } catch (ViewCreationException $exception) {
                    continue;
                }
            }

        }

        return $productView;
    }

}
