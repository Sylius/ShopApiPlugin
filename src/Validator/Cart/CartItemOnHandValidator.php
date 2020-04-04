<?php

namespace Sylius\ShopApiPlugin\Validator\Cart;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sylius\ShopApiPlugin\Request\Checkout\CompleteOrderRequest;

class CartItemOnHandValidator extends ConstraintValidator
{
    private $tokenStorage;
    private $orderRepository;
    private $translator;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        OrderRepositoryInterface $orderRepository,
        TranslatorInterface $translator
    ) {
        $this->orderRepository = $orderRepository;
        $this->tokenStorage    = $tokenStorage;
        $this->translator      = $translator;
    }

    public function validate($request, Constraint $constraint)
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneBy(["tokenValue" => $request->getToken()]);

        /** @var OrderItemInterface $item */
        foreach ($order->getItems() as $item){

            if($item->getVariant()->getTracked() == true && $item->getVariant()->getOnHand() == 0){
                $this->context->addViolation($this->translator->trans($constraint->message, ['name' => str_replace("&nbsp;", "", $item->getVariantName())]));
            }
        }
    }
    /** @param Constraint $constraint */
    private function buildViolation(Constraint $constraint, $message)
    {
        $this->context->buildViolation($message)->atPath('items')->addViolation();
    }

}
