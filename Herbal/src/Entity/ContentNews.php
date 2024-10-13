<?php

namespace App\Entity;

use App\Repository\ContentNewsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ContentNewsRepository::class)]
class ContentNews
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(inversedBy: 'contentNews')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Content $content = null;

    #[ORM\ManyToOne(inversedBy: 'contentNews')]
    #[ORM\JoinColumn(nullable: false)]
    private ?News $news = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getContent(): ?Content
    {
        return $this->content;
    }

    public function setContent(?Content $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getNews(): ?News
    {
        return $this->news;
    }

    public function setNews(?News $news): static
    {
        $this->news = $news;

        return $this;
    }
}
