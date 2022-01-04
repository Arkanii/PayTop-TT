<?php

// https://api-platform.com/docs/core/events/#custom-event-listeners

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Client;
use Exception;
use RuntimeException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ClientWebhookSubscriber implements EventSubscriberInterface
{
    private HttpClientInterface $client;
    private ParameterBagInterface $params;
    private SerializerInterface $serializer;

    public function __construct(ParameterBagInterface $params, HttpClientInterface $client, SerializerInterface $serializer)
    {
        $this->params = $params;
        $this->client = $client;
        $this->serializer = $serializer;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['sendWebhook', EventPriorities::POST_WRITE],
        ];
    }

    /**
     * @param ViewEvent $event
     * @throws TransportExceptionInterface
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws Exception
     */
    public function sendWebhook(ViewEvent $event): void
    {
        $client = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$client instanceof Client || Request::METHOD_POST !== $method) {
            return;
        }

        // https://symfony.com/doc/current/http_client.html
        $response = $this->client->request(
            'POST',
            'https://webhook.site/' . $this->params->get('app.webhook_token'),
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                // https://symfony.com/doc/current/serializer.html
                'body' => $this->serializer->serialize(
                    $client,
                    'json',
                    ['groups' => 'post']
                )
            ]
        );

        $statusCode = $response->getStatusCode();

        if ($statusCode !== 200) {
            throw new RuntimeException("Error on webhook.site request.");
        }
    }
}
