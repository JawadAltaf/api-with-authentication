<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request){

        $Validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required'
        ]);

       if($Validator->fails()){
            $response = [
                'message' => $Validator->messages(),
                'status' => 0
            ];
            $respCode = 400;
       }else{
            DB::beginTransaction();
            try{
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password), // Hash the password
                ]);
                $token = $user->createToken("auth_token")->accessToken;
                DB::commit();
            }catch(\Exception $e){
                DB::rollBack();
                $user = null;
            }

            if(is_null($user)){
                $response = [
                    'message' => 'Internal Server Error',
                    'status' => 0
                ];
                $respCode = 500;
            }else{
                $response = [
                    'token' => $token,
                    'user' => $user,
                    'message' => 'User Register Successfully',
                    'status' => 1
                ];
                $respCode = 200;
            }
       }

       return response()->json($response,$respCode);
    }


    public function login(Request $request){

        $Validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if($Validator->fails()){
            $response = [
                'message' => $Validator->messages(),
                'status' => 0
            ];
            $respCode = 400;
       }else{
            $user = User::where(['email' => $request->email])->first();

            if($user && Hash::check($request->password, $user->password)){
                $token = $user->createToken("auth_token")->accessToken;
                $response = [
                    'token' => $token,
                    'user' => $user,
                    'message' => 'Logged in Successfully',
                    'status' => 1
                ];
                $respCode = 200;
            }
       }

       return response()->json($response,$respCode);
    }

    public function getUser($id){
        $user = User::find($id);

        if(is_null($user)){
            $response = [
                'message' => 'User not found',
                'user' => null,
                'status' => 0
            ];
            $respCode = 200;
        }else{
            $response = [
                'user' => $user,
                'message' => 'User Found',
                'status' => 1
            ];
            $respCode = 200;
        }

        return response()->json($response,$respCode);
    }
}