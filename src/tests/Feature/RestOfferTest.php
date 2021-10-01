<?php

namespace Tests\Feature;
use App\Models\User;
use App\Models\Offer;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesApplication;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RestOfferTest extends TestCase
{
    use CreatesApplication;
    use DatabaseMigrations;
    use RefreshDatabase;

    private $responseStructure = [
        "success",
        "message",
        "data"
    ];

    public function test_get_all_offers()
    {
        [$offer,$offerJson]=$this->generateOffer("create");

        $token=$this->getAuthToken($this);
        $response = $this->json('GET', '/api/offers',['Accept'=>'application/json','Authorization' => 'Bearer '. $token]);

        $response->assertStatus(200);
        $response->assertJsonStructure($this->responseStructure);
        $response->assertJson([
            'success'=>true,
            'data'=>[
                $offerJson
            ],
            'message' => "All offers fetched."
        ]);
    }

    public function test_get_one_offer()
    {
        [$offer1,$offerJson1]=$this->generateOffer("create");
        [$offer2,$offerJson2]=$this->generateOffer("create");

        //Offer1
        $token=$this->getAuthToken($this);
        $response = $this->json('GET', '/api/offers/'.$offer1->id,['Accept'=>'application/json','Authorization' => 'Bearer '. $token]);

        $response->assertStatus(200);
        $response->assertJsonStructure($this->responseStructure);
        $response->assertJson([
            'success'=>true,
            'data'=>$offerJson1,
            'message' => "OfferResource ".$offer1->product_name." fetched."
        ]);

        //Offer2
        $response = $this->json('GET', '/api/offers/'.$offer2->id,['Accept'=>'application/json','Authorization' => 'Bearer '. $token]);

        $response->assertStatus(200);
        $response->assertJsonStructure($this->responseStructure);
        $response->assertJson([
            'success'=>true,
            'data'=>$offerJson2,
            'message' => "OfferResource ".$offer2->product_name." fetched."
        ]);
    }

    public function test_add_offer()
    {
        [$newOffer,$newOfferJson]=$this->generateOffer("make");
        $token=$this->getAuthToken($this);
        $response = $this->json('POST', '/api/offers/',$newOfferJson,['Accept'=>'application/json','Authorization' => 'Bearer '. $token]);
        $response->assertStatus(200);
        $response->assertJsonStructure($this->responseStructure);
        $response->assertJson([
            'success'=>true,
            'message' => "OfferResource ".$newOffer->product_name." created.",
            'data'=> $newOfferJson,
        ]);
    }

    public function test_remove_offer()
    {
        [$newOffer,$newOfferJson]=$this->generateOffer("create");
        $token=$this->getAuthToken($this);
        $response = $this->json('DELETE', '/api/offers/'.$newOffer->id,['Accept'=>'application/json','Authorization' => 'Bearer '. $token]);
        $response->assertStatus(200);
        $response->assertJsonStructure($this->responseStructure);
        $response->assertJson([
            'success'=>true,
            'message' => "OfferResource ".$newOffer->id." deleted.",
            'data'=> null,
        ]);
    }

    private function getAuthToken($self){
        $user=User::factory()->create(["password"=>bcrypt("alex")]);
        $response = $self->json('POST', '/api/login',["email"=>$user->email,"password"=>"alex"],['Accept'=>'application/json']);
        return $response->decodeResponseJson()["data"]["token"];
    }

    private function generateOffer($createOrmake="create")
    {
        if($createOrmake=="create"){
            $offer = Offer::factory()->create();
            $offerJson =  $offer->getOriginal();
            $offerJson["start_date"]=$offer->getAttribute("start_date")->format('c');
            $offerJson["end_date"]=$offer->getAttribute("end_date")->format('c');
            $offerJson["created_at"]=$offer->getAttribute("created_at")->format('c');
            $offerJson["updated_at"]=$offer->getAttribute("updated_at")->format('c');
        }else{
            $offer = Offer::factory()->make() ;
            $offerJson =  $offer->getAttributes();
            $offerJson["start_date"]=date('c',strtotime($offer->getAttribute("start_date")));
            $offerJson["end_date"]=date('c',strtotime($offer->getAttribute("end_date")));
        }
        return [$offer,$offerJson];
    }


}
