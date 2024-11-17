<?php

namespace App\DataFixtures;

use App\Entity\PhoneNumber;
use App\Service\TrustService\Enum\TrustStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PhoneNumberFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $phoneNumber = new PhoneNumber();
            $phoneNumberString = "+38098111111$i";
            if ($i === 10) {
                $phoneNumberString = '+380981111110';
            }

            $phoneNumber->setPhoneNumber($phoneNumberString);
            $phoneNumber->setTrustStatus(TrustStatus::ACTIVE_ID->value);
            $user = $this->getReference(UserFixtures::USER_REFERENCE . '_' . $i);
            $phoneNumber->setUser($user);
            
            $manager->persist($phoneNumber);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
