<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Service\TrustService\Enum\TrustStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const USER_REFERENCE = 'user';
    
    public function __construct(protected UserPasswordHasherInterface $userPasswordHasher) {}

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setEmail("test$i@example.com");
            $user->setRoles(['faker']);

            $password = $this->userPasswordHasher->hashPassword($user, 'qwerty');
            $user->setPassword($password);
            $user->setTrustStatus(TrustStatus::ACTIVE_ID->value);
            $manager->persist($user);

            $this->addReference(self::USER_REFERENCE . '_' . $i, $user);
        }

        $manager->flush();
    }
}
