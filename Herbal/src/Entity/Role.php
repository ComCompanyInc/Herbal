<?php

namespace App\Entity;

use App\Repository\RoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
class Role
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    /**
     * @var Collection<int, Access>
     */
    #[ORM\OneToMany(targetEntity: Access::class, mappedBy: 'role', orphanRemoval: true)]
    private Collection $accesses;

    public function __construct()
    {
        $this->accesses = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, Access>
     */
    public function getAccesses(): Collection
    {
        return $this->accesses;
    }

    public function addAccess(Access $access): static
    {
        if (!$this->accesses->contains($access)) {
            $this->accesses->add($access);
            $access->setRole($this);
        }

        return $this;
    }

    public function removeAccess(Access $access): static
    {
        if ($this->accesses->removeElement($access)) {
            // set the owning side to null (unless already changed)
            if ($access->getRole() === $this) {
                $access->setRole(null);
            }
        }

        return $this;
    }
}
