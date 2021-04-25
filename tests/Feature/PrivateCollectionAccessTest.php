<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PrivateCollectionAccessTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testMaintainerAccess()
    {
		$user = \App\User::find(1);
		
        $response = $this->actingAs($user)->get('/collection/3');
        $response->assertStatus(200);
    }
}
