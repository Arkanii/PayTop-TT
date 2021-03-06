<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class SendToWebhookWebsite
{
    public function __construct(
        private ParameterBagInterface $params,
        private HttpClientInterface   $client,
        private SerializerInterface   $serializer
    )
    {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendEntity(object $entity): ResponseInterface
    {
        // https://symfony.com/doc/current/http_client.html
        return $this->client->request(
            'POST',
            'https://webhook.site/' . $this->params->get('app.webhook_token'),
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                // https://symfony.com/doc/current/serializer.html
                'body' => $this->serializer->serialize(
                    $entity,
                    'json',
                    ['groups' => 'write:customer']
                )
            ]
        );
    }
}
