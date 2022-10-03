<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Validator;
class ApiController extends Controller
{
    public function register(Request $request)
    {
        
        $messages = [
        'name.required' => 'We need to know name',
        'name.min' => 'Name should have min 4 characters',
        'email.required' => 'Email is required',
        'email.email' => 'Please Provide Valid Email',
        'email.unique' => 'User with this email is already registered',
        'password.required' => 'Password field is Required',
        'password.min' => "Password should have atleast 8 characters",
        ];

        $validator = Validator::make($request->all(),[
            'name' => 'required|min:4',
            'email' => 'required|email:rfc,dns|unique:users',
            'password' => 'required|min:8',
        ], $messages);


        if ($validator->fails()) 
        {
            $messages = $validator->messages();

            $error_message = "";
            $i = 0;
            $len = count($messages->all());
            foreach ($messages->all() as $message)
            {
                $i++;
                $error_message.= $message;
                if($i<$len)
                {
                    $error_message.=", "; 
                }
            }

            return response()->json(['status' => 0,'message' => $error_message], 201);
        }
 
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
       
        $token = $user->createToken('LaravelAuthApp')->accessToken;
        return response()->json(['status'=>1, 'token' => $token], 201);
    }


     /**
     * Login
     */
    public function login(Request $request)
    {
        $messages = [
            'email.required' => 'Email is required',
            'email.email' => 'Please Provide Valid Email',
            'password.required' => 'Password field is Required',
            'password.min' => "Password should have atleast 8 characters",
            ];
    
        $validator = Validator::make($request->all(),[
            'email' => 'required|email:rfc,dns',
            'password' => 'required|min:8',
        ], $messages);


        if ($validator->fails()) 
        {
            $messages = $validator->messages();

            $error_message = "";
            $i = 0;
            $len = count($messages->all());
            foreach ($messages->all() as $message)
            {
                $i++;
                $error_message.= $message;
                if($i<$len)
                {
                    $error_message.=", "; 
                }
            }

            return response()->json(['status' => 0,'message' => $error_message], 201);
        }
        
        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];
 
        if (auth()->attempt($data)) {
            $token = auth()->user()->createToken('LaravelAuthApp')->accessToken;
            return response()->json(['status'=> 1, 'message' => 'Logged In Successfully', 'token' => $token], 200);
        } else {
            return response()->json(['status'=> 0, 'message' => 'Unauthorised Access'], 401);
        }
        
    }   

}
