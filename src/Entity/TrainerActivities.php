<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TrainerActivities
 *
 * @ORM\Table(name="trainer_activities", indexes={@ORM\Index(name="service_id", columns={"service_id"}), @ORM\Index(name="trainer_id", columns={"trainer_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\TrainerActivitiesRepository")
 */
class TrainerActivities
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
     * @var \Services
     *
     * @ORM\ManyToOne(targetEntity="Services")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="service_id", referencedColumnName="record_id")
     * })
     */
    private $service;

    /**
     * @var \TrainerProfiles
     *
     * @ORM\ManyToOne(targetEntity="TrainerProfiles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="trainer_id", referencedColumnName="client_look_up_id")
     * })
     */
    private $trainer;

    public function getRecordId(): ?string
    {
        return $this->recordId;
    }

    public function getService(): ?Services
    {
        return $this->service;
    }

    public function setService(?Services $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getTrainer(): ?TrainerProfiles
    {
        return $this->trainer;
    }

    public function setTrainer(?TrainerProfiles $trainer): self
    {
        $this->trainer = $trainer;

        return $this;
    }


}
