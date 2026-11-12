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

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $airport = $this->airportService->find($id);

        if (!$airport) {
            return new JsonResponse(['message' => 'Airport not found'], 404);
        }

        return new JsonResponse([
            'airport_id' => $airport->getId(),
            'code' => $airport->getCode(),
            'name' => $airport->getName(),
            'city' => $airport->getCity(),
            'country' => $airport->getCountry(),
        ]);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        if (!$this->airportService->delete($id)) {
            return new JsonResponse(['message' => 'Airport not found'], 404);
        }

        return new JsonResponse(null, 204);
    }
}