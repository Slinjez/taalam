<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TrainerFiles
 *
 * @ORM\Table(name="trainer_files", indexes={@ORM\Index(name="client_id", columns={"client_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\TrainerFilesRepository")
 */
class TrainerFiles
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
     * @ORM\Column(name="client_id", type="bigint", nullable=false)
     */
    private $clientId;

    /**
     * @var string
     *
     * @ORM\Column(name="file_path", type="text", length=65535, nullable=false)
     */
    private $filePath;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="upload_date", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $uploadDate = 'CURRENT_TIMESTAMP';

    public function getRecordId(): ?string
    {
        return $this->recordId;
    }

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): self
    {
        $this->clientId = $clientId;

        return $this;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(string $filePath): self
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function getUploadDate(): ?\DateTimeInterface
    {
        return $this->uploadDate;
    }

    public function setUploadDate(\DateTimeInterface $uploadDate): self
    {
        $this->uploadDate = $uploadDate;

        return $this;
    }


}
