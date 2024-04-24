<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Auth;

class RegisterController extends Controller
{
    public function register(Request $request)
    {   
        
        $validation = Validator::make($request->all(), [

            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
            'confirmPassword' => 'required|same:password',
            
            'firstname' => 'required|max:255',
            'lastName' => 'required|max:255',
            'phone' => 'required',
            'gender' => 'required',
            'country' => 'required',
            'dob' => 'required',
            'citizenship' => 'required',
            
        ]);

        if ($validation->fails()) {
            return err('Validation error.', $validation->errors());
        }

        $user = User::create([
            'firstname' => $request->firstname,
            'lastName' => $request->lastName,
            
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'gender' => $request->gender,
            'country' => $request->country,
            'dob' => $request->dob,
            'citizenship' => $request->citizenship,
            
        ]);

        $success['token'] = $user->createToken('registration_token')->plainTextToken;
        $success['name'] = $user->firstname . ' ' . $user->lastName;

        return res($success, 'Registration complete.');
    }

    public function login(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required|min:6',
        ]);

        if ($validation->fails()) {
            return err('Validation error.', $validation->errors());
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $login['token'] = $user->createToken('Login token.')->plainTextToken;
            $login['user'] = $user->name;

            return res('User logged in.', $login);
        } else {
            return err("Credential doesn't match.", ['err' => 'Unauthorized']);
        }
    }
}
