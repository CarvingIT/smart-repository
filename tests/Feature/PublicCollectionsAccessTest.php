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
        $response = $this->get('/collection/2');
        $response->assertStatus(200)
			->assertSeeText('Demo web resources');
    }

	public function testAccessOthersPrivateCollection(){
        $response = $this->get('/collection/1');
        $response->assertStatus(403);
	}

	public function testNonExistentCollectionAccess(){
        $response = $this->get('/collection/1000'); // something that is not likely to exist
        $response->assertStatus(403); // purposefully returning 403 status code
	}
}
