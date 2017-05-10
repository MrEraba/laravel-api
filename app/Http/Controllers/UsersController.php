<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UsersController extends Controller
{
    //
    protected $user_fields = [
        'fname',
        'lname',
        'status',
        'email',
        'age',
        'password',
        'password_confirmation'
    ];

    protected function getRules()
    {
        return [
            'fname' => 'required|string|max:255|min:1',
            'lname' => 'required|string|max:255|min:1',
            'status' =>  [
                'required',
                Rule::in(['admin', 'user', 'guess']),
            ],
            'email' => 'required|string|email|max:255|unique:users',
            'age' => 'required|integer|min:1',
            'password' => 'required|string|min:6|confirmed',
        ];
    }

    protected function validator(array $data, array $rules)
    {
        return Validator::make($data, $rules);
    }

    public function index(Request $request)
    {
        $pagination = 15;
        $users = User::paginate($pagination);
        return response()->json(compact('users'))->setStatusCode(200);
    }

    public function show($id)
    {
        try{
            $user = User::findOrFail($id);
            return response()->json(compact('user'))->setStatusCode(200);

        } catch (ModelNotFoundException $e) {
            $error = 'user not found.';
            return response()->json(compact('error'))->setStatusCode(404);
        }
    }

    public function store(Request $request)
    {

        $data = $request->only($this->user_fields);
        $rules = $this->getRules();
        $validator = $this->validator($data, $rules);

        if ($validator->fails()) {
            $errors = $validator->messages();
            return response()->json(compact('errors'))->setStatusCode(400);
        } else {
            $user = new User($data);
            $user->password = bcrypt($data['password']);
            $user->save();
            return response()->json(compact('user'))->setStatusCode(201);
        }
    }

    public function update(Request $request, $id)
    {

        try{
            $user = User::findOrFail($id);

            // set a blank email in order to re-use the validation rules for update
            $user->email = '';
            $user->save();

            // check if password is present in the request data
            if ($request->input('password')) {
                // if password is present, then use the entire validation rules
                $data = $request->only($this->user_fields);
                $rules = $this->getRules();

            } else {
                // if password is not present take off password from user fields array and from validation rules
                $data = $request->only(array_slice($this->user_fields, 0, 5));
                $rules = array_slice( $this->getRules(), 0, 5);
            }

            $validator = $this->validator($data, $rules);

            if ($validator->fails()) {
                $errors = $validator->messages();
                return response()->json(compact('errors'))->setStatusCode(400);

            } else {
                if ($data['password']) {
                    $data['password'] = bcrypt($data['password']);
                }
                $user->update($data);
                return response()->json(compact('user'))->setStatusCode(200);
            }

        } catch (ModelNotFoundException $e) {
            $error = 'user not found.';
            return response()->json(compact('error'))->setStatusCode(404);
        }

    }

    public function destroy($id)
    {
        try{
            $user = User::findOrFail($id);
            $user->delete();
            $message = 'user deleted.';
            return response()->json(compact('message'))->setStatusCode(200);

        } catch (ModelNotFoundException $e) {
            $error = 'user not found';
            return response()->json(compact('error'))->setStatusCode(404);
        }
    }
}
