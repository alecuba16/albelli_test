<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Advertisement;
use App\Http\Resources\AdvertisementResource as AdvertisementResource;

/**
 * @OA\Schema(
 *     @OA\Xml(name="AdvertisementSchema"),
 *   schema="AdvertisementSchema",
 *   title="AdvertisementResource Model",
 *   description="AdvertisementResource model",
 *   required={"id","title","created_at","updated_at"},
 *   @OA\Property(
 *     property="id", description="ID of the advertisement",
 *     type="number",
 *     example=1
 *  ),
 *   @OA\Property(
 *     property="title", description="Title of the advertisement",
 *     type="string",
 *     example="My title"
 *  ),
 *  @OA\Property(
 *     property="created_at", description="Creation date of the AdvertisementResource",
 *     type="timestamp",
 *     example="2021-09-26T10:57:37+00:00"
 *  ),
 *  @OA\Property(
 *     property="updated_at", description="Update date of the AdvertisementResource",
 *     type="timestamp",
 *     example="2021-09-26T10:57:37+00:00"
 *  )
 * )
 */

/**
 * @OA\Schema(
 *     @OA\Xml(name="AdvertisementCreateSchema"),
 *   schema="AdvertisementCreateSchema",
 *   title="AdvertisementResource Create Update Model",
 *   description="AdvertisementResource Create Update Model",
 *   required={"title"},
 *   @OA\Property(
 *     property="title", description="Title of the advertisement",
 *     type="string",
 *     example="My title"
 *  ),
 *   @OA\Property(
 *     property="offers", description="[OPTIONAL] The ids of the related offers. All ids should exists before creating this advertisement",
 *     type="array",
 *     @OA\items(type="integer"),
 *     example="[1,2,3]"
 *  )
 * )
 */

/**
 * @OA\Schema(
 *     @OA\Xml(name="JsonResponseAdvertisement"),
 *   schema="JsonResponseAdvertisement",
 *   title="Json Response AdvertisementResource Model",
 *   description="Json AdvertisementResource Model",
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
 *     @OA\Items(ref="#/components/schemas/AdvertisementSchema")
 *  )
 * )
 */

class RestAdvertisementController extends RestController
{


    /**
     * @OA\Get(
     * tags={"Advertisements"},
     * path="/api/advertisements",
     * security={{"bearer_token":{}}},
     * summary="Get all the available advertisements",
     * @OA\Response(
     *    response=200,
     *    description="A list of advertisements.",
     *    @OA\JsonContent(
     *      ref="#/components/schemas/JsonResponseAdvertisement",
     *    )
     * )
     *)
     */

    public function index()
    {
        $advertisements = Advertisement::all();
        return $this->sendResponse(AdvertisementResource::collection($advertisements), 'All advertisements fetched.');
    }

    /**
     * @OA\Post(
     *     path="/api/advertisements",
     *     tags={"Advertisements"},
     *     summary="Adds a new advertisement",
     *     security={{"bearer_token":{}}},
     *     @OA\RequestBody(
     *             @OA\JsonContent(
     *      ref="#/components/schemas/AdvertisementCreateSchema",
     *    )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="AdvertisementResource created.",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(ref="#/components/schemas/JsonResponseAdvertisement"),
     *                 example={"success":true,"message":"AdvertisementResource My title created.","data": {"id": 12,"title": "My title","created_at": "2021-09-26T18:17:47+00:00","updated_at": "2021-09-26T18:17:47+00:00"}}     *
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
     *                 example={"success":false,"message":{"title": {"The title field is required."}}}
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
            'title' => 'required|unique:advertisements'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $advertisement = Advertisement::create($input);
        if( array_key_exists('offers', $input)&&$input["offers"]!=null&&count($input["offers"])>0){
            $error = $this->attachOffers($input["offers"], $advertisement);
            if($error!=null)
                return $error;
         }

        return $this->sendResponse(new AdvertisementResource($advertisement), "AdvertisementResource {$advertisement->title} created.");
    }

    /**
     * @OA\Get(
     *     path="/api/advertisements/{id}",
     *     tags={"Advertisements"},
     *     summary="Returns a specific instance given by the id",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *                  required=true,
     *                  name="id",
     *                  example=1,
     *                  in="path"
     *                  ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Retuns one instance with the asked advertisement",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(ref="#/components/schemas/JsonResponseAdvertisement"),
     *                 example={"success":true,"message":"AdvertisementResource My title fetched.","data": {"id": 12,"title": "My title","created_at": "2021-09-26T18:17:47+00:00","updated_at": "2021-09-26T18:17:47+00:00"}}     *
     *             )
     *         }
     *    ),
     *    @OA\Response(
     *         response=404,
     *         description="AdvertisementResource does not exist.",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(ref="#/components/schemas/JsonResponse"),
     *                 example={"success":false,"message":"AdvertisementResource does not exist."}
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
        $advertisement = Advertisement::find($id);
        if (is_null($advertisement)) {
            return $this->sendError('AdvertisementResource does not exist.');
        }
        return $this->sendResponse(new AdvertisementResource($advertisement), "AdvertisementResource {$advertisement->title} fetched.");
    }


    /**
     * @OA\Put(
     *     path="/api/advertisements/{id}",
     *     tags={"Advertisements"},
     *     summary="Updates the existing advertisement given by the id",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *                  name="id",
     *                  example=1,
     *                  required=true,
     *                  in="path"
     *                  ),
     *     @OA\RequestBody(
     *             @OA\JsonContent(
     *      ref="#/components/schemas/AdvertisementCreateSchema",
     *    )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="AdvertisementResource updated.",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(ref="#/components/schemas/JsonResponseAdvertisement"),
     *                 example={"success":true,"message":"AdvertisementResource My title3 updated.","data": {"id": 1,"title": "My title3","created_at": "2021-09-26T16:08:55+00:00","updated_at": "2021-09-26T18:40:53+00:00"}}     *
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
     *                 example={"success":false,"message":{"title": {"The title field is required."}}}
     *             ),
     *         },
     *    )
     * )
     */
    public function update(Request $request, Advertisement $advertisement)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'title' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors());
        }

        if(array_key_exists("offers",$input)) {
            $currentAttachmentsIds = $advertisement->offers()->get();
            if(sizeof($currentAttachmentsIds)>0) {
                $currentAttachmentsIds = $currentAttachmentsIds->map(function ($e) {
                    return $e["id"];
                })->toArray();
            }else {
                $currentAttachmentsIds=[];
            }
            if( $input["offers"]==null||count($input["offers"])==0){
                //Delete
                $deleteIds=$currentAttachmentsIds;
                $newIds=[];
            }else {
                //Calculate difference in attachments
                $inputIds = array_map(function ($e) {
                    return $e["id"];
                }, $input["offers"]);
                $commonIds = array_intersect($inputIds, $currentAttachmentsIds);
                $newIds = array_diff($inputIds, $commonIds);
                $deleteIds = array_diff($currentAttachmentsIds, $commonIds);
            }
            if(count($newIds)>0) {
                $error = $this->attachOffers($newIds, $advertisement);
                if ($error != null)
                    return $error;
            }
            if(count($deleteIds)>0) {
                foreach ($deleteIds as $deleteId) {
                    $error = $advertisement->offers()->detach($deleteId);
                    if ($error != null)
                        return $error;
                }
            }
        }

        $advertisement->title = $input['title'];
        $advertisement->save();

        return $this->sendResponse(new AdvertisementResource($advertisement), "AdvertisementResource {$advertisement->title} updated.");
    }

    /**
     * @OA\Delete(
     *     path="/api/advertisements/{id}",
     *     tags={"Advertisements"},
     *     summary="Deletes the advertisement given by the id",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *                  required=true,
     *                  name="id",
     *                  example=1,
     *                  in="path"
     *                  ),
     *     @OA\Response(
     *         response=200,
     *         description="AdvertisementResource deleted.",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(ref="#/components/schemas/JsonResponse"),
     *                 example={"success":true,"message":"AdvertisementResource MyTitle deleted."}
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
     *                 example={"success":false,"message": "AdvertisementResource does not exist."}
     *             ),
     *         },
     *    )
     * )
     */
    public function destroy(Request $request, $id)
    {
        try {
            $advertisement = Advertisement::find($id);
            if($advertisement == null)
                return $this->sendError('AdvertisementResource does not exist.');
            $this->destroyWithAttached($advertisement);
            return $this->sendResponse(null, "AdvertisementResource {$id} deleted.");
        } catch (ModelNotFoundException $e) {
            return $this->sendError('Unable to delete the advertisement {$id}.');
        }
    }

    private function destroyWithAttached($advertisement){
        //Detach from pivot table
        foreach ($advertisement->offers as $offer)
        {
            $advertisement->offers()->detach($offer->id);
        }
        Advertisement::destroy($advertisement->id);
    }

    private function attachOffers($input,$advertisement){
        try{
            $advertisement->offers()->attach($input);
            return null;
        }catch(\Exception $e){
            $i=0;
            $currentId=Offer::find($input[$i]);
            while($currentId!=null&&$i<count($input)){
                $currentId=Offer::find($input[$i]);
                if($currentId!=null) $i++;
            }
            $this->destroyWithAttached($advertisement);
            return $this->sendError("The offer_id:".$input[$i]." doesn't exists, advertisement not inserted");
        }
    }
}
