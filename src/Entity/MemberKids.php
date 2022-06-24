<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MemberKids
 *
 * @ORM\Table(name="member_kids")
 * @ORM\Entity
 */
class MemberKids
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
     * @ORM\Column(name="member_id", type="bigint", nullable=false)
     */
    private $memberId;

    /**
     * @var string
     *
     * @ORM\Column(name="kidsname", type="string", length=100, nullable=false)
     */
    private $kidsname;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_of_birth", type="datetime", nullable=false)
     */
    private $dateOfBirth;

    /**
     * @var string|null
     *
     * @ORM\Column(name="allergies", type="text", length=65535, nullable=true)
     */
    private $allergies;

    /**
     * @var string|null
     *
     * @ORM\Column(name="medical_conditions", type="text", length=65535, nullable=true)
     */
    private $medicalConditions;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", nullable=false, options={"default"="1"})
     */
    private $status = 1;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $createdDate = 'CURRENT_TIMESTAMP';

    public function getRecordId(): ?string
    {
        return $this->recordId;
    }

    public function getMemberId(): ?string
    {
        return $this->memberId;
    }

    public function setMemberId(string $memberId): self
    {
        $this->memberId = $memberId;

        return $this;
    }

    public function getKidsname(): ?string
    {
        return $this->kidsname;
    }

    public function setKidsname(string $kidsname): self
    {
        $this->kidsname = $kidsname;

        return $this;
    }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(\DateTimeInterface $dateOfBirth): self
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    public function getAllergies(): ?string
    {
        return $this->allergies;
    }

    public function setAllergies(?string $allergies): self
    {
        $this->allergies = $allergies;

        return $this;
    }

    public function getMedicalConditions(): ?string
    {
        return $this->medicalConditions;
    }

    public function setMedicalConditions(?string $medicalConditions): self
    {
        $this->medicalConditions = $medicalConditions;

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

    public function getCreatedDate(): ?\DateTimeInterface
    {
        return $this->createdDate;
    }

    public function setCreatedDate(\DateTimeInterface $createdDate): self
    {
        $this->createdDate = $createdDate;

        return $this;
    }


}
