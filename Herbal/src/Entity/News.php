<?php

namespace App\Entity;

use App\Repository\NewsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: NewsRepository::class)]
class News
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\ManyToOne(inversedBy: 'news')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Content $content = null;

    /**
     * @var Collection<int, ContentNews>
     */
    #[ORM\OneToMany(targetEntity: ContentNews::class, mappedBy: 'news', orphanRemoval: true)]
    private Collection $contentNews;

    public function __construct()
    {
        $this->contentNews = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
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

    /**
     * @return Collection<int, ContentNews>
     */
    public function getContentNews(): Collection
    {
        return $this->contentNews;
    }

    public function addContentNews(ContentNews $contentNews): static
    {
        if (!$this->contentNews->contains($contentNews)) {
            $this->contentNews->add($contentNews);
            $contentNews->setNews($this);
        }

        return $this;
    }

    public function removeContentNews(ContentNews $contentNews): static
    {
        if ($this->contentNews->removeElement($contentNews)) {
            // set the owning side to null (unless already changed)
            if ($contentNews->getNews() === $this) {
                $contentNews->setNews(null);
            }
        }

        return $this;
    }
}
