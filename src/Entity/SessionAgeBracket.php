<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SessionAgeBracket
 *
 * @ORM\Table(name="session_age_bracket", indexes={@ORM\Index(name="age_bracket", columns={"age_bracket"}), @ORM\Index(name="session_id", columns={"session_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\SessionAgeBracketRepository")
 */
class SessionAgeBracket
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
     * @ORM\Column(name="age_bracket", type="integer", nullable=false)
     */
    private $ageBracket;

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

    public function getAgeBracket(): ?int
    {
        return $this->ageBracket;
    }

    public function setAgeBracket(int $ageBracket): self
    {
        $this->ageBracket = $ageBracket;

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
