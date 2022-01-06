<?php

namespace App\Entity;

use App\Repository\PartnerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PartnerRepository::class)]
class Partner extends User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['read:customer:item:admin', 'read:customer:collection:admin'])]
    protected $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Groups(['read:customer:item:admin', 'read:customer:collection:admin'])]
    protected $email;

    #[ORM\Column(type: 'json')]
    protected $roles = ['ROLE_PARTNER'];

    #[ORM\OneToMany(mappedBy: 'partner', targetEntity: Customer::class)]
    private $customers;

    public function __construct()
    {
        $this->customers = new ArrayCollection();
    }

    /**
     * @return Collection|Customer[]
     */
    public function getCustomers(): Collection
    {
        return $this->customers;
    }

    public function addCustomer(Customer $customer): self
    {
        if (!$this->customers->contains($customer)) {
            $this->customers[] = $customer;
            $customer->setPartner($this);
        }

        return $this;
    }

    public function removeCustomer(Customer $customer): self
    {
        if ($this->customers->removeElement($customer)) {
            // set the owning side to null (unless already changed)
            if ($customer->getPartner() === $this) {
                $customer->setPartner(null);
            }
        }

        return $this;
    }
}
