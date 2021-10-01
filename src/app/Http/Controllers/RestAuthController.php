<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\RestController as RestController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

/**
 * Class RestAuthController
 * @package App\Http\Controllers
 */
class RestAuthController extends RestController
{

    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"User"},
     *     summary="Authentication using email and password",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"email","password"},
     *                 @OA\Property(
     *                     property="email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="password"
     *                 ),
     *                 example={"email": "alecuba16@gmail.com", "password": "alex"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Logged succesfully",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(ref="#/components/schemas/JsonResponse"),
     *                 example={"success":true,"message":"User signed in","data":{"token":"1|c9iSWWNP8iNfl1ZfM13cisTsM3oJE5oSGcgeA3Wf","name":"alex"}}     *
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
     *                 example={"success":false,"message":"Unauthorised.","data":{"error":"Unauthorised"}}
     *             ),
     *
     *         },
     *
     *    )
     *
     * )
     */
    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $authUser = Auth::user();
            $success['token'] = $authUser->createToken('MyAuthApp')->plainTextToken;
            $success['name'] = $authUser->name;

            return $this->sendResponse($success, 'User signed in');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/register",
     *     tags={"User"},
     *     summary="Registers a new user",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"name","email","password"},
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="email"
     *                 ),
     *                  @OA\Property(
     *                     property="password",
     *                     type="password"
     *                 ),
     *                 example={"name": "alex", "email": "alecuba16@gmail.com", "password": "alex"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Registered successfully",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(ref="#/components/schemas/JsonResponse"),
     *                 example={"success":true,"message":"User created successfully.","data":{"token":"1|c9iSWWNP8iNfl1ZfM13cisTsM3oJE5oSGcgeA3Wf","name":"alex"}}     *
     *             )
     *         }
     *    ),
     *    @OA\Response(
     *         response=404,
     *         description="Error registering",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(ref="#/components/schemas/JsonResponse"),
     *                 example={"success":false,"message":"Error validation","data":{"email":{"The email has already been taken."}}}
     *             ),
     *
     *         },
     *
     *    )
     *
     * )
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors());
        }

        //Check for duplicated users


        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('MyAuthApp')->plainTextToken;
        $success['name'] = $user->name;

        return $this->sendResponse($success, 'User created successfully.');
    }

    /**
     * @OA\Get(
     *     path="/api/logout",
     *     tags={"User"},
     *     security={{"bearer_token":{}}},
     *     summary="Logout the current user session",
     *     @OA\Response(
     *         response=200,
     *         description="Logout successfully",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(ref="#/components/schemas/JsonResponse"),
     *                 example={"success":true,"message":"Logout successfully"}
     *             )
     *         }
     *    ),
     *    @OA\Response(
     *         response=401,
     *         description="Unable to logout",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(ref="#/components/schemas/JsonResponse"),
     *                 example={"success":false,"message":"User not authenticated."}
     *             ),
     *
     *         },
     *
     *    )
     *
     * )
     */
    public function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request...
        try{
            $user = $request->user();
            $user->tokens()->where('id', auth()->id())->delete();
            return $this->sendResponse("", 'Logout successfully.');
        }catch(\Exception $e){
            return $this->sendError('Unable to logout', null);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/checkLogin",
     *     tags={"User"},
     *     security={{"bearer_token":{}}},
     *     summary="Check if the current token is valid",
     *     @OA\Response(
     *         response=200,
     *         description="Token check successfully"
     *    ),
     *    @OA\Response(
     *         response=401,
     *         description="User not authenticated."
     *    )
     * )
     */
    public function checkLogin()
    {
        //return auth()->user();
       return auth('sanctum')->check();;  // or Auth::guard('web')->check() or $this->user() all returned null
    }
}
