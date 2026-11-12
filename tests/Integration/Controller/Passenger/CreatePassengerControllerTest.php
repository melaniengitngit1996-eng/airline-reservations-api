<?php

it('has create passenger API endpoint', function () {
    $client = $this->createClient();

    $client->jsonRequest('POST', '/api/passengers', []);

    expect($client->getResponse()->getStatusCode())
        ->not->toBe(404);
});

it('creates a passenger with blank fields (400)', function () {
    $client = $this->createClient();
    $client->jsonRequest('POST', '/api/passengers', [
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'email' => '',
        ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(400);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Required fields are missing.');
});

it('creates a passenger with missing fields (400)', function () {
    $client = $this->createClient();
    $client->jsonRequest('POST', '/api/passengers', [
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
        ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(400);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Required fields are missing.');
});

it('creates a passenger with empty fields (400)', function () {
    $client = $this->createClient();
    $client->jsonRequest('POST', '/api/passengers', []);

    expect($client->getResponse()->getStatusCode())
        ->toBe(400);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Required fields are missing.');
});

it('creates a passenger with invalid email address (400)', function () {
    $client = $this->createClient();
    $client->jsonRequest('POST', '/api/passengers', [
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'email' => 'invalid_email',
        ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(400);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Invalid email address.');
});

it('creates a passenger successfully (201)', function () {
    $client = $this->createClient();
    $client->jsonRequest('POST', '/api/passengers', [
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'email' => 'some@email.com',
        ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(201);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['first_name'])->toBe('Juan');
});
