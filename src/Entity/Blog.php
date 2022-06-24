<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Blog
 *
 * @ORM\Table(name="blog")
 * @ORM\Entity(repositoryClass="App\Repository\BlogRepository")
 */
class Blog
{
    /**
     * @var int
     *
     * @ORM\Column(name="record_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $recordId;

    /**
     * @var int
     *
     * @ORM\Column(name="blogger_id", type="bigint", nullable=false)
     */
    private $boggerId;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="text", length=65535, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="teaser", type="text", length=65535, nullable=false)
     */
    private $teaser;

    /**
     * @var string
     *
     * @ORM\Column(name="blog", type="text", length=65535, nullable=false)
     */
    private $blog;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $date = 'CURRENT_TIMESTAMP';

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status = '0';

    public function getRecordId(): ?string
    {
        return $this->recordId;
    }

    public function getBoggerId(): ?string
    {
        return $this->boggerId;
    }

    public function setBoggerId(string $boggerId): self
    {
        $this->boggerId = $boggerId;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTeaser(): ?string
    {
        return $this->teaser;
    }

    public function setTeaser(string $teaser): self
    {
        $this->teaser = $teaser;

        return $this;
    }

    public function getBlog(): ?string
    {
        return $this->blog;
    }

    public function setBlog(string $blog): self
    {
        $this->blog = $blog;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }


}
