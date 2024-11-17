<?php

namespace App\Entity;

use App\Repository\BanLogRepository\BanLogRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BanLogRepository::class)]
#[ORM\Table(name: "ban_log")]
class BanLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private string $id;

    #[ORM\Column(type: "string", length: 255)]
    private string $bannedId;

    #[ORM\Column(type: "string", length: 255)]
    private string $bannedType;

    #[ORM\Column(type: "string", length: 255)]
    private string $reason;

    #[ORM\Column(type: "datetime")]
    private DateTime $bannedAt;

    /**
     * @return string
     */
    public function getBannedId(): string
    {
        return $this->bannedId;
    }

    /**
     * @param string $bannedId
     *
     * @return $this
     */
    public function setBannedId(string $bannedId): self
    {
        $this->bannedId = $bannedId;

        return $this;
    }

    /**
     * @return string
     */
    public function getBannedType(): string
    {
        return $this->bannedType;
    }

    /**
     * @param string $bannedType
     *
     * @return $this
     */
    public function setBannedType(string $bannedType): self
    {
        $this->bannedType = $bannedType;

        return $this;
    }

    /**
     * @return string
     */
    public function getReason(): string
    {
        return $this->reason;
    }

    /**
     * @param string $reason
     *
     * @return $this
     */
    public function setReason(string $reason): self
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getBannedAt(): DateTime
    {
        return $this->bannedAt;
    }

    /**
     * @param \DateTime $bannedAt
     *
     * @return $this
     */
    public function setBannedAt(DateTime $bannedAt): self
    {
        $this->bannedAt = $bannedAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return void
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }
}
