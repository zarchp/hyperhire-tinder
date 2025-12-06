<?php

declare(strict_types=1);

test('the application root returns not found response', function () {
    $response = $this->get('/');

    $response->assertStatus(404);
});
