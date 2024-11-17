<?php

namespace App\Entity;

use App\Repository\ClientIpRepository\ClientIpRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientIpRepository::class)]
#[ORM\Table(name: '`client_ip`')]
class ClientIp
{
    #[ORM\Id]
    #[ORM\Column(length: 15)]
    private ?string $client_ip = null;

    #[ORM\Column]
    private ?int $trust_status = null;

    /**
     * @return string|null
     */
    public function getClientIp(): ?string
    {
        return $this->client_ip;
    }

    /**
     * @param string|null $clientIp
     *
     * @return void
     */
    public function setClientIp(?string $clientIp): void
    {
        $this->client_ip = $clientIp;
    }

    /**
     * @return int|null
     */
    public function getTrustStatus(): ?int
    {
        return $this->trust_status;
    }

    /**
     * @param int|null $trustStatus
     *
     * @return void
     */
    public function setTrustStatus(?int $trustStatus): void
    {
        $this->trust_status = $trustStatus;
    }
}
