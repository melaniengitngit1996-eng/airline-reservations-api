<?php

namespace App\DataFixtures;

use App\Entity\Airport;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class AirportFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['airports'];
    }

    public function load(ObjectManager $manager): void
    {
        $airports = [
            [
                'code' => 'MNL',
                'name' => 'Ninoy Aquino International Airport',
                'city' => 'Manila',
                'country' => 'Philippines',
            ],
            [
                'code' => 'CEB',
                'name' => 'Mactan-Cebu International Airport',
                'city' => 'Cebu',
                'country' => 'Philippines',
            ],
            [
                'code' => 'DVO',
                'name' => 'Francisco Bangoy International Airport',
                'city' => 'Davao',
                'country' => 'Philippines',
            ],
            [
                'code' => 'ILO',
                'name' => 'Iloilo International Airport',
                'city' => 'Iloilo',
                'country' => 'Philippines',
            ],
            [
                'code' => 'KLO',
                'name' => 'Kalibo International Airport',
                'city' => 'Kalibo',
                'country' => 'Philippines',
            ],
        ];

        foreach ($airports as $airportData) {
            $airport = new Airport();
            $airport->setCode($airportData['code']);
            $airport->setName($airportData['name']);
            $airport->setCity($airportData['city']);
            $airport->setCountry($airportData['country']);

            $manager->persist($airport);
        }

        $manager->flush();
    }
}
