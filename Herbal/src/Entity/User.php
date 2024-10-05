<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $surname = null;

    #[ORM\Column(length: 255)]
    private ?string $patronumic = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateOfBirth = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateOfRegistration = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Country $country = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Access $access = null;

    #[ORM\Column]
    private ?bool $isBlocked = null;

    /**
     * @var Collection<int, Content>
     */
    #[ORM\OneToMany(targetEntity: Content::class, mappedBy: 'author', orphanRemoval: true)]
    private Collection $contents;

    /**
     * @var Collection<int, Subscribe>
     */
    #[ORM\OneToMany(targetEntity: Subscribe::class, mappedBy: 'author', orphanRemoval: true)]
    private Collection $subscribes;

    public function __construct()
    {
        $this->contents = new ArrayCollection();
        $this->subscribes = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): static
    {
        $this->surname = $surname;

        return $this;
    }

    public function getPatronumic(): ?string
    {
        return $this->patronumic;
    }

    public function setPatronumic(string $patronumic): static
    {
        $this->patronumic = $patronumic;

        return $this;
    }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(\DateTimeInterface $dateOfBirth): static
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    public function getDateOfRegistration(): ?\DateTimeInterface
    {
        return $this->dateOfRegistration;
    }

    public function setDateOfRegistration(\DateTimeInterface $dateOfRegistration): static
    {
        $this->dateOfRegistration = $dateOfRegistration;

        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getAccess(): ?Access
    {
        return $this->access;
    }

    public function setAccess(?Access $access): static
    {
        $this->access = $access;

        return $this;
    }

    public function isBlocked(): ?bool
    {
        return $this->isBlocked;
    }

    public function setBlocked(bool $isBlocked): static
    {
        $this->isBlocked = $isBlocked;

        return $this;
    }

    /**
     * @return Collection<int, Content>
     */
    public function getContents(): Collection
    {
        return $this->contents;
    }

    public function addContent(Content $content): static
    {
        if (!$this->contents->contains($content)) {
            $this->contents->add($content);
            $content->setAuthor($this);
        }

        return $this;
    }

    public function removeContent(Content $content): static
    {
        if ($this->contents->removeElement($content)) {
            // set the owning side to null (unless already changed)
            if ($content->getAuthor() === $this) {
                $content->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Subscribe>
     */
    public function getSubscribes(): Collection
    {
        return $this->subscribes;
    }

    public function addSubscribe(Subscribe $subscribe): static
    {
        if (!$this->subscribes->contains($subscribe)) {
            $this->subscribes->add($subscribe);
            $subscribe->setAuthor($this);
        }

        return $this;
    }

    public function removeSubscribe(Subscribe $subscribe): static
    {
        if ($this->subscribes->removeElement($subscribe)) {
            // set the owning side to null (unless already changed)
            if ($subscribe->getAuthor() === $this) {
                $subscribe->setAuthor(null);
            }
        }

        return $this;
    }
}
