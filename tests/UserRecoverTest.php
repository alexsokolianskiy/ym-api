<?php

namespace Tests;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class UserRecoverTest extends TestCase
{
    use DatabaseTransactions;


    public function test_should_success_send_reset_token_if_email_exists()
    {
        $password = "secret";
        $user = User::factory()->create([
            'password' => Hash::make($password)
        ]);
        $this->post('/api/user/recover-password', [
            "email" => $user->email,
            "password" => $password
        ]);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'token'
        ]);
    }

    public function test_should_success_send_reset_token_and_reset_password_if_email_exists()
    {
        $password = "secret";
        $user = User::factory()->create([
            'password' => Hash::make($password)
        ]);
        $result = $this->post('/api/user/recover-password', [
            "email" => $user->email,
            "password" => $password
        ])->response->decodeResponseJson();

        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'token'
        ]);


        $this->patch('/api/user/recover-password', [
            "token" => $result["token"],
            "password" => "test"
        ]);
        $this->seeStatusCode(200);
        //refresh model before fetch
        $user->refresh();
        $this->assertTrue(Hash::check("test", $user->password));
    }


    public function test_should_success_send_reset_token_and_fail_reset_password_bcz_token_incorrect()
    {
        $password = "secret";
        $user = User::factory()->create([
            'password' => Hash::make($password)
        ]);
        $this->post('/api/user/recover-password', [
            "email" => $user->email,
            "password" => $password
        ]);

        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'token'
        ]);


        $this->patch('/api/user/recover-password', [
            "token" => "test",
            "password" => "test"
        ]);
        $this->seeStatusCode(403);
    }



    public function test_should_get_validate_exception_if_email_is_missing()
    {
        $params = [
            "email" => "test@mail.com",
            "password" => "test"

        ];
        $this->post('/api/user/recover-password', $params);
        $this->seeStatusCode(422);
    }
}
