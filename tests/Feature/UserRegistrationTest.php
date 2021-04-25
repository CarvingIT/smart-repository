<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserRegistrationTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
	public function testSmallPassword(){
		putenv("ENABLE_REGISTRATION=1");
		$response = $this->json('POST', '/register', ['name' => 'Jaee', 'email'=>'jaee404@gmail.com', 
				'password'=>'jaee12', 'password_confirmation'=>'jaee12']);
        $response->assertStatus(422);
	}

	public function testDifferentConfirmationPassword(){
		putenv("ENABLE_REGISTRATION=1");
		$response = $this->json('POST', '/register', ['name' => 'Jaee', 'email'=>'jaee404@gmail.com', 
				'password'=>'jaee12345', 'password_confirmation'=>'jaee1234567']);
        $response->assertStatus(422);
	}

    public function testRegistration()
    {
		putenv("ENABLE_REGISTRATION=1");
		$response = $this->followingRedirects()
				->post('/register', ['name' => 'Jaee', 'email'=>'jaee404@gmail.com', 
				'password'=>'jaee1234', 'password_confirmation'=>'jaee1234']);
        $response->assertStatus(200)->assertSeeText('Verify Your Email Address');
    }

	public function testWrongPassword(){
		$response = $this->json('POST', '/login', ['email'=>'jaee404@gmail.com', 
				'password'=>'jaee23']);
        $response->assertStatus(422);
	}
	public function testLogin(){
		$response = $this->json('POST', '/login', ['email'=>'jaee404@gmail.com', 
				'password'=>'jaee1234']);
        $response->assertRedirect('/collections');
	}
}
