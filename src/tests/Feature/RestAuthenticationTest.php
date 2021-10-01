<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesApplication;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RestAuthenticationTest extends TestCase
{
    use CreatesApplication;
    use DatabaseMigrations;
    use RefreshDatabase;
    private $user = ["name"=>"alex","email"=>"alecuba16@gmail.com", "password"=>"alex"];
    private $responseStructure = [
        "success",
        "data" => [
            "token",
            "name"
        ],
        "message"
    ];
    /**
     * The user is able to register into the system.
     */
    public function test_user_is_able_to_register()
    {

        $response = $this->json('POST', '/api/register',$this->user,['Accept'=>'application/json']);
        $response->assertStatus(200);
        $response->assertJsonStructure($this->responseStructure);
        $response->assertJson([
            'success'=>true,
            'data' => [
                'name'=>$this->user["name"]
            ]
        ]);
        $userDb = User::find(1);
        $this->assertEquals($this->user["name"],$userDb->name);
        $this->assertEquals($this->user["email"],$userDb->email);
    }

    /**
     * The user is able to log-in into the system.
     */
    public function test_user_is_able_to_login()
    {
        $this->registerUser($this->user);

        $response = $this->json('POST', '/api/login',$this->user,['Accept'=>'application/json']);

        $response->assertStatus(200);
        $response->assertJsonStructure($this->responseStructure);
        $response->assertJson([
            'success'=>true,
            'data' => [
                'name'=>$this->user["name"]
            ]
        ]);
    }

    /**
     * The user is able to log-in into the system.
     */
    public function test_user_is_able_to_logout()
    {
        $responseStructure = [
            "success",
            "message"
        ];
        $userObj=$this->registerUser($this->user);
        $this->actingAs($userObj);

        $response = $this->json('POST', '/api/login',$this->user,['Accept'=>'application/json']);
        $token=$response->decodeResponseJson()["data"]["token"];
        $response = $this->json('GET', '/api/logout',['Accept'=>'application/json','Authorization' => 'Bearer '. $token]);

        $response->assertStatus(200);
        $response->assertJsonStructure($responseStructure);
        $response->assertJson([
            'success'=>true,
            'message' => "Logout successfully."
        ]);
    }

    private function registerUser($user){
            $tempUserBcrypt= $user;
            $tempUserBcrypt["password"] = bcrypt($tempUserBcrypt['password']);
            return User::create($tempUserBcrypt);
    }
}
