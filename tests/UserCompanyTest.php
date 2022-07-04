<?php

namespace Tests;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class UserCompany extends TestCase
{
    use DatabaseTransactions;


    public function test_should_success_show_user_companies()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->get('/api/user/companies');
        $this->seeStatusCode(200);
        $this->seeJsonStructure([

        ]);
    }

    public function test_should_get_401_bcz_user_not_auth()
    {
        $user = User::factory()->create();
        $this->get('/api/user/companies');
        $this->seeStatusCode(401);
    }

    public function test_should_success_add_company_to_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $params = [
            "title" => "test",
            "description" => "test",
            "phone" => "123"
        ];
        $this->post('/api/user/companies', $params);
        $this->seeStatusCode(200);
        $this->seeJsonStructure(array_keys($params));
    }

    public function test_should_fail_bcz_description_too_long()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $params = [
            "title" => "test",
            "description" => Str::random(300),
            "phone" => "123"
        ];
        $this->post('/api/user/companies', $params);
        $this->seeStatusCode(422);
    }


}
