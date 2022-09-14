<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class IsSiteWorkingProperly extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_example() {        
       $response = $this->get('/');
       $response->assertStatus(200);
    }
}