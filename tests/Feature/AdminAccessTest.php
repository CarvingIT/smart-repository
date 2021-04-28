<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminAccessTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUserManagementAccess()
    {
		$user = \App\User::find(1);
		// test accessing private collection as the maintainer
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
}
