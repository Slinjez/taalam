<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GroupCTMessages
 *
 * @ORM\Table(name="group_c_t_messages")
 * @ORM\Entity
 */
class GroupCTMessages
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
     * @ORM\Column(name="client_id", type="bigint", nullable=false)
     */
    private $clientId;

    /**
     * @var int
     *
     * @ORM\Column(name="trainer_id", type="bigint", nullable=false)
     */
    private $trainerId;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", length=65535, nullable=false)
     */
    private $message;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="on_date", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $onDate = 'CURRENT_TIMESTAMP';

    /**
     * @var int
     *
     * @ORM\Column(name="is_read_status", type="integer", nullable=false, options={"default"="1"})
     */
    private $isReadStatus = 1;

    /**
     * @var int
     *
     * @ORM\Column(name="is_viewable", type="integer", nullable=false, options={"default"="1"})
     */
    private $isViewable = 1;

    public function getRecordId(): ?string
    {
        return $this->recordId;
    }

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): self
    {
        $this->clientId = $clientId;

        return $this;
    }

    public function getTrainerId(): ?string
    {
        return $this->trainerId;
    }

    public function setTrainerId(string $trainerId): self
    {
        $this->trainerId = $trainerId;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getOnDate(): ?\DateTimeInterface
    {
        return $this->onDate;
    }

    public function setOnDate(\DateTimeInterface $onDate): self
    {
        $this->onDate = $onDate;

        return $this;
    }

    public function getIsReadStatus(): ?int
    {
        return $this->isReadStatus;
    }

    public function setIsReadStatus(int $isReadStatus): self
    {
        $this->isReadStatus = $isReadStatus;

        return $this;
    }

    public function getIsViewable(): ?int
    {
        return $this->isViewable;
    }

    public function setIsViewable(int $isViewable): self
    {
        $this->isViewable = $isViewable;

        return $this;
    }


}
