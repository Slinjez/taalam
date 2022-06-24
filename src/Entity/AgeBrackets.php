<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AgeBrackets
 *
 * @ORM\Entity(repositoryClass="App\Repository\AgeBracketsRepository")
 */
class AgeBrackets
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
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=10, nullable=false)
     */
    private $description;

    public function getRecordId(): ?int
    {
        return $this->recordId;
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


}
