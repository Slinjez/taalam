<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CareersApplications
 *
 * @ORM\Table(name="careers_applications")
 * @ORM\Entity
 */
class CareersApplications
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
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=100, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="full_names", type="string", length=200, nullable=false)
     */
    private $fullNames;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=200, nullable=false)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile", type="string", length=50, nullable=false)
     */
    private $mobile;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=250, nullable=false)
     */
    private $email;

    /**
     * @var int
     *
     * @ORM\Column(name="under_taking_study", type="integer", nullable=false)
     */
    private $underTakingStudy = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="completed_studies", type="integer", nullable=false)
     */
    private $completedStudies = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="volunteer_exp", type="text", length=65535, nullable=false)
     */
    private $volunteerExp;

    /**
     * @var string
     *
     * @ORM\Column(name="prefered_age_group", type="text", length=65535, nullable=false)
     */
    private $preferedAgeGroup;

    /**
     * @var int
     *
     * @ORM\Column(name="worked_wit_able_diff", type="integer", nullable=false)
     */
    private $workedWitAbleDiff = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="coaching_phil", type="text", length=65535, nullable=false)
     */
    private $coachingPhil;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="app_date", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $appDate = 'CURRENT_TIMESTAMP';

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", nullable=false, options={"default"="1"})
     */
    private $status = 1;

    public function getRecordId(): ?string
    {
        return $this->recordId;
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

    public function getFullNames(): ?string
    {
        return $this->fullNames;
    }

    public function setFullNames(string $fullNames): self
    {
        $this->fullNames = $fullNames;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(string $mobile): self
    {
        $this->mobile = $mobile;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUnderTakingStudy(): ?int
    {
        return $this->underTakingStudy;
    }

    public function setUnderTakingStudy(int $underTakingStudy): self
    {
        $this->underTakingStudy = $underTakingStudy;

        return $this;
    }

    public function getCompletedStudies(): ?int
    {
        return $this->completedStudies;
    }

    public function setCompletedStudies(int $completedStudies): self
    {
        $this->completedStudies = $completedStudies;

        return $this;
    }

    public function getVolunteerExp(): ?string
    {
        return $this->volunteerExp;
    }

    public function setVolunteerExp(string $volunteerExp): self
    {
        $this->volunteerExp = $volunteerExp;

        return $this;
    }

    public function getPreferedAgeGroup(): ?string
    {
        return $this->preferedAgeGroup;
    }

    public function setPreferedAgeGroup(string $preferedAgeGroup): self
    {
        $this->preferedAgeGroup = $preferedAgeGroup;

        return $this;
    }

    public function getWorkedWitAbleDiff(): ?int
    {
        return $this->workedWitAbleDiff;
    }

    public function setWorkedWitAbleDiff(int $workedWitAbleDiff): self
    {
        $this->workedWitAbleDiff = $workedWitAbleDiff;

        return $this;
    }

    public function getCoachingPhil(): ?string
    {
        return $this->coachingPhil;
    }

    public function setCoachingPhil(string $coachingPhil): self
    {
        $this->coachingPhil = $coachingPhil;

        return $this;
    }

    public function getAppDate(): ?\DateTimeInterface
    {
        return $this->appDate;
    }

    public function setAppDate(\DateTimeInterface $appDate): self
    {
        $this->appDate = $appDate;

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
