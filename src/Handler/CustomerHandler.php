<?php

namespace App\Handler;

use App\Entity\Customer;
use App\Service\SendToWebhookWebsite;
use Symfony\Component\Messenger\Exception\RuntimeException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

final class CustomerHandler implements MessageHandlerInterface
{
    public function __construct(
        private SendToWebhookWebsite $sender
    )
    {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function __invoke(Customer $customer)
    {
        if ($this->sender->sendEntity($customer)->getStatusCode() !== 200) {
            throw new RuntimeException("Error on webhook.site request.");
        }
    }
}
