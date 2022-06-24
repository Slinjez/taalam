<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SesssionServices
 *
 * @ORM\Table(name="sesssion_services", indexes={@ORM\Index(name="service_id", columns={"service_id"}), @ORM\Index(name="session_id", columns={"session_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\SesssionServicesRepository")
 */
class SesssionServices
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
     * @var \SessionsRevamp
     *
     * @ORM\ManyToOne(targetEntity="SessionsRevamp")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="session_id", referencedColumnName="record_id")
     * })
     */
    private $session;

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
