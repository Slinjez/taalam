<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SessionTrainers
 *
 * @ORM\Table(name="session_trainers", indexes={@ORM\Index(name="session_id", columns={"session_id"}), @ORM\Index(name="trainer_id", columns={"trainer_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\SessionTrainersRepository")
 */
class SessionTrainers
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
     * @var \SessionsRevamp
     *
     * @ORM\ManyToOne(targetEntity="SessionsRevamp")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="session_id", referencedColumnName="record_id")
     * })
     */
    private $session;

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

    public function getSession(): ?SessionsRevamp
    {
        return $this->session;
    }

    public function setSession(?SessionsRevamp $session): self
    {
        $this->session = $session;

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
