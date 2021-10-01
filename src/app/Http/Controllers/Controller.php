<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(title="Albelli API", version="0.1"),
 * @OA\Server(url="http://localhost:8000"),
 * @OAS\SecurityScheme(
 * securityScheme="bearer_token",
 * type="http",
 * scheme="bearer"
 * ),
 * @OA\Schema(
 *     @OA\Xml(name="JsonResponse"),
 *   schema="JsonResponse",
 *   title="Json Response Model",
 *   description="Json Response Model",
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
 *     description="Optional data returned by the API server goes here or an error message if success was false",
 *  )

 * )
 */

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

}
