<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Tests\TestCase;


class UploadTest extends TestCase
{
    /**
     * @test
     */
    public function upload_csv()
    {
        $file = new UploadedFile(
            base_path('public\uploads\contacts.csv'), 'contacts.csv', 'text/csv', 13071, null, false);

        $response = $this->withHeaders([
            'X-Header' => 'application/x-www-form-urlencoded',
        ])->json('POST', '/create', [
            "first_name" => "first_name",
            "last_name" => "last_name",
            "email" => "email",
            "phone" => "phone",
            "file" => $file,
            "team_id" => "team_id",
            "unsubscribed_status" => "unsubscribed_status",
            "sticky_phone_number_id" => "sticky_phone_number_id",
            "twitter_id" => "twitter_id",
            "fb_messenger_id" => "fb_messenger_id",
            "time_zone" => "time_zone",
        ]);

        $response
            ->assertStatus(201)
            ->assertJson([
                'created' => true,
            ]);
    }
}
