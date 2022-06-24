<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CareersInstitutions
 *
 * @ORM\Table(name="careers_institutions")
 * @ORM\Entity
 */
class CareersInstitutions
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
     * @var string
     *
     * @ORM\Column(name="institution", type="string", length=200, nullable=false)
     */
    private $institution;

    /**
     * @var string
     *
     * @ORM\Column(name="program", type="string", length=200, nullable=false)
     */
    private $program;

    /**
     * @var string
     *
     * @ORM\Column(name="year_of_completion", type="string", length=50, nullable=false)
     */
    private $yearOfCompletion;

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

    public function getInstitution(): ?string
    {
        return $this->institution;
    }

    public function setInstitution(string $institution): self
    {
        $this->institution = $institution;

        return $this;
    }

    public function getProgram(): ?string
    {
        return $this->program;
    }

    public function setProgram(string $program): self
    {
        $this->program = $program;

        return $this;
    }

    public function getYearOfCompletion(): ?string
    {
        return $this->yearOfCompletion;
    }

    public function setYearOfCompletion(string $yearOfCompletion): self
    {
        $this->yearOfCompletion = $yearOfCompletion;

        return $this;
    }


}
