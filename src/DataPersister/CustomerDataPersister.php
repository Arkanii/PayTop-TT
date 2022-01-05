<?php

// https://api-platform.com/docs/core/data-persisters/#creating-a-custom-data-persister

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use ApiPlatform\Core\DataPersister\ResumableDataPersisterInterface;
use App\Entity\Customer;
use App\Entity\Partner;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

final class CustomerDataPersister implements ContextAwareDataPersisterInterface, ResumableDataPersisterInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security               $security
    )
    {
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof Customer;
    }

    /**
     * @param Customer $data
     * @param array $context
     * @return Customer
     */
    public function persist($data, array $context = []): Customer
    {
        $user = $this->security->getUser();

        if (!$data->getId()) {
            $data->setCreated();

            if ($user instanceof Partner) {
                $data->setPartner($user);
            }
        }

        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return $data;
    }

    /**
     * @param Customer $data
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
