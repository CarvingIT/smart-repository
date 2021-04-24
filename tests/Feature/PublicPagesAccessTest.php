<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PublicPagesAccessTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testAccess()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response = $this->get('/collections');
        $response->assertStatus(200);
        $response = $this->get('/documents');
        $response->assertStatus(200);
        $response = $this->get('/contact');
        $response->assertStatus(200);
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response = $this->get('/password/reset');
        $response->assertStatus(200);
		if(env('ENABLE_REGISTRATION') == 1){
			// do nothing
		}
		else{
			putenv("ENABLE_REGISTRATION=1");
		}
       	$response = $this->get('/register');
       	$response->assertStatus(200);
    }
}
