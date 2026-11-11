<?php

namespace App\Controller;

use App\Entity\Passenger;
use App\Entity\Reservation;
use App\Entity\ReservationPassenger;
use App\Entity\Seat;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractController {
    /**
     * Entity manager for persisting the entity.
     */
    public EntityManagerInterface $entityManager;

    /**
     * Validator for validating DTOs and entities.
     */
    public ValidatorInterface $validator;

    /**
     * Object mapper for mapping DTOs to entities.
     */
    public ObjectMapperInterface $objectMapper;

    /**
     * Creates a new AbstractController instance.
     *
     * @param EntityManagerInterface $entityManager Entity manager for database operations.
     * @param ValidatorInterface $validator Validator for DTO and entity validation.
     * @param ObjectMapperInterface $objectMapper Object mapper for DTO-to-entity mapping.
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        ObjectMapperInterface $objectMapper
    )
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->objectMapper = $objectMapper;
    }

    /**
     * Creates a successful JSON response.
     *
     * @param array $data The data to include in the response.
     * @param int $status The HTTP status code for the response.
     *
     * @return JsonResponse The JSON response containing the provided data.
     */
    protected function successResponse(
        ?array $data,
        int $status = 200
    ): JsonResponse {
        return new JsonResponse($data, $status);
    }

    /**
     * Creates an error JSON response.
     *
     * @param string $message The error message to include in the response.
     * @param int $status The HTTP status code for the response.
     * @param string|null $errors Optional validation or error details.
     *
     * @return JsonResponse The JSON response containing the error information.
     */
    protected function errorResponse(
        string $message,
        int $status,
        ?string $errors = null
    ): JsonResponse {
        $response = [
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return new JsonResponse($response, $status);
    }

    /**
     * Returns true if seat is taken
     *
     * @param Seat $seat The seat to be checked.
     *
     * @return boolean Returns `true` if already taken. 
     */
    public function isTaken(Seat $seat): bool
    {
        $reservationPassengerRepository = $this->entityManager->getRepository(ReservationPassenger::class);

        return $reservationPassengerRepository->isTaken($seat);
    }

    /**
     * Adds passenger to an existing reservation
     *
     * @param Reservation $reservation The reservation of the passengers.
     * @param array $passengers The passengers of the flight reservation
     *
     * @return JsonResponse The JSON response containing the error information.
     */
    public function addReservationPassenger(Reservation $reservation, array $passengers): ?JsonResponse {
        $passengerRepository = $this->entityManager->getRepository(Passenger::class);
        $seatRepository = $this->entityManager->getRepository(Seat::class);

        foreach ($passengers as $passenger) {
            $reservationPassenger = new ReservationPassenger();

            $foundPassenger = $passengerRepository->find($passenger['passenger_id']);
            $foundSeat = $seatRepository->find($passenger['seat_id']);

            if (!$foundPassenger) {
                return $this->errorResponse(
                    'Unknown passenger.',
                    404
                );
            }

            if (!$foundSeat) {
                return $this->errorResponse(
                    'Unknown seat.',
                    404
                );
            }

            if ($this->isTaken($foundSeat)) {
                return $this->errorResponse(
                    'Seat already taken.',
                    409
                );
            }

            $reservationPassenger->setPassenger($foundPassenger);
            $reservationPassenger->setSeat($foundSeat);

            $reservation->addReservationPassenger($reservationPassenger);

            $this->entityManager->persist($reservationPassenger);
        }

        return null;
    }
}