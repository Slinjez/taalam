<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TrainingSessions
 *
 * @ORM\Table(name="training_sessions", indexes={@ORM\Index(name="client_id", columns={"client_id"}), @ORM\Index(name="session_id", columns={"session_id"}), @ORM\Index(name="trainer_id", columns={"trainer_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\TrainingSessionsRepository")
 */
class TrainingSessions
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
     * @var \DateTime
     *
     * @ORM\Column(name="session_booked_date", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $sessionBookedDate = 'CURRENT_TIMESTAMP';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="session_date", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $sessionDate = 'CURRENT_TIMESTAMP';

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="text", length=65535, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=false)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", nullable=false, options={"default"="1"})
     */
    private $status = 1;

    /**
     * @var int
     *
     * @ORM\Column(name="rating", type="integer", nullable=false)
     */
    private $rating = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="rating_comment", type="text", length=65535, nullable=false)
     */
    private $ratingComment;

    /**
     * @var \TrainerProfiles
     *
     * @ORM\ManyToOne(targetEntity="TrainerProfiles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="trainer_id", referencedColumnName="record_id")
     * })
     */
    private $trainer;

    /**
     * @var \Clients
     *
     * @ORM\ManyToOne(targetEntity="Clients")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="client_id", referencedColumnName="record_id")
     * })
     */
    private $client;

    /**
     * @var \Services
     *
     * @ORM\ManyToOne(targetEntity="Services")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="session_id", referencedColumnName="record_id")
     * })
     */
    private $session;

    public function getRecordId(): ?string
    {
        return $this->recordId;
    }

    public function getSessionBookedDate(): ?\DateTimeInterface
    {
        return $this->sessionBookedDate;
    }

    public function setSessionBookedDate(\DateTimeInterface $sessionBookedDate): self
    {
        $this->sessionBookedDate = $sessionBookedDate;

        return $this;
    }

    public function getSessionDate(): ?\DateTimeInterface
    {
        return $this->sessionDate;
    }

    public function setSessionDate(\DateTimeInterface $sessionDate): self
    {
        $this->sessionDate = $sessionDate;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getRatingComment(): ?string
    {
        return $this->ratingComment;
    }

    public function setRatingComment(string $ratingComment): self
    {
        $this->ratingComment = $ratingComment;

        return $this;
    }

    public function getTrainer(): ?TrainerProfiles
    {
        return $this->trainer;
    }

    public function setTrainer(?TrainerProfiles $trainer): self
    {
        $this->trainer = $trainer;

        return $this;
    }

    public function getClient(): ?Clients
    {
        return $this->client;
    }

    public function setClient(?Clients $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getSession(): ?Services
    {
        return $this->session;
    }

    public function setSession(?Services $session): self
    {
        $this->session = $session;

        return $this;
    }


}
