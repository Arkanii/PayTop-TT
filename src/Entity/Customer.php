<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CustomerRepository;
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
        'get' => [
            "security" => "is_granted('ROLE_PARTNER')",
            "normalization_context" => ["groups" => ["read:customer:collection"]],
        ],
        'post' => [
            "messenger" => true,
            "security_post_denormalize" => "is_granted('CUSTOMER_CREATE', object)"
        ]
    ],
    itemOperations: [
        'get' => [
            "normalization_context" => ["groups" => ["read:customer:item"]],
            "security" => "is_granted('ROLE_PARTNER') and (object.getPartner() == user or is_granted('ROLE_ADMIN'))"
        ]
    ],
    denormalizationContext: ["groups" => ["write:customer"]],
    normalizationContext: ["groups" => ["read:customer:collection", "read:customer:item"]]
)]
#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['write:customer', 'read:customer:item', 'read:customer:collection'])]
    private string $firstName;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['write:customer', 'read:customer:item', 'read:customer:collection'])]
    private string $lastName;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['write:customer', 'read:customer:item', 'read:customer:collection'])]
    private string $email;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['write:customer', 'read:customer:item', 'read:customer:collection'])]
    private string $phone;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['read:customer:item:admin'])]
    private DateTimeImmutable $created;

    #[ORM\ManyToOne(targetEntity: Partner::class, inversedBy: 'customers')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:customer:item:admin', 'read:customer:collection:admin'])]
    private Partner $partner;

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

    public function getPartner(): ?Partner
    {
        return $this->partner;
    }

    public function setPartner(?Partner $partner): self
    {
        $this->partner = $partner;

        return $this;
    }
}
