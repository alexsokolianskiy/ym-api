<?php

namespace Tests;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class UserLoginTest extends TestCase
{
    use DatabaseTransactions;


    public function test_should_success_if_email_password_isok()
    {
        $password = "secret";
        $user = User::factory()->create([
            'password' => Hash::make($password)
        ]);
        $this->post('/api/user/signin', [
            "email" => $user->email,
            "password" => $password
        ]);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'token'
        ]);
    }

    public function test_should_get_forbidden_exception_without_user_with_such_credentials()
    {
        $params = [
            "email" => "test@mail.com",
            "password" => "test"

        ];
        $this->post('/api/user/signin', $params);
        $this->seeStatusCode(403);
    }
}
