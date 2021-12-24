<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MaintainerAccessTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testMaintainerAccess()
    {
		$user = \App\User::find(1);
		
		// test accessing private collection as the maintainer
        $response = $this->actingAs($user)->get('/collection/3');
        $response->assertStatus(200);
		// test accessing public collection as the maintainer
        $response = $this->actingAs($user)->get('/collection/1');
        $response->assertStatus(200);
    }
	
	public function testAccessToCollectionUsers(){
		$user = \App\User::find(1);
        $response = $this->actingAs($user)->get('/collection/3/users');
        $response->assertStatus(200);
		// non-maintainer should not get access to this area
		$user = \App\User::find(2);
        $response = $this->actingAs($user)->get('/collection/3/users');
        $response->assertStatus(403);
	}

	public function testAccessToMetaFieldManagement(){
		$user = \App\User::find(1);
        $response = $this->actingAs($user)->get('/collection/3/meta');
        $response->assertStatus(200);
		// non-maintainer should not get access to this area
		$user = \App\User::find(2);
        $response = $this->actingAs($user)->get('/collection/3/meta');
        $response->assertStatus(403);
	}

	/*
	public function testAccessToSettings(){
		$user = \App\User::find(1);
        $response = $this->actingAs($user)->get('/collection/3/settings');
        $response->assertStatus(200);
		// non-maintainer should not get access to this area
		$user = \App\User::find(2);
        $response = $this->actingAs($user)->get('/collection/3/settings');
        $response->assertStatus(403);
	}
	*/

	public function testAccessToUpload(){
		$user = \App\User::find(1);
        $response = $this->actingAs($user)->get('/collection/3/upload');
        $response->assertStatus(200);
		// non-maintainer should not get access to this area
		$user = \App\User::find(2);
        $response = $this->actingAs($user)->get('/collection/3/upload');
        $response->assertStatus(403);
	}

	public function testAccessToMetaFilters(){
		$user = \App\User::find(1);
        $response = $this->actingAs($user)->get('/collection/3/metafilters');
        $response->assertStatus(200);
	}

	public function testAccessToExport(){
		/*
		$user = \App\User::find(1);
        $response = $this->actingAs($user)->get('/collection/3/export');;
		$this->assertTrue(strpos($response->content(), 'csv') !== false);
		*/
		// non-maintainer should not get access to this area
		$user = \App\User::find(2);
        $response = $this->actingAs($user)->get('/collection/3/export');
        $response->assertStatus(403);
	}

	public function testAccessToCollectionUser(){
		$user = \App\User::find(1);
        $response = $this->actingAs($user)->get('/collection/3/user');
        $response->assertStatus(200);
		// non-maintainer should not get access to this area
		$user = \App\User::find(2);
        $response = $this->actingAs($user)->get('/collection/3/user');
        $response->assertStatus(403);
	}

	public function testAccessToCollectionMetaManagement(){
		$user = \App\User::find(1);
        $response = $this->actingAs($user)->get('/collection/3/meta');
        $response->assertStatus(200);
		// non-maintainer should not get access to this area
		$user = \App\User::find(2);
        $response = $this->actingAs($user)->get('/collection/3/meta');
        $response->assertStatus(403);
	}

	public function testMetaFieldCreation(){
		$user = \App\User::find(1);
		$response = $this->actingAs($user)
				->json('POST','/collection/3/meta', 
				['collection_id'=>3,'label' => 'Test Label', 
				'placeholder'=>'TEST LABEL', 
				'type'=>'Text', 'display_order'=>1]);
        $response->assertStatus(200);
		// non-maintainer should not get access to this area
		$user = \App\User::find(2);
		$response = $this->actingAs($user)
				->json('POST','/collection/3/meta', 
				['collection_id'=>3,'label' => 'Test Label', 
				'placeholder'=>'TEST LABEL', 
				'type'=>'Text', 'display_order'=>1]);
        $response->assertStatus(403);
	}

	public function testMetaFieldUpdate(){
		$user = \App\User::find(1);
		$response = $this->actingAs($user)
				->json('POST','/collection/3/meta', 
				['collection_id'=>3, 'meta_field_id'=>1, 
				'label' => 'Test Label new', 'placeholder'=>'TEST LABEL NEW', 
				'type'=>'Date', 'display_order'=>1]);
        $response->assertStatus(200);
		// non-maintainer should not get access to this area
		$user = \App\User::find(2);
		$response = $this->actingAs($user)
				->json('POST','/collection/3/meta', 
				['collection_id'=>3, 'meta_field_id'=>1, 
				'label' => 'Test Label New', 'placeholder'=>'TEST LABEL NEW', 
				'type'=>'Date', 'display_order'=>1]);
        $response->assertStatus(403);
	}
}
