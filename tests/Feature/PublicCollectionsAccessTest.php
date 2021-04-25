<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PublicCollectionsAccessTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testAccessPublicCollections()
    {
        $response = $this->get('/collection/1');
        $response->assertStatus(200)
			->assertSeeText('Misc documents');
        $response = $this->get('/collection/2');
        $response->assertStatus(200)
			->assertSeeText('Demo web resources');
    }

	public function testAccessOthersPrivateCollection(){
        $response = $this->get('/collection/3');
        $response->assertStatus(403);
	}
}
