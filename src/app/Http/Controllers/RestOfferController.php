<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Offer;
use App\Http\Resources\OfferResource as OfferResource;

/**
 * @OA\Schema(
 *     @OA\Xml(name="OfferSchema"),
 *   schema="OfferSchema",
 *   title="OfferResource Model",
 *   description="OfferResource model",
 *   @OA\Property(
 *     property="id", description="ID of the advertisement",
 *     type="number",
 *     example=1
 *  ),
 *   @OA\Property(
 *     property="product_name", description="Product name of the advertisement",
 *     type="string",
 *     example="The product name"
 *  ),
 *  @OA\Property(
 *     property="discount_value", description="The percentage (natural number) from 0 to 100 of discount (in %)",
 *     type="integer",
 *     example="25"
 *  ),
 *  @OA\Property(
 *     property="start_date", description="The date time that the offer starts to be valid",
 *     type="timestamp",
 *     example="2021-09-26T10:57:37+00:00"
 *  ),
 *  @OA\Property(
 *     property="end_date", description="The date time that the offer will be invalidated",
 *     type="timestamp",
 *     example="2021-09-26T10:57:37+00:00"
 *  ),
 *  @OA\Property(
 *     property="created_at", description="Creation date of the offer",
 *     type="timestamp",
 *     example="2021-09-26T10:57:37+00:00"
 *  ),
 *  @OA\Property(
 *     property="updated_at", description="Update date of the offer",
 *     type="timestamp",
 *     example="2021-09-26T10:57:37+00:00"
 *  )
 * )
 */

/**
 * @OA\Schema(
 *     @OA\Xml(name="OfferCreateSchema"),
 *   schema="OfferCreateSchema",
 *   title="OfferResource Create Update Model",
 *   description="OfferResource Create Update Model",
 *   required={"product_name","discount_value","start_date","end_date"},
 *   @OA\Property(
 *     property="product_name", description="Product name of the advertisement",
 *     type="string",
 *     example="The product name"
 *  ),
 *  @OA\Property(
 *     property="discount_value", description="The percentage (natural number) from 0 to 100 of discount (in %)",
 *     type="integer",
 *     example="25"
 *  ),
 *  @OA\Property(
 *     property="start_date", description="The date time that the offer starts to be valid",
 *     type="timestamp",
 *     example="2021-09-26T10:57:37+00:00"
 *  ),
 *  @OA\Property(
 *     property="end_date", description="The date time that the offer will be invalidated",
 *     type="timestamp",
 *     example="2021-09-26T10:57:37+00:00"
 *  ),
 *  @OA\Property(
 *     property="advertisements", description="[OPTIONAL] The ids of the related advertisements. All ids should exists before creating this offer",
 *     type="array",
 *     @OA\items(type="integer"),
 *     example="[1,2,3]"
 *  )
 * )
 */

/**
 * @OA\Schema(
 *     @OA\Xml(name="JsonResponseOffer"),
 *   schema="JsonResponseOffer",
 *   title="Json Response OfferResource Model",
 *   description="Json OfferResource Model",
 *   required={"success","message"},
 *   @OA\Property(
 *     property="success",
 *     description="Returns true if the action was completed succesfully",
 *     type="bolean",
 *     example=true
 *  ),
 *   @OA\Property(
 *     property="message", description="Includes any aditional message that the endpoint may generate with informative proposes",
 *     type="string",
 *     example="Inserted ok. The query took 100ms"
 *  ),
 *  @OA\Property(
 *     property="data",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/OfferSchema")
 *  )
 * )
 */

class RestOfferController extends RestController
{

    /**
     * @OA\Get(
     * tags={"Offers"},
     * path="/api/offers",
     * security={{"bearer_token":{}}},
     * summary="Get all the available offers",
     * @OA\Response(
     *    response=200,
     *    description="A list of offers.",
     *    @OA\JsonContent(
     *      ref="#/components/schemas/JsonResponseOffer",
     *    )
     * )
     *)
     */
    public function index()
    {
        $offers = Offer::all();
        return $this->sendResponse(OfferResource::collection($offers), 'All offers fetched.');
    }

    /**
     * @OA\Post(
     *     path="/api/offers",
     *     tags={"Offers"},
     *     summary="Adds a new offer",
     *     security={{"bearer_token":{}}},
     *     @OA\RequestBody(
     *             @OA\JsonContent(
     *      ref="#/components/schemas/OfferCreateSchema",
     *    )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OfferResource created.",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(ref="#/components/schemas/JsonResponseOffer"),
     *                 example={"success":true,"message": "OfferResource The product name created.","data": {"id": 14,"product_name": "The product name","discount_value": 25,"start_date": "2021-09-26T10:57:37+00:00","end_date": "2021-09-26T10:57:37+00:00","created_at": "2021-09-26T20:47:39+00:00","updated_at": "2021-09-26T20:47:39+00:00"}}
     *             )
     *         }
     *    ),
     *    @OA\Response(
     *         response=404,
     *         description="Error validation",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(ref="#/components/schemas/JsonResponse"),
     *                 example={"success":false,"message": {"discount_value": {"The discount value field is required."},"start_date": {"The start date field is required."}}}
     *             ),
     *
     *         },
     *
     *    )
     *
     * )
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'product_name' => 'required',
            'discount_value' => 'required',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $offer = Offer::create($input);
		if(array_key_exists('advertisements', $input)&& $input["advertisements"]!=null&&count($input["advertisements"])>0){
            $error = $this->attachOffers($input["advertisements"], $offer);
            if($error!=null)
                return $error;
        }

        return $this->sendResponse(new OfferResource($offer), "OfferResource {$offer->product_name} created.");
    }

    /**
     * @OA\Get(
     *     path="/api/offers/{id}",
     *     tags={"Offers"},
     *     summary="Returns a specific instance given by the id",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *                  name="id",
     *                  required=true,
     *                  example=1,
     *                  in="path"
     *                  ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Retuns one instance with the asked offer",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(ref="#/components/schemas/JsonResponseOffer"),
     *                 example={"success":true,"message":"OfferResource My title fetched.", "data": {"id": 14,"product_name": "The product name","discount_value": 25,"start_date": "2021-09-26T10:57:37+00:00","end_date": "2021-09-26T10:57:37+00:00","created_at": "2021-09-26T20:47:39+00:00","updated_at": "2021-09-26T20:47:39+00:00"}}
     *             )
     *         }
     *    ),
     *    @OA\Response(
     *         response=404,
     *         description="OfferResource does not exist.",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(ref="#/components/schemas/JsonResponse"),
     *                 example={"success":false,"message":"OfferResource does not exist."}
     *             ),
     *
     *         },
     *
     *    )
     *
     * )
     */
    public function show($id)
    {
        $offer = Offer::find($id);
        if (is_null($offer)) {
            return $this->sendError('OfferResource does not exist.');
        }
        return $this->sendResponse(new OfferResource($offer), "OfferResource {$offer->product_name} fetched.");
    }

    /**
     * @OA\Put(
     *     path="/api/offers/{id}",
     *     tags={"Offers"},
     *     summary="Updates the existing offer given by the id",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *                  name="id",
     *                  example=1,
     *                  required=true,
     *                  in="path"
     *                  ),
     *     @OA\RequestBody(
     *             @OA\JsonContent(
     *      ref="#/components/schemas/OfferCreateSchema",
     *    )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OfferResource updated.",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(ref="#/components/schemas/JsonResponseOffer"),
     *                 example={"success":true,"message":"OfferResource My title3 updated.","data": {"id": 14,"product_name": "The product name","discount_value": 25,"start_date": "2021-09-26T10:57:37+00:00","end_date": "2021-09-26T10:57:37+00:00","created_at": "2021-09-26T20:47:39+00:00","updated_at": "2021-09-26T20:47:39+00:00"}}
     *             )
     *         }
     *    ),
     *    @OA\Response(
     *         response=404,
     *         description="Error validation",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(ref="#/components/schemas/JsonResponse"),
     *                 example={"success":false,"message":{"title": {"The product_name field is required."}}}
     *             ),
     *         },
     *    )
     * )
     */
    public function update(Request $request, Offer $offer)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'product_name' => 'required',
            'discount_value' => 'required',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors());
        }

        if(array_key_exists("advertisements",$input)) {
            $currentAttachmentsIds = $offer->advertisements()->get();
            if(sizeof($currentAttachmentsIds)>0) {
                $currentAttachmentsIds = $currentAttachmentsIds->map(function ($e) {
                    return $e["id"];
                })->toArray();
            }else {
                $currentAttachmentsIds=[];
            }
            if( $input["advertisements"]==null||count($input["advertisements"])==0){
                //Delete
                $deleteIds=$currentAttachmentsIds;
                $newIds=[];
            }else {
                //Calculate difference in attachments
                $inputIds = array_map(function ($e) {
                    return $e["id"];
                }, $input["advertisements"]);
                $commonIds = array_intersect($inputIds, $currentAttachmentsIds);
                $newIds = array_diff($inputIds, $commonIds);
                $deleteIds = array_diff($currentAttachmentsIds, $commonIds);
            }
            if(count($newIds)>0) {
                $error = $this->attachAdvertisements($newIds, $offer);
                if ($error != null)
                    return $error;
            }
            if(count($deleteIds)>0) {
                foreach ($deleteIds as $deleteId) {
                    $error = $offer->advertisements()->detach($deleteId);
                    if ($error != null)
                        return $error;
                }
            }
        }

        $offer->product_name = $input['product_name'];
        $offer->discount_value = $input['discount_value'];
        $offer->start_date = $input['start_date'];
        $offer->end_date = $input['end_date'];
        $offer->save();

        return $this->sendResponse(new OfferResource($offer), "OfferResource {$offer->product_name} updated.");
    }


    /**
     * @OA\Delete(
     *     path="/api/offers/{id}",
     *     tags={"Offers"},
     *     summary="Deletes the offer given by the id",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *                  required=true,
     *                  name="id",
     *                  example=1,
     *                  in="path"
     *                  ),
     *     @OA\Response(
     *         response=200,
     *         description="OfferResource deleted.",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(ref="#/components/schemas/JsonResponse"),
     *                 example={"success":true,"message":"OfferResource MyTitle deleted."}
     *             )
     *         }
     *    ),
     *    @OA\Response(
     *         response=406,
     *         description="Error Referenced",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(ref="#/components/schemas/JsonResponse"),
     *                 example={"success":false,"message": "Cannot proceed with query, it is referenced by other records in the database."}
     *             ),
     *         },
     *    ),
     *     @OA\Response(
     *         response=404,
     *         description="Error Not exists",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(ref="#/components/schemas/JsonResponse"),
     *                 example={"success":false,"message": "OfferResource does not exist."}
     *             ),
     *         },
     *    )
     * )
     */
    public function destroy(Request $request, $id)
    {
        try {
            $offer = Offer::find($id);
            if($offer == null)
                return $this->sendError('AdvertisementResource does not exist.');
            //Detach from pivot table
            $this->destroyWithAttached($offer);
            Offer::destroy($id);
            return $this->sendResponse(null, "OfferResource {$id} deleted.");
        } catch (ModelNotFoundException $e) {
            return $this->sendError('Unable to delete the offer {$id}.');
        }
    }

    private function destroyWithAttached($offer){
        //Detach from pivot table
        foreach ($offer->advertisements as $advertisement)
        {
            $offer->advertisements()->detach($advertisement->id);
        }
        Offer::destroy($offer->id);
    }

    private function attachAdvertisements($input,$offer){
        try{
            $res=$offer->advertisements()->attach($input);
            return null;
        }catch(\Exception $e){
            $i=0;
            $currentId=Offer::find($input[$i]);
            while($currentId!=null&&$i<count($input)){
                $currentId=Offer::find($input[$i]);
                if($currentId!=null) $i++;
            }
            $this->destroyWithAttached($offer);
            return $this->sendError("The advertisement_id:".$input[$i]." doesn't exists, offer not inserted");
        }
    }
}
