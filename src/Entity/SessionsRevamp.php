<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SessionsRevamp
 *
 * @ORM\Table(name="sessions_revamp", indexes={@ORM\Index(name="age_bracket", columns={"age_bracket"}), @ORM\Index(name="type_of_training", columns={"type_of_training"})})
 * @ORM\Entity(repositoryClass="App\Repository\SessionsRevampRepository")
 */
class SessionsRevamp
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
     * @var string
     *
     * @ORM\Column(name="session_title", type="string", length=200, nullable=false)
     */
    private $sessionTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="tag_line", type="string", length=200, nullable=false)
     */
    private $tagLine;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $startDate = 'CURRENT_TIMESTAMP';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $endDate = 'CURRENT_TIMESTAMP';

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=100, nullable=false)
     */
    private $location;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=false)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="thumbnail", type="string", length=200, nullable=false)
     */
    private $thumbnail;

    /**
     * @var int
     *
     * @ORM\Column(name="max_attendee", type="integer", nullable=false)
     */
    private $maxAttendee = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="chaperone_allowed", type="integer", nullable=false)
     */
    private $chaperoneAllowed = '0';

    /**
     * @var \AgeBrackets
     *
     * @ORM\ManyToOne(targetEntity="AgeBrackets")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="age_bracket", referencedColumnName="record_id")
     * })
     */
    private $ageBracket;

    /**
     * @var \TypeOfTraining
     *
     * @ORM\ManyToOne(targetEntity="TypeOfTraining")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_of_training", referencedColumnName="record_id")
     * })
     */
    private $typeOfTraining;

    public function getRecordId(): ?string
    {
        return $this->recordId;
    }

    public function getSessionTitle(): ?string
    {
        return $this->sessionTitle;
    }

    public function setSessionTitle(string $sessionTitle): self
    {
        $this->sessionTitle = $sessionTitle;

        return $this;
    }

    public function getTagLine(): ?string
    {
        return $this->tagLine;
    }

    public function setTagLine(string $tagLine): self
    {
        $this->tagLine = $tagLine;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

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

    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    public function setThumbnail(string $thumbnail): self
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    public function getMaxAttendee(): ?int
    {
        return $this->maxAttendee;
    }

    public function setMaxAttendee(int $maxAttendee): self
    {
        $this->maxAttendee = $maxAttendee;

        return $this;
    }

    public function getChaperoneAllowed(): ?int
    {
        return $this->chaperoneAllowed;
    }

    public function setChaperoneAllowed(int $chaperoneAllowed): self
    {
        $this->chaperoneAllowed = $chaperoneAllowed;

        return $this;
    }

    public function getAgeBracket(): ?AgeBrackets
    {
        return $this->ageBracket;
    }

    public function setAgeBracket(?AgeBrackets $ageBracket): self
    {
        $this->ageBracket = $ageBracket;

        return $this;
    }

    public function getTypeOfTraining(): ?TypeOfTraining
    {
        return $this->typeOfTraining;
    }

    public function setTypeOfTraining(?TypeOfTraining $typeOfTraining): self
    {
        $this->typeOfTraining = $typeOfTraining;

        return $this;
    }


}
