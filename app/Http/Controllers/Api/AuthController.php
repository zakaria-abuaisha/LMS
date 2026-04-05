<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Users\LoginUserRequest;
use App\Http\Requests\Api\V1\Users\RegisterUserRequest;
use App\Models\User;    
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponses;

    /**
     * Login
     * 
     * Authenticates the user and returns the user's API token.
     * 
     * @unauthenticated
     * @group Authentication
     * @response 200 {"data": { "token": "{YOUR_AUTH_KEY}"}, "message": "Authenticated", "status": 200 }
     * @response 401 {"message": "Invalid Email or Password.", "status": 401 }
     */
    public function login(LoginUserRequest $request)
    {
        $request->validated($request->all());

        if(!Auth::attempt($request->only("email","password"))) 
        {
            return $this->error("Invalid Email or Password.", 401);
        }

        $user = User::firstWhere("email", $request->email);
        
        return $this->ok(
            "Authenticated",
            [
                "token" => $user->createToken(
                    name: "API token for " . $user->email,
                    expiresAt: now()->addMonth())->plainTextToken
            ]);
    }

    /**
     * Logout
     * 
     * Signs out the user and destroys the API token.
     * 
     * @group Authentication
     * @response 200 {"message": "Logged Out Successfully", "status": 200 }
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->ok('Logged Out Successfully');
    }

    /**
     * Register
     * 
     * Signs up a new user and directly returns the user's API token.
     * 
     * @unauthenticated
     * @group Authentication
     * @response 200 {"data": { "token": "{YOUR_AUTH_KEY}"}, "message": "Authenticated", "status": 200 }
     */
    public function register(RegisterUserRequest $request)
    {
        $attributes = $request->validated();

        $user = User::create([
            'first_name' => $attributes['firstName'],
            'last_name' => $attributes['lastName'],
            'email' => $attributes['email'],
            'password' => Hash::make($attributes['password']),
        ]);

        return $this->ok(
            "Authenticated",
            [
                "token" => $user->createToken(
                    name: "API token for " . $user->email,
                    expiresAt: now()->addMonth())->plainTextToken
            ]);
    }
}
