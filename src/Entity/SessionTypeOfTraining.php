<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SessionTypeOfTraining
 *
 * @ORM\Table(name="session_type_of_training", indexes={@ORM\Index(name="session_id", columns={"session_id"}), @ORM\Index(name="training_type", columns={"training_type"})})
 * @ORM\Entity(repositoryClass="App\Repository\SessionTypeOfTrainingRepository")
 */
class SessionTypeOfTraining
{
    /**
     * @var int
     *
     * @ORM\Column(name="record_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $recordId;

    /**
     * @var int
     *
     * @ORM\Column(name="training_type", type="integer", nullable=false)
     */
    private $trainingType;

    /**
     * @var \SessionsRevamp
     *
     * @ORM\ManyToOne(targetEntity="SessionsRevamp")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="session_id", referencedColumnName="record_id")
     * })
     */
    private $session;

    public function getRecordId(): ?int
    {
        return $this->recordId;
    }

    public function getTrainingType(): ?int
    {
        return $this->trainingType;
    }

    public function setTrainingType(int $trainingType): self
    {
        $this->trainingType = $trainingType;

        return $this;
    }

    public function getSession(): ?SessionsRevamp
    {
        return $this->session;
    }

    public function setSession(?SessionsRevamp $session): self
    {
        $this->session = $session;

        return $this;
    }


}
