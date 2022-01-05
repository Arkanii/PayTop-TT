<?php

namespace App\Handler;

use App\Entity\Client;
use App\Service\SendToWebhookWebsite;
use Symfony\Component\Messenger\Exception\RuntimeException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

final class ClientHandler implements MessageHandlerInterface
{
    private SendToWebhookWebsite $sender;

    public function __construct(SendToWebhookWebsite $sender)
    {
        $this->sender = $sender;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function __invoke(Client $client)
    {
        if ($this->sender->sendEntity($client)->getStatusCode() !== 200) {
            throw new RuntimeException("Error on webhook.site request.");
        }
    }
}
