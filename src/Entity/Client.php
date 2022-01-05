<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ClientRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/* Step 2 can be resolved with "Lifecycle Callbacks" too */
# https://symfony.com/doc/current/doctrine/events.html#doctrine-lifecycle-callbacks
# #[ORM\HasLifecycleCallbacks]

// https://api-platform.com/docs/core/operations/#enabling-and-disabling-operations
// https://api-platform.com/docs/core/serialization/#using-serialization-groups-per-operation
// https://api-platform.com/docs/core/messenger/#dispatching-a-resource-through-the-message-bus
#[ApiResource(
    collectionOperations: [
        'get',
        'post' => ["messenger" => true]
    ],
    itemOperations: ['get'],
    denormalizationContext: ['groups' => ['post']],
    normalizationContext: ['groups' => ['get']]
)]
#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['get', 'post'])]
    private string $firstName;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['get', 'post'])]
    private string $lastName;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['get', 'post'])]
    private string $email;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['get', 'post'])]
    private string $phone;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['get'])]
    private DateTimeImmutable $created;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getCreated(): ?DateTimeImmutable
    {
        return $this->created;
    }

    // #[ORM\PrePersist]
    public function setCreated(): self
    {
        $this->created = new DateTimeImmutable();

        return $this;
    }
}
