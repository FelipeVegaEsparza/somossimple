<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_raiz_muestra_la_landing_publica(): void
    {
        $this->get('/')->assertOk()->assertSee('Tu negocio tiene necesidades');
    }
}
