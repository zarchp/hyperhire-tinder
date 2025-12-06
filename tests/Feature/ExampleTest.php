<?php

test('the application root returns not found response', function () {
    $response = $this->get('/');

    $response->assertStatus(404);
});
