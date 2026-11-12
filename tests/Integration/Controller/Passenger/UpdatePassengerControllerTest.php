<?php

use App\Controller\Passenger\UpdatePassengerController;
use App\Dto\Passengers\UpdatePassengerDto;
use App\Entity\Passenger;
use App\Repository\PassengerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

it('has update passenger API endpoint. (200)', function () {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    $passenger = new Passenger();

    $passengerRepository = $this->createMock(PassengerRepository::class);

    $passengerRepository
        ->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($passenger);

    $entityManager
        ->method('getRepository')
        ->willReturnMap([
            [Passenger::class, $passengerRepository],
        ]);

    $validator
        ->method('validate')
        ->willReturn(new ConstraintViolationList());

    static::getContainer()->set(
        UpdatePassengerController::class,
        new UpdatePassengerController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('PATCH', '/api/passengers/1', [
        'first_name' => 'Juan',
        'last_name' => 'Dela Cruz',
        'email' => 'juan@example.com',
    ]);

    expect($client->getResponse()->getStatusCode())
        ->not->toBe(404);

    expect($client->getResponse()->getStatusCode())
        ->not->toBe(405);
});

it('updates passenger with missing fields. (400)', function () {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    static::getContainer()->set(
        UpdatePassengerController::class,
        new UpdatePassengerController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('PATCH', '/api/passengers/1', []);

    expect($client->getResponse()->getStatusCode())
        ->toBe(400);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Required fields are missing.');
});

it('updates passenger with blank fields. (400)', function () {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    $violation = new ConstraintViolation(
        'This value should not be blank.',
        null,
        [],
        null,
        'firstName',
        ''
    );

    $validator
        ->expects($this->once())
        ->method('validate')
        ->with($this->isInstanceOf(UpdatePassengerDto::class))
        ->willReturn(
            new ConstraintViolationList([$violation])
        );

    static::getContainer()->set(
        UpdatePassengerController::class,
        new UpdatePassengerController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('PATCH', '/api/passengers/1', [
        'first_name' => '',
        'last_name' => '',
        'email' => '',
    ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(400);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('This value should not be blank.');
});

it('updates passenger with unknown passenger. (404)', function () {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    $passengerRepository = $this->createMock(PassengerRepository::class);

    $passengerRepository
        ->expects($this->once())
        ->method('find')
        ->with(999)
        ->willReturn(null);

    $entityManager
        ->method('getRepository')
        ->willReturnMap([
            [Passenger::class, $passengerRepository],
        ]);

    $validator
        ->method('validate')
        ->willReturn(new ConstraintViolationList());

    static::getContainer()->set(
        UpdatePassengerController::class,
        new UpdatePassengerController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('PATCH', '/api/passengers/999', [
        'first_name' => 'Juan',
        'last_name' => 'Dela Cruz',
        'email' => 'juan@example.com',
    ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(404);
});

it('updates passenger with duplicate email. (409)', function () {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    $passenger = new Passenger();

    $passenger->setFirstName('Pedro');
    $passenger->setLastName('Santos');
    $passenger->setEmail('pedro@example.com');

    $passengerRepository = $this->createMock(PassengerRepository::class);

    $passengerRepository
        ->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($passenger);

    $entityManager
        ->method('getRepository')
        ->willReturnMap([
            [Passenger::class, $passengerRepository],
        ]);

    $violation = new ConstraintViolation(
        'This email is already used.',
        null,
        [],
        $passenger,
        'email',
        'duplicate@example.com'
    );

    $validator
        ->expects($this->exactly(2))
        ->method('validate')
        ->willReturnOnConsecutiveCalls(
            new ConstraintViolationList(),
            new ConstraintViolationList([$violation])
        );

    $entityManager
        ->expects($this->never())
        ->method('flush');

    static::getContainer()->set(
        UpdatePassengerController::class,
        new UpdatePassengerController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('PATCH', '/api/passengers/1', [
        'first_name' => 'Pedro',
        'last_name' => 'Santos',
        'email' => 'duplicate@example.com',
    ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(409);
});

it('updates passenger successfully. (200)', function () {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    $passenger = new Passenger();

    $passenger->setFirstName('Juana');
    $passenger->setLastName('Dela Cruz');
    $passenger->setEmail('juana@email.com');

    $passengerRepository = $this->createMock(PassengerRepository::class);

    $passengerRepository
        ->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($passenger);

    $entityManager
        ->method('getRepository')
        ->willReturnMap([
            [Passenger::class, $passengerRepository],
        ]);

    $validator
        ->expects($this->exactly(2))
        ->method('validate')
        ->willReturnOnConsecutiveCalls(
            new ConstraintViolationList(),
            new ConstraintViolationList()
        );

    $entityManager
        ->expects($this->once())
        ->method('flush');

    static::getContainer()->set(
        UpdatePassengerController::class,
        new UpdatePassengerController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest(
        'PATCH',
        '/api/passengers/1',
        [
            'first_name' => 'Juan',
            'last_name' => 'Santos',
            'email' => 'pedro@email.com',
        ]
    );

    expect($client->getResponse()->getStatusCode())
        ->toBe(200);
});