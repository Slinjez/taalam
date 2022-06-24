<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TypeOfTraining
 *
 * @ORM\Table(name="type_of_training")
 * @ORM\Entity(repositoryClass="App\Repository\TypeOfTrainingRepository")
 */
class TypeOfTraining
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
     * @ORM\Column(name="description", type="string", length=100, nullable=false)
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
