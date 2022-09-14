<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class testLoginApiFeatureNegativeScenario extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {        
        $user = [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'password_confirmation' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        ];
        $response = $this->post('/api/auth/register', $user);
        if($response->assertStatus(200)) {
            $user['password'] = '12345678'; //Modified password as to Check negative scenario
            $response = $this->post('/api/auth/login', $user);
            $response->assertJsonPath('status', false);//Checking for status=false
            // $response->assertJsonPath('status', true);//Checking for status=false
        }
    }
}
