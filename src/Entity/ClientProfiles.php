<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ClientProfiles
 *
 * @ORM\Table(name="client_profiles", indexes={@ORM\Index(name="client_id", columns={"client_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\ClientProfilesRepository")
 */
class ClientProfiles
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
     * @var \DateTime
     *
     * @ORM\Column(name="date_of_birth", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $dateOfBirth = 'CURRENT_TIMESTAMP';

    /**
     * @var string
     *
     * @ORM\Column(name="social_link_twitter", type="text", length=65535, nullable=false)
     */
    private $socialLinkTwitter;

    /**
     * @var string
     *
     * @ORM\Column(name="social_link_facebook", type="text", length=65535, nullable=false)
     */
    private $socialLinkFacebook;

    /**
     * @var string
     *
     * @ORM\Column(name="social_link_insta", type="text", length=65535, nullable=false)
     */
    private $socialLinkInsta;

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="text", length=65535, nullable=false)
     */
    private $location;

    /**
     * @var string
     *
     * @ORM\Column(name="nationality", type="text", length=65535, nullable=false)
     */
    private $nationality;

    /**
     * @var string
     *
     * @ORM\Column(name="bio", type="text", length=65535, nullable=false)
     */
    private $bio;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile", type="string", length=50, nullable=false)
     */
    private $mobile;

    /**
     * @var string
     *
     * @ORM\Column(name="education_qualification", type="text", length=65535, nullable=false)
     */
    private $educationQualification;

    /**
     * @var \Clients
     *
     * @ORM\ManyToOne(targetEntity="Clients")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="client_id", referencedColumnName="record_id")
     * })
     */
    private $client;

    public function getRecordId(): ?string
    {
        return $this->recordId;
    }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(\DateTimeInterface $dateOfBirth): self
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    public function getSocialLinkTwitter(): ?string
    {
        return $this->socialLinkTwitter;
    }

    public function setSocialLinkTwitter(string $socialLinkTwitter): self
    {
        $this->socialLinkTwitter = $socialLinkTwitter;

        return $this;
    }

    public function getSocialLinkFacebook(): ?string
    {
        return $this->socialLinkFacebook;
    }

    public function setSocialLinkFacebook(string $socialLinkFacebook): self
    {
        $this->socialLinkFacebook = $socialLinkFacebook;

        return $this;
    }

    public function getSocialLinkInsta(): ?string
    {
        return $this->socialLinkInsta;
    }

    public function setSocialLinkInsta(string $socialLinkInsta): self
    {
        $this->socialLinkInsta = $socialLinkInsta;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getNationality(): ?string
    {
        return $this->nationality;
    }

    public function setNationality(string $nationality): self
    {
        $this->nationality = $nationality;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(string $bio): self
    {
        $this->bio = $bio;

        return $this;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(string $mobile): self
    {
        $this->mobile = $mobile;

        return $this;
    }

    public function getEducationQualification(): ?string
    {
        return $this->educationQualification;
    }

    public function setEducationQualification(string $educationQualification): self
    {
        $this->educationQualification = $educationQualification;

        return $this;
    }

    public function getClient(): ?Clients
    {
        return $this->client;
    }

    public function setClient(?Clients $client): self
    {
        $this->client = $client;

        return $this;
    }


}
