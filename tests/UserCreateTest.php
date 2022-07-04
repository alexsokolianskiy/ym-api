<?php

namespace Tests;

use Illuminate\Support\Str;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class UserCreateTest extends TestCase
{
    use DatabaseTransactions;

    public function test_should_success_register_user()
    {
        $params = [
            "first_name" => "test",
            "last_name" => "test",
            "phone" => "123",
            "email" => "test@mail.com",

        ];
        $this->post('/api/user/register', array_merge(
            ["password" => "test"],
            $params
        ));
        $this->seeStatusCode(200);
        $this->seeJsonStructure(
            array_merge(
                ['id'],
                array_keys($params)
            )
        );
        $this->seeJson($params);
    }

    public function test_user_shouldnt_be_create_if_fname_is_too_long()
    {
        $params = [
            "first_name" => Str::random(260),
            "last_name" => "test",
            "phone" => "123",
            "email" => "test@mail.com",

        ];
        $this->post('/api/user/register', array_merge(
            ["password" => "test"],
            $params
        ));
        $this->seeStatusCode(422);
    }

    public function test_user_shouldnt_be_create_if_emailis_wrong_format()
    {
        $params = [
            "first_name" => "test",
            "last_name" => "test",
            "phone" => "123",
            "email" => "testmailcom",

        ];
        $this->post('/api/user/register', array_merge(
            ["password" => "test"],
            $params
        ));
        $this->seeStatusCode(422);
    }
}
