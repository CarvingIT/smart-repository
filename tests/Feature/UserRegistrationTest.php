<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserRegistrationTest extends TestCase
{
	use WithFaker;

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
		$faker = $this->faker;
		$password = $faker->password;
		$response = $this->followingRedirects()
				->post('/register', ['name' => $faker->name, 'email'=>$faker->email, 
				'password'=>$password, 'password_confirmation'=>$password]);
        $response->assertStatus(200)->assertSeeText('Verify Your Email Address');
    }

	public function testWrongPassword(){
		$response = $this->json('POST', '/login', ['email'=>'ketan@carvingit.com', 
				'password'=>'jaee23']);
        $response->assertStatus(422);
	}
	public function testLogin(){
		$response = $this->json('POST', '/login', ['email'=>'ketan@carvingit.com', 
				'password'=>'ketan123']);
        $response->assertRedirect('/collections');
	}
}
