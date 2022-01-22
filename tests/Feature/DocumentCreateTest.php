<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

class DocumentCreateTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testFileUploadByMaintainer()
    {
		$user = \App\User::find(1);
		$filename = $this->randomString().'.pdf';
		$file = UploadedFile::fake()->create($filename, 100, 'application/pdf');
        $response = $this->actingAs($user)->followingRedirects()->json('POST','/collection/1/upload',
			['collection_id'=>1, 'document'=>$filename,]);
        $response->assertStatus(200);

		// make sure user who does not have permission can not upload here
		$user = \App\User::find(2);
		$filename = $this->randomString().'.pdf';
		$file = UploadedFile::fake()->create($filename, 100, 'application/pdf');
        $response = $this->actingAs($user)->followingRedirects()->json('POST','/collection/1/upload',
			['collection_id'=>1, 'document'=>$filename,]);
        $response->assertStatus(403);
    }

	private function randomString()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randstring = '';
        for ($i = 0; $i < 10; $i++) {
            $randstring .= $characters[rand(0, strlen($characters))];
        }
        return $randstring;
    }
}
