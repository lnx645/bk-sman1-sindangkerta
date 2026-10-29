<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Rute / memang sengaja mengalihkan ke halaman login (bukan 200),
     * karena aplikasi ini tidak punya halaman publik — semua halaman
     * di balik login. Tes scaffold bawaan Laravel disesuaikan ke
     * perilaku yang dirancang ini (bukan bug aplikasi).
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }
}
