<?php

namespace App\DataFixtures;

use App\Entity\Flight;
use App\Entity\Seat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SeatFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public static function getGroups(): array
    {
        return ['seats'];
    }

    public function getDependencies(): array
    {
        return [
            FlightFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $flightRepository = $manager->getRepository(Flight::class);
        $seatRepository = $manager->getRepository(Seat::class);

        // Six seats per row, five rows = 30 seats per flight.
        $columns = ['A', 'B', 'C', 'D', 'E', 'F'];

        $flights = $flightRepository->findAll();

        foreach ($flights as $flight) {
            for ($row = 1; $row <= 5; $row++) {
                foreach ($columns as $column) {
                    $code = $row . $column;

                    // Avoid duplicate seats when rerunning fixtures.
                    $existingSeat = $seatRepository->findOneBy([
                        'flight' => $flight,
                        'code' => $code,
                    ]);

                    if ($existingSeat) {
                        continue;
                    }

                    $seat = new Seat();
                    $seat->setFlight($flight);
                    $seat->setCode($code);

                    $manager->persist($seat);
                }
            }
        }

        $manager->flush();
    }
}
