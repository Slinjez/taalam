<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Services
 *
 * @ORM\Table(name="services")
 * @ORM\Entity(repositoryClass="App\Repository\ServicesRepository")
 */
class Services
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
     * @ORM\Column(name="service_name", type="text", length=65535, nullable=false)
     */
    private $serviceName;

    /**
     * @var string
     *
     * @ORM\Column(name="service_description", type="text", length=65535, nullable=false)
     */
    private $serviceDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="desc_picture", type="text", length=65535, nullable=false)
     */
    private $descPicture;

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

    public function getServiceName(): ?string
    {
        return $this->serviceName;
    }

    public function setServiceName(string $serviceName): self
    {
        $this->serviceName = $serviceName;

        return $this;
    }

    public function getServiceDescription(): ?string
    {
        return $this->serviceDescription;
    }

    public function setServiceDescription(string $serviceDescription): self
    {
        $this->serviceDescription = $serviceDescription;

        return $this;
    }

    public function getDescPicture(): ?string
    {
        return $this->descPicture;
    }

    public function setDescPicture(string $descPicture): self
    {
        $this->descPicture = $descPicture;

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
