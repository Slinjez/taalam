<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CareerAbleDiff
 *
 * @ORM\Table(name="career_able_diff")
 * @ORM\Entity
 */
class CareerAbleDiff
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
     * @ORM\Column(name="condition_exp", type="text", length=65535, nullable=false)
     */
    private $conditionExp;

    /**
     * @var string
     *
     * @ORM\Column(name="activity_exp", type="text", length=65535, nullable=false)
     */
    private $activityExp;

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

    public function getConditionExp(): ?string
    {
        return $this->conditionExp;
    }

    public function setConditionExp(string $conditionExp): self
    {
        $this->conditionExp = $conditionExp;

        return $this;
    }

    public function getActivityExp(): ?string
    {
        return $this->activityExp;
    }

    public function setActivityExp(string $activityExp): self
    {
        $this->activityExp = $activityExp;

        return $this;
    }


}
