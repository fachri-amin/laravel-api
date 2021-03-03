<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller
{
    
    public function register(Request $request){
        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required|email',
            'password'=>'required'
        ]);

        if($validator->fails()){
            return response()->json(['status'=>'failed', 'validation_errors'=>$validation->errors()]);
        }

        $inputs = $request->all();
        $inputs['password'] = Hash::make($request->password);

        $user = User::create($inputs);

        if(!is_null($user)){
            return response()->json(['status'=>'success', 'message'=>'Success, registration completed', 'data'=>$user]);
        }
        else{
            return response()->json(["status" => "failed", "message" => "Registration failed!"]);
        }
    }

    public function login(Request $request){
        $validator = Validator::make($request->all(),[
            'email'=>'required|email',
            'password'=>'required'
        ]);

        if($validator->fails()){
            return response()->json(['status'=>'failed', 'validation_errors'=>$validation->errors()]);
        }

        $user = User::where("email", $request->email)->first();

        
        if(is_null($user)){
            return response()->json(["status" => "failed", "message" => "Invalid Login"]);
        }
        
        if(Auth::attempt(['email'=>$request->email, 'password'=>$request->password])){
            $user = Auth::user();
            $token = $user->createToken('token')->plainTextToken;

            return response()->json(["status" => "success", "login" => true, "token" => $token, "data" => $user]);
        }
        else {
            return response()->json(["status" => "failed", "success" => false, "message" => "Invalid Login"]);
        }
    }

    public function logout(){
        $user = Auth::user();

        if(!is_null($user)){
            $deleted = $user->currentAccessToken()->delete();

            if($deleted){
                return response()->json(['status'=>'success', 'message'=>'Logout success']);
            }
            else{
                return response()->json(['status'=>'failed', 'message'=>'Logout fail']);
            }
        }
        else{
            return response()->json(["status" => "failed", "message" => "Whoops! no user found"]);
        }
    }

    public function user(Request $request){
        $user = Auth::user();
        
        if(!is_null($user)) { 
            return response()->json(["status" => "success", "data" => $user]);
        }

        else {
            return response()->json(["status" => "failed", "message" => "Whoops! no user found"]);
        }        
    }

}
