<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CareerAvailability
 *
 * @ORM\Table(name="career_availability")
 * @ORM\Entity
 */
class CareerAvailability
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
     * @ORM\Column(name="career_app_id", type="bigint", nullable=false)
     */
    private $careerAppId;

    /**
     * @var int
     *
     * @ORM\Column(name="id_avl", type="integer", nullable=false)
     */
    private $idAvl = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=20, nullable=true)
     */
    private $description;

    public function getRecordId(): ?string
    {
        return $this->recordId;
    }

    public function getCareerAppId(): ?string
    {
        return $this->careerAppId;
    }

    public function setCareerAppId(string $careerAppId): self
    {
        $this->careerAppId = $careerAppId;

        return $this;
    }

    public function getIdAvl(): ?int
    {
        return $this->idAvl;
    }

    public function setIdAvl(int $idAvl): self
    {
        $this->idAvl = $idAvl;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }


}
