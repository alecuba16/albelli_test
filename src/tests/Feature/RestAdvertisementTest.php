<?php

namespace Tests\Feature;

use App\Models\Advertisement;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\CreatesApplication;

class RestAdvertisementTest extends TestCase
{
    use CreatesApplication;
    use DatabaseMigrations;
    use RefreshDatabase;

    private $responseStructure = [
        "success",
        "message",
        "data"
    ];

    public function test_get_all_advertisements()
    {
        [$advertisement,$advertisementJson]=$this->generateAdvertisement("create");

        $token=$this->getAuthToken($this);
        $response = $this->json('GET', '/api/advertisements',['Accept'=>'application/json','Authorization' => 'Bearer '. $token]);

        $response->assertStatus(200);
        $response->assertJsonStructure($this->responseStructure);
        $response->assertJson([
            'success'=>true,
            'data'=>[
                $advertisementJson
            ],
            'message' => "All advertisements fetched."
        ]);
    }

    public function test_get_one_advertisement()
    {
        [$advertisement1,$advertisementJson1]=$this->generateAdvertisement("create");
        [$advertisement2,$advertisementJson2]=$this->generateAdvertisement("create");

        //Advertisement1
        $token=$this->getAuthToken($this);
        $response = $this->json('GET', '/api/advertisements/'.$advertisement1->id,['Accept'=>'application/json','Authorization' => 'Bearer '. $token]);

        $response->assertStatus(200);
        $response->assertJsonStructure($this->responseStructure);
        $response->assertJson([
            'success'=>true,
            'data'=>$advertisementJson1,
            'message' => "AdvertisementResource ".$advertisement1->title." fetched."
        ]);

        //Advertisement2
        $response = $this->json('GET', '/api/advertisements/'.$advertisement2->id,['Accept'=>'application/json','Authorization' => 'Bearer '. $token]);

        $response->assertStatus(200);
        $response->assertJsonStructure($this->responseStructure);
        $response->assertJson([
            'success'=>true,
            'data'=>$advertisementJson2,
            'message' => "AdvertisementResource ".$advertisement2->title." fetched."
        ]);
    }

    public function test_add_advertisement()
    {
        [$newAdvertisement,$newAdvertisementJson]=$this->generateAdvertisement("make");
        $token=$this->getAuthToken($this);
        $response = $this->json('POST', '/api/advertisements/',$newAdvertisementJson,['Accept'=>'application/json','Authorization' => 'Bearer '. $token]);
        $response->assertStatus(200);
        $response->assertJsonStructure($this->responseStructure);
        $response->assertJson([
            'success'=>true,
            'message' => "AdvertisementResource ".$newAdvertisement->title." created.",
            'data'=> $newAdvertisementJson,
        ]);
    }

    public function test_remove_advertisement()
    {
        [$newAdvertisement,$newAdvertisementJson]=$this->generateAdvertisement("create");
        $token=$this->getAuthToken($this);
        $response = $this->json('DELETE', '/api/advertisements/'.$newAdvertisement->id,['Accept'=>'application/json','Authorization' => 'Bearer '. $token]);
        $response->assertStatus(200);
        $response->assertJsonStructure($this->responseStructure);
        $response->assertJson([
            'success'=>true,
            'message' => "AdvertisementResource ".$newAdvertisement->id." deleted.",
            'data'=> null,
        ]);
    }

    private function getAuthToken($self){
        $user=User::factory()->create(["password"=>bcrypt("alex")]);
        $response = $self->json('POST', '/api/login',["email"=>$user->email,"password"=>"alex"],['Accept'=>'application/json']);
        return $response->decodeResponseJson()["data"]["token"];
    }

    private function generateAdvertisement($createOrmake="create")
    {
        if($createOrmake=="create"){
            $advertisement = Advertisement::factory()->create();
            $advertisementJson =  $advertisement->getOriginal();
            $advertisementJson["created_at"]=$advertisement->getAttribute("created_at")->format('c');
            $advertisementJson["updated_at"]=$advertisement->getAttribute("updated_at")->format('c');
        }else{
            $advertisement = Advertisement::factory()->make() ;
            $advertisementJson =  $advertisement->getAttributes();
        }
        return [$advertisement,$advertisementJson];
    }

}
