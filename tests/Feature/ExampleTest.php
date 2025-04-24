<?php

it('returns a successful response', function () {
    $response = test()->get('/');

    $response->assertStatus(200);
});
