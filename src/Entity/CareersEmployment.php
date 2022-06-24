<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CareersEmployment
 *
 * @ORM\Table(name="careers_employment")
 * @ORM\Entity
 */
class CareersEmployment
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
     * @ORM\Column(name="employer", type="string", length=200, nullable=false)
     */
    private $employer;

    /**
     * @var string
     *
     * @ORM\Column(name="position_and_resp", type="text", length=65535, nullable=false)
     */
    private $positionAndResp;

    /**
     * @var string
     *
     * @ORM\Column(name="reason_for_leaving", type="text", length=65535, nullable=false)
     */
    private $reasonForLeaving;

    /**
     * @var string
     *
     * @ORM\Column(name="volunteer_experience", type="text", length=65535, nullable=false)
     */
    private $volunteerExperience;

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

    public function getEmployer(): ?string
    {
        return $this->employer;
    }

    public function setEmployer(string $employer): self
    {
        $this->employer = $employer;

        return $this;
    }

    public function getPositionAndResp(): ?string
    {
        return $this->positionAndResp;
    }

    public function setPositionAndResp(string $positionAndResp): self
    {
        $this->positionAndResp = $positionAndResp;

        return $this;
    }

    public function getReasonForLeaving(): ?string
    {
        return $this->reasonForLeaving;
    }

    public function setReasonForLeaving(string $reasonForLeaving): self
    {
        $this->reasonForLeaving = $reasonForLeaving;

        return $this;
    }

    public function getVolunteerExperience(): ?string
    {
        return $this->volunteerExperience;
    }

    public function setVolunteerExperience(string $volunteerExperience): self
    {
        $this->volunteerExperience = $volunteerExperience;

        return $this;
    }


}
