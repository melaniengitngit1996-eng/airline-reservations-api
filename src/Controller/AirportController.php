<?php

namespace App\Controller;

use App\Service\AirportServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/airports')]
class AirportController
{
    public function __construct(private AirportServiceInterface $airportService) {}

    #[Route('', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $code = $request->query->get('code');
        $name = $request->query->get('name');

        $airports = $this->airportService->findAll($code, $name);

        return new JsonResponse(array_map(
            fn ($airport) => [
                'airport_id' => $airport->getId(),
                'code' => $airport->getCode(),
                'name' => $airport->getName(),
                'city' => $airport->getCity(),
                'country' => $airport->getCountry(),
            ],
            $airports
        ));
    }
}