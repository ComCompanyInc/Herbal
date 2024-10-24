<?php

namespace App\Entity;

use App\Repository\ContentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ContentRepository::class)]
class Content
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(inversedBy: 'contents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotNull]
    private ?string $mainText = null;

    #[ORM\Column]
    private ?bool $isDelete = null;

    /**
     * @var Collection<int, News>
     */
    #[ORM\OneToMany(targetEntity: News::class, mappedBy: 'content', orphanRemoval: true)]
    private Collection $news;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateSending = null;

    /**
     * @var Collection<int, ContentNews>
     */
    #[ORM\OneToMany(targetEntity: ContentNews::class, mappedBy: 'content', orphanRemoval: true)]
    private Collection $contentNews;

    public function __construct()
    {
        $this->news = new ArrayCollection();
        $this->contentNews = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getMainText(): ?string
    {
        return $this->mainText;
    }

    public function setMainText(string $mainText): static
    {
        $this->mainText = $mainText;

        return $this;
    }

    public function getIsDelete(): ?bool
    {
        return $this->isDelete;
    }

    public function setIsDelete(bool $isDelete): static
    {
        $this->isDelete = $isDelete;

        return $this;
    }

    /**
     * @return Collection<int, News>
     */
    public function getNews(): Collection
    {
        return $this->news;
    }

    public function addNews(News $news): static
    {
        if (!$this->news->contains($news)) {
            $this->news->add($news);
            $news->setContent($this);
        }

        return $this;
    }

    public function removeNews(News $news): static
    {
        if ($this->news->removeElement($news)) {
            // set the owning side to null (unless already changed)
            if ($news->getContent() === $this) {
                $news->setContent(null);
            }
        }

        return $this;
    }

    public function getDateSending(): ?\DateTimeInterface
    {
        return $this->dateSending;
    }

    public function setDateSending(\DateTimeInterface $dateSending): static
    {
        $this->dateSending = $dateSending;

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
            $contentNews->setContent($this);
        }

        return $this;
    }

    public function removeContentNews(ContentNews $contentNews): static
    {
        if ($this->contentNews->removeElement($contentNews)) {
            // set the owning side to null (unless already changed)
            if ($contentNews->getContent() === $this) {
                $contentNews->setContent(null);
            }
        }

        return $this;
    }
}
