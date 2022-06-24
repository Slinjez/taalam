<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CareerSportExp
 *
 * @ORM\Table(name="career_sport_exp")
 * @ORM\Entity
 */
class CareerSportExp
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
     * @ORM\Column(name="activity", type="text", length=65535, nullable=false)
     */
    private $activity;

    /**
     * @var string
     *
     * @ORM\Column(name="age_group", type="text", length=65535, nullable=false)
     */
    private $ageGroup;

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

    public function getActivity(): ?string
    {
        return $this->activity;
    }

    public function setActivity(string $activity): self
    {
        $this->activity = $activity;

        return $this;
    }

    public function getAgeGroup(): ?string
    {
        return $this->ageGroup;
    }

    public function setAgeGroup(string $ageGroup): self
    {
        $this->ageGroup = $ageGroup;

        return $this;
    }


}
