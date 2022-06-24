<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Careers
 *
 * @ORM\Table(name="careers")
 * @ORM\Entity
 */
class Careers
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
     * @ORM\Column(name="admin_id", type="bigint", nullable=false)
     */
    private $adminId;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=200, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="text", length=65535, nullable=false)
     */
    private $location;

    /**
     * @var string
     *
     * @ORM\Column(name="contract_length", type="text", length=65535, nullable=false)
     */
    private $contractLength;

    /**
     * @var string
     *
     * @ORM\Column(name="over_view", type="text", length=65535, nullable=false)
     */
    private $overView;

    /**
     * @var string
     *
     * @ORM\Column(name="responsibilities", type="text", length=65535, nullable=false)
     */
    private $responsibilities;

    /**
     * @var string
     *
     * @ORM\Column(name="desirability", type="text", length=65535, nullable=false)
     */
    private $desirability;

    /**
     * @var string
     *
     * @ORM\Column(name="qualifications", type="text", length=65535, nullable=false)
     */
    private $qualifications;

    /**
     * @var string
     *
     * @ORM\Column(name="commitment", type="text", length=65535, nullable=false)
     */
    private $commitment;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $createdOn = 'CURRENT_TIMESTAMP';

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

    public function getAdminId(): ?string
    {
        return $this->adminId;
    }

    public function setAdminId(string $adminId): self
    {
        $this->adminId = $adminId;

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

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getContractLength(): ?string
    {
        return $this->contractLength;
    }

    public function setContractLength(string $contractLength): self
    {
        $this->contractLength = $contractLength;

        return $this;
    }

    public function getOverView(): ?string
    {
        return $this->overView;
    }

    public function setOverView(string $overView): self
    {
        $this->overView = $overView;

        return $this;
    }

    public function getResponsibilities(): ?string
    {
        return $this->responsibilities;
    }

    public function setResponsibilities(string $responsibilities): self
    {
        $this->responsibilities = $responsibilities;

        return $this;
    }

    public function getDesirability(): ?string
    {
        return $this->desirability;
    }

    public function setDesirability(string $desirability): self
    {
        $this->desirability = $desirability;

        return $this;
    }

    public function getQualifications(): ?string
    {
        return $this->qualifications;
    }

    public function setQualifications(string $qualifications): self
    {
        $this->qualifications = $qualifications;

        return $this;
    }

    public function getCommitment(): ?string
    {
        return $this->commitment;
    }

    public function setCommitment(string $commitment): self
    {
        $this->commitment = $commitment;

        return $this;
    }

    public function getCreatedOn(): ?\DateTimeInterface
    {
        return $this->createdOn;
    }

    public function setCreatedOn(\DateTimeInterface $createdOn): self
    {
        $this->createdOn = $createdOn;

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
