<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Prueba el endpoint público /health del contrato de la API.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/health');

        $response->assertStatus(200);
        $response->assertJson(['status' => 'ok']);
    }
}
