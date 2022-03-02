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

namespace Sylius\ShopApiPlugin\Controller\Customer;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\ShopApiPlugin\View\ValidationErrorView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final class CustomerController extends ResourceController
{
    public function createAction(Request $request): Response
    {
        $response = parent::createAction($request);

        if (Response::HTTP_BAD_REQUEST !== $response->getStatusCode()) {
            return $response;
        }

        /** @var ViewHandlerInterface $viewHandler */
        $viewHandler = $this->get('fos_rest.view_handler');

        $validationResults = $response->getContent();
        Assert::string($validationResults);

        return $viewHandler
            ->handle(View::create($this->createValidationMessage($validationResults), Response::HTTP_BAD_REQUEST))
        ;
    }

    private function createValidationMessage(string $validationResults): ValidationErrorView
    {
        /** @var ValidationErrorView $errorMessage */
        $errorMessage = new ValidationErrorView();

        $errorMessage->code = Response::HTTP_BAD_REQUEST;
        $errorMessage->message = 'Validation failed';

        $parsedValidationResults = json_decode($validationResults, true);

        if (isset($parsedValidationResults['errors']['errors'])) {
            $errorMessage->errors['errors'] = $parsedValidationResults['errors']['errors'];
        }

        $childrenErrors = $parsedValidationResults['errors']['children'];
        $this->addErrorFromField($errorMessage, $childrenErrors, 'email');
        $this->addErrorFromField($errorMessage, $childrenErrors, 'firstName');
        $this->addErrorFromField($errorMessage, $childrenErrors, 'lastName');
        $this->addErrorFromField($errorMessage, $childrenErrors, 'phoneNumber');
        $this->addErrorFromField($errorMessage, $childrenErrors, 'subscribedToNewsletter');
        $this->addErrorFromField(
            $errorMessage,
            $childrenErrors['user']['children']['plainPassword']['children']['first'],
            'subscribedToNewsletter'
        );
        $this->addErrorFromField(
            $errorMessage,
            $childrenErrors['user']['children']['plainPassword']['children']['second'],
            'subscribedToNewsletter'
        );

        return $errorMessage;
    }

    private function addErrorFromField(ValidationErrorView $errorMessage, array $errors, string $field): void
    {
        if (isset($errors[$field]['errors'])) {
            $errorMessage->errors[$field] = $errors[$field]['errors'];
        }
    }
}
