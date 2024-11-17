<?php

namespace App\Entity;

use App\Repository\PhoneNumberRepository\PhoneNumberRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PhoneNumberRepository::class)]
#[ORM\Table(name: '`phone_numbers`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_PHONE_NUMBER', fields: ['phone_number'])]
class PhoneNumber
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(name: 'phone_number', length: 50)]
    public ?string $phone_number = null;

    #[ORM\Column(unique: true)]
    #[ORM\JoinColumn(name: 'user_id')]
    private ?int $userId = null;

    #[ORM\Column]
    private ?int $trust_status = null;

    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'phoneNumber', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private User $user;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getPhoneNumber(): ?string
    {
        return $this->phone_number;
    }

    /**
     * @param string|null $phoneNumber
     *
     * @return void
     */
    public function setPhoneNumber(?string $phoneNumber): void
    {
        $this->phone_number = $phoneNumber;
    }

    public function getTrustStatus(): ?int
    {
        return $this->trust_status;
    }

    public function setTrustStatus(?int $trust_status): void
    {
        $this->trust_status = $trust_status;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }
}
