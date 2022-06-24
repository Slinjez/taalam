<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TrainerProfiles
 *
 * @ORM\Table(name="trainer_profiles", indexes={@ORM\Index(name="client_look_up_id", columns={"client_look_up_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\TrainerProfilesRepository")
 */
class TrainerProfiles
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
     * @ORM\Column(name="date_created", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $dateCreated = 'CURRENT_TIMESTAMP';

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", nullable=false, options={"default"="1"})
     */
    private $status = 1;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=10, nullable=false)
     */
    private $gender;

    /**
     * @var int
     *
     * @ORM\Column(name="is_top", type="integer", nullable=false)
     */
    private $isTop = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="top_status_date", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $topStatusDate = 'CURRENT_TIMESTAMP';

    /**
     * @var \Clients
     *
     * @ORM\ManyToOne(targetEntity="Clients")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="client_look_up_id", referencedColumnName="record_id")
     * })
     */
    private $clientLookUp;

    public function getRecordId(): ?string
    {
        return $this->recordId;
    }

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function setDateCreated(\DateTimeInterface $dateCreated): self
    {
        $this->dateCreated = $dateCreated;

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

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getIsTop(): ?int
    {
        return $this->isTop;
    }

    public function setIsTop(int $isTop): self
    {
        $this->isTop = $isTop;

        return $this;
    }

    public function getTopStatusDate(): ?\DateTimeInterface
    {
        return $this->topStatusDate;
    }

    public function setTopStatusDate(\DateTimeInterface $topStatusDate): self
    {
        $this->topStatusDate = $topStatusDate;

        return $this;
    }

    public function getClientLookUp(): ?Clients
    {
        return $this->clientLookUp;
    }

    public function setClientLookUp(?Clients $clientLookUp): self
    {
        $this->clientLookUp = $clientLookUp;

        return $this;
    }


}
