<?php

namespace App\DataFixtures;

use App\Entity\Airport;
use App\Entity\Flight;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class FlightFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public static function getGroups(): array
    {
        return ['flights'];
    }

    public function getDependencies(): array
    {
        return [
            AirportFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $airportRepository = $manager->getRepository(Airport::class);

        $manila = $airportRepository->findOneBy(['code' => 'MNL']);
        $cebu = $airportRepository->findOneBy(['code' => 'CEB']);
        $davao = $airportRepository->findOneBy(['code' => 'DVO']);
        $iloilo = $airportRepository->findOneBy(['code' => 'ILO']);
        $kalibo = $airportRepository->findOneBy(['code' => 'KLO']);

        $flights = [
            [
                'flightNumber' => '5J 561',
                'capacity' => 10,
                'origin' => $manila,
                'destination' => $cebu,
                'departure' => '2026-11-10T06:00:00+08:00',
                'arrival' => '2026-11-10T07:30:00+08:00',
            ],
            [
                'flightNumber' => '5J 971',
                'capacity' => 10,
                'origin' => $manila,
                'destination' => $davao,
                'departure' => '2026-11-10T08:00:00+08:00',
                'arrival' => '2026-11-10T10:00:00+08:00',
            ],
            [
                'flightNumber' => '5J 448',
                'capacity' => 10,
                'origin' => $cebu,
                'destination' => $manila,
                'departure' => '2026-11-10T11:00:00+08:00',
                'arrival' => '2026-11-10T12:30:00+08:00',
            ],
            [
                'flightNumber' => '5J 464',
                'capacity' => 10,
                'origin' => $manila,
                'destination' => $iloilo,
                'departure' => '2026-11-11T09:00:00+08:00',
                'arrival' => '2026-11-11T10:15:00+08:00',
            ],
            [
                'flightNumber' => '5J 335',
                'capacity' => 10,
                'origin' => $manila,
                'destination' => $kalibo,
                'departure' => '2026-11-11T13:00:00+08:00',
                'arrival' => '2026-11-11T14:10:00+08:00',
            ],
        ];

        foreach ($flights as $flightData) {
            $flight = new Flight();

            $flight->setFlightNumber($flightData['flightNumber']);
            $flight->setCapacity($flightData['capacity']);
            $flight->setOriginAirport($flightData['origin']);
            $flight->setDestinationAirport($flightData['destination']);
            $flight->setDepartureTime(
                new \DateTimeImmutable($flightData['departure'])
            );
            $flight->setArrivalTime(
                new \DateTimeImmutable($flightData['arrival'])
            );

            $manager->persist($flight);
        }

        $manager->flush();
    }
}
