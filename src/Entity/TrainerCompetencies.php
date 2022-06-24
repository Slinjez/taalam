<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TrainerCompetencies
 *
 * @ORM\Table(name="trainer_competencies", indexes={@ORM\Index(name="trainer_id", columns={"trainer_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\TrainerCompetenciesRepository")
 */
class TrainerCompetencies
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
     * @ORM\Column(name="competency", type="text", length=65535, nullable=false)
     */
    private $competency;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=false)
     */
    private $description;

    /**
     * @var \TrainerProfiles
     *
     * @ORM\ManyToOne(targetEntity="TrainerProfiles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="trainer_id", referencedColumnName="record_id")
     * })
     */
    private $trainer;

    public function getRecordId(): ?string
    {
        return $this->recordId;
    }

    public function getCompetency(): ?string
    {
        return $this->competency;
    }

    public function setCompetency(string $competency): self
    {
        $this->competency = $competency;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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
