<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminAccessTest extends TestCase
{
	use WithFaker;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUserManagementAccess()
    {
		$user = \App\User::find(1);
		// test accessing user management as admin
        $response = $this->actingAs($user)->get('/admin/usermanagement');
        $response->assertStatus(200);
		// non-admin should not get access
		$user = \App\User::find(2);
        $response = $this->actingAs($user)->get('/admin/usermanagement');
        $response->assertStatus(403);
    }

	public function testCollectionManagementAccess(){
		$user = \App\User::find(1);
		// test accessing private collection as the maintainer
        $response = $this->actingAs($user)->get('/admin/collectionmanagement');
        $response->assertStatus(200);
		// non-admin should not get access
		$user = \App\User::find(2);
        $response = $this->actingAs($user)->get('/admin/collectionmanagement');
        $response->assertStatus(403);
	}

	public function testSystemConfigAccess(){
		$user = \App\User::find(1);
		// test accessing private collection as the maintainer
        $response = $this->actingAs($user)->get('/admin/sysconfig');
        $response->assertStatus(200);
		// non-admin should not get access
		$user = \App\User::find(2);
        $response = $this->actingAs($user)->get('/admin/sysconfig');
        $response->assertStatus(403);
	}

	public function testReportsAccess(){
		$user = \App\User::find(1);
		// test accessing private collection as the maintainer
        $response = $this->actingAs($user)->get('/reports');
        $response->assertStatus(200);
		// non-admin should not get access
		$user = \App\User::find(2);
        $response = $this->actingAs($user)->get('/reports');
        $response->assertStatus(403);
	}

	public function testUserCreation(){
		$user = \App\User::find(1);
		$faker = $this->faker;
		$response = $this->actingAs($user)->followingRedirects()
				->json('POST','/user', 
				['name'=>$faker->name,'email' => $faker->email, 
				'_method' => 'post',
				'password'=>'S0mePasswd@', 
				'password_confirmation'=>'S0mePasswd@']);
        $response->assertStatus(200)->assertSeeText('Users');
		// non-admin should not get access
		$user = \App\User::find(2);
        $response = $this->actingAs($user)->get('/user/create');
        $response->assertStatus(403);
	}

	public function testUserUpdate(){
		$user = \App\User::find(1);
		$faker = $this->faker;
		$response = $this->actingAs($user)->followingRedirects()
				->json('POST','/user/4', 
				['name'=>$faker->name,'email' => $faker->email, 
				'_method' => 'put',
				'password'=>'S0mePasswd@', 
				'password_confirmation'=>'S0mePasswd@']);
        $response->assertStatus(200)->assertSeeText('Users');
		// non-admin should not get access
		$user = \App\User::find(2);
        $response = $this->actingAs($user)->get('/user/4/edit');
        $response->assertStatus(403);
	}

	public function testCollectionCreation(){
		$user = \App\User::find(1);
		$response = $this->actingAs($user)->followingRedirects()
				->json('POST','/admin/savecollection', 
				['collection_name'=>'test collection','description' => 'test description', 
				'content_type' => 'Uploaded documents',
				'storage_drive'=>'local', 
				'require_approval'=>0, 
				'maintainer'=>'ketan@carvingit.com']);
        $response->assertStatus(200);
		// another collection for testing .. a public collection
		$response = $this->actingAs($user)->followingRedirects()
				->json('POST','/admin/savecollection', 
				['collection_name'=>'test public collection','description' => 'some description', 
				'content_type' => 'Uploaded documents',
				'storage_drive'=>'local', 
				'require_approval'=>0, 
				'maintainer'=>'ketan@carvingit.com']);
        $response->assertStatus(200);
		// non-admin should not get access
		$user = \App\User::find(2);
        $response = $this->actingAs($user)->get('/admin/collection-form/new');
        $response->assertStatus(403);
	}

	public function testCollectionUpdate(){
		$user = \App\User::find(1);
		$response = $this->actingAs($user)->followingRedirects()
				->json('POST','/admin/savecollection', 
				['collection_name'=>'test collection 4','description' => 'test 3 description', 
				'collection_id'=>1,
				'content_type' => 'Uploaded documents',
				'storage_drive'=>'local', 
				'collection_type'=>'Members only',
				'require_approval'=>0, 
				'maintainer'=>'shraddha@carvingit.com']);
        $response->assertStatus(200);
		// non-admin should not get access
		$user = \App\User::find(2);
        $response = $this->actingAs($user)->get('/admin/collection-form/1');
        $response->assertStatus(403);
	}
}
