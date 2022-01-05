<?php

// https://api-platform.com/docs/core/data-persisters/#creating-a-custom-data-persister

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use ApiPlatform\Core\DataPersister\ResumableDataPersisterInterface;
use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;

final class ClientDataPersister implements ContextAwareDataPersisterInterface, ResumableDataPersisterInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof Client;
    }

    /**
     * @param Client $data
     * @param array $context
     * @return Client
     */
    public function persist($data, array $context = []): Client
    {
        if (!$data->getId()) {
            $data->setCreated();
        }

        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return $data;
    }

    /**
     * @param Client $data
     * @param array $context
     * @return void
     */
    public function remove($data, array $context = []): void
    {
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }

    public function resumable(array $context = []): bool
    {
        return true;
    }
}
