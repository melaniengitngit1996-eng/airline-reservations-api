<?php

it('creates a airport successfully (201)', function () {
    $client = $this->createClient();
    $client->jsonRequest('POST', '/api/airports', [
        'code' => 'ABC',
        'name' => 'Some Airport',
        'city' => 'Some City',
        'country' => 'Philippines',
    ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(201);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['code'])->toBe('ABC');
});

it('creates an airport with duplicate code (409)', function() {
    $client = $this->createClient();
    $client->jsonRequest('POST', '/api/airports', [
        'code' => 'ABC',
        'name' => 'Some Airport',
        'city' => 'Some City',
        'country' => 'Philippines',
    ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(409);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Airport code already exists.');
});

it('creates an airport with blank fields (400)', function() {
    $client = $this->createClient();
    $client->jsonRequest('POST', '/api/airports', [
        'code' => 'ABC',
        'name' => 'Some Airport',
        'city' => 'Some City',
        'country' => '',
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

it('creates an airport with missing fields (400)', function() {
    $client = $this->createClient();
    $client->jsonRequest('POST', '/api/airports', [
        'code' => 'ABC',
        'name' => 'Some Airport',
        'city' => 'Some City',
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

it('creates an airport with empty fields (400)', function() {
    $client = $this->createClient();
    $client->jsonRequest('POST', '/api/airports', []);

    expect($client->getResponse()->getStatusCode())
        ->toBe(400);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Required fields are missing.');
});