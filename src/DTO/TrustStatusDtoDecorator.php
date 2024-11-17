<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class TrustStatusDtoDecorator
{
    #[Assert\NotNull(message: "The user wasn't registered.")]
    public ?TrustStatusDto $trustStatusDto;

    /**
     * @param \App\DTO\TrustStatusDto|null $trustStatusDto
     */
    public function __construct(?TrustStatusDto $trustStatusDto)
    {
        $this->trustStatusDto = $trustStatusDto;
    }

    /**
     * @return \App\DTO\TrustStatusDto|null
     */
    public function getTrustStatusDto(): ?TrustStatusDto
    {
        return $this->trustStatusDto;
    }

    /**
     * @param \App\DTO\TrustStatusDto|null $trustStatusDto
     *
     * @return void
     */
    public function setTrustStatusDto(?TrustStatusDto $trustStatusDto): void
    {
        $this->trustStatusDto = $trustStatusDto;
    }
}
