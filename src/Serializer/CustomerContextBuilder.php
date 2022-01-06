<?php

// https://api-platform.com/docs/core/serialization/#changing-the-serialization-context-dynamically

namespace App\Serializer;

use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use App\Entity\Customer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class CustomerContextBuilder implements SerializerContextBuilderInterface
{
    public function __construct(
        private SerializerContextBuilderInterface $decorated,
        private AuthorizationCheckerInterface     $authorizationChecker
    )
    {
    }

    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array
    {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);
        $resourceClass = $context['resource_class'] ?? null;

        if (
            $resourceClass === Customer::class &&
            isset($context['groups']) &&
            true === $normalization &&
            $this->authorizationChecker->isGranted('ROLE_ADMIN')
        ) {
            $context['groups'][] = 'read:customer:item:admin';
        }

        return $context;
    }
}
