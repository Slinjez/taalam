<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SessionKids
 *
 * @ORM\Table(name="session_kids")
 * @ORM\Entity
 */
class SessionKids
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
     * @ORM\Column(name="session_id", type="bigint", nullable=false)
     */
    private $sessionId;

    /**
     * @var int
     *
     * @ORM\Column(name="child_id", type="bigint", nullable=false)
     */
    private $childId;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", nullable=false, options={"default"="1"})
     */
    private $status = 1;

    public function getRecordId(): ?string
    {
        return $this->recordId;
    }

    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }

    public function setSessionId(string $sessionId): self
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    public function getChildId(): ?string
    {
        return $this->childId;
    }

    public function setChildId(string $childId): self
    {
        $this->childId = $childId;

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
