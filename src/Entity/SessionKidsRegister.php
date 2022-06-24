<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SessionKidsRegister
 *
 * @ORM\Table(name="session_kids_register")
 * @ORM\Entity
 */
class SessionKidsRegister
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
     * @var \DateTime
     *
     * @ORM\Column(name="on_date", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $onDate = 'CURRENT_TIMESTAMP';

    /**
     * @var int
     *
     * @ORM\Column(name="attendance_status", type="integer", nullable=false)
     */
    private $attendanceStatus = '0';

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

    public function getOnDate(): ?\DateTimeInterface
    {
        return $this->onDate;
    }

    public function setOnDate(\DateTimeInterface $onDate): self
    {
        $this->onDate = $onDate;

        return $this;
    }

    public function getAttendanceStatus(): ?int
    {
        return $this->attendanceStatus;
    }

    public function setAttendanceStatus(int $attendanceStatus): self
    {
        $this->attendanceStatus = $attendanceStatus;

        return $this;
    }


}
