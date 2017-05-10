<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use JWTAuth;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */


    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */

    protected $user_fields = [
        'fname',
        'lname',
        'status',
        'email',
        'age',
        'password',
        'password_confirmation'
    ];

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'fname' => 'required|string|max:255|min:1',
            'lname' => 'required|string|max:255|min:1',
            'status' =>  [
                'required',
                Rule::in(['admin', 'user', 'guess']),
            ],
            'email' => 'required|string|email|max:255|unique:users',
            'age' => 'required|integer|min:1',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    public function register(Request $request)
    {

        $data = $request->only($this->user_fields);

        $validator = $this->validator($data);

        if ($validator->fails()) {
            $errors = $validator->messages();

            return response()->json(compact('errors'))->setStatusCode(400);
        } else {
            $user = new User($data);
            $user->password = bcrypt($data['password']);
            $user->save();

            $token = JWTAuth::fromUser($user);

            return response()->json(compact('token', 'user'))->setStatusCode(201);
        }

    }
}
