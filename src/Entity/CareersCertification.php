<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CareersCertification
 *
 * @ORM\Table(name="careers_certification")
 * @ORM\Entity
 */
class CareersCertification
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
     * @ORM\Column(name="career_id", type="bigint", nullable=false)
     */
    private $careerId;

    /**
     * @var string
     *
     * @ORM\Column(name="certification", type="text", length=65535, nullable=false)
     */
    private $certification;

    /**
     * @var string
     *
     * @ORM\Column(name="level", type="text", length=65535, nullable=false)
     */
    private $level;

    public function getRecordId(): ?string
    {
        return $this->recordId;
    }

    public function getCareerId(): ?string
    {
        return $this->careerId;
    }

    public function setCareerId(string $careerId): self
    {
        $this->careerId = $careerId;

        return $this;
    }

    public function getCertification(): ?string
    {
        return $this->certification;
    }

    public function setCertification(string $certification): self
    {
        $this->certification = $certification;

        return $this;
    }

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function setLevel(string $level): self
    {
        $this->level = $level;

        return $this;
    }


}
