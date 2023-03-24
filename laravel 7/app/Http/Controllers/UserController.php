<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\HasApiTokens;

class UserController extends BaseController
{
    use HasApiTokens;
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'user_type' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
       
        $success['token'] = $user->createToken('MyAuthApp')->plainTextToken;
        $success['user'] = $user;

        return $this->sendResponse($success, 'User created successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function login(Request $request)
    {
        // dd($request);
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $authUser = Auth::user();
            $success['token'] = $authUser->createToken('studentApiKey')->plainTextToken;
            $success['user'] = $authUser;

            return $this->sendResponse($success, 'User signed in');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }
    }

    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('MyAuthApp')->plainTextToken;
        $success['name'] = $user->name;

        return $this->sendResponse($success, 'User created successfully.');
    }


    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->sendError(true, "logout successfully");
    }

    /**
     * get all user
     */
    public function admins()
    {
        //$this->authorize('view', User::class);
        return $this->sendResponse(User::where('user_type', 'admin')->get(), "data fetched successfully");
    }

    public function agents()
    {
        //$this->authorize('view', User::class);
        return $this->sendResponse(User::where('user_type', 'agent')->get(), "data fetched successfully");
    }
}
