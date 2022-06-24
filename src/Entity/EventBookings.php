<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EventBookings
 *
 * @ORM\Table(name="event_bookings")
 * @ORM\Entity(repositoryClass="App\Repository\EventBookingsRepository")
 */
class EventBookings
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
     * @ORM\Column(name="client_id", type="bigint", nullable=false)
     */
    private $clientId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="booking_date", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $bookingDate = 'CURRENT_TIMESTAMP';

    /**
     * @var int
     *
     * @ORM\Column(name="number_of_children", type="integer", nullable=false)
     */
    private $numberOfChildren;

    /**
     * @var int
     *
     * @ORM\Column(name="number_of_number_of_chaperone", type="integer", nullable=false)
     */
    private $numberOfNumberOfChaperone;

    /**
     * @var string
     *
     * @ORM\Column(name="extra_info", type="text", length=65535, nullable=false)
     */
    private $extraInfo;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="is_school_booking", type="integer", nullable=false)
     */
    private $isSchoolBooking = '0';

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

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): self
    {
        $this->clientId = $clientId;

        return $this;
    }

    public function getBookingDate(): ?\DateTimeInterface
    {
        return $this->bookingDate;
    }

    public function setBookingDate(\DateTimeInterface $bookingDate): self
    {
        $this->bookingDate = $bookingDate;

        return $this;
    }

    public function getNumberOfChildren(): ?int
    {
        return $this->numberOfChildren;
    }

    public function setNumberOfChildren(int $numberOfChildren): self
    {
        $this->numberOfChildren = $numberOfChildren;

        return $this;
    }

    public function getNumberOfNumberOfChaperone(): ?int
    {
        return $this->numberOfNumberOfChaperone;
    }

    public function setNumberOfNumberOfChaperone(int $numberOfNumberOfChaperone): self
    {
        $this->numberOfNumberOfChaperone = $numberOfNumberOfChaperone;

        return $this;
    }

    public function getExtraInfo(): ?string
    {
        return $this->extraInfo;
    }

    public function setExtraInfo(string $extraInfo): self
    {
        $this->extraInfo = $extraInfo;

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

    public function getIsSchoolBooking(): ?int
    {
        return $this->isSchoolBooking;
    }

    public function setIsSchoolBooking(int $isSchoolBooking): self
    {
        $this->isSchoolBooking = $isSchoolBooking;

        return $this;
    }


}
