<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\User;
use Validator;
use Str;

class UserController extends Controller
{
    public function SignUp(Request $req) {

    	// $error = [
    	// 	"required" => "This field is required!",
    	// 	"email" => "The email address you entered is incorrect!",
    	// 	"min" => "Small number of characters!",
    	// 	"max" => "Too many characters!",
    	// 	"unique" => "The data already exists!",
    	// ];

    	$validator = Validator::make($req->all(), [
    		'first_name' => 'required|min:4|unique:users,first_name',
    		'last_name' => 'required|min:4|unique:users,last_name',
    		'email' => 'required|email|unique:users,email',
    		'login' => 'required|min:4|max:16|unique:users,login',
    		'password' => 'required|min:6|max:20',
    	]);
    	if ($validator->fails()) {
    		return response()->json([
    			'error' => [
    				"code" => 422,
    				"message" => "Validation error",
    				"errors" => $validator->errors(),
    			]
    		], 422);
    	} else {
	    	$user = new User();
	    	$user->first_name = $req->input('first_name');
	    	$user->last_name = $req->input('last_name');
	    	$user->email = $req->input('email');
	    	$user->login = $req->input('login');
	    	$user->password = $req->input('password');
	    	$user->api_token = null;
	    	$user->recovery_key = rand(100000, 999999);

	    	$user->save();
	    	return response()->json([
	    		'message' => "Registration is successful!",
	    		"recovery_key" => "New code: ".$user->recovery_key
	    	]);
    	}
    }

    public function SignIn(Request $req) {
    	$validator = Validator::make($req->all(), [
    		'login' => 'required|min:4|max:16',
    		'password' => 'required|min:6|max:20',
    	]);
    	if ($validator->fails()) {
    		return response()->json([
    			'error' => [
    				"code" => 422,
    				"message" => "Validation error",
    				"errors" => $validator->errors(),
    			]
    		], 422);
    	} else {
    		$user = User::where("login", $req->login)->first();
    		if ($user) {
    			if ($req->password == $user->password) {
    				$user->api_token = Str::random(50);
    				$user->save();
    				return response()->json(
    					[
    						"api_token" => $user->api_token,
    						"message" => "You are logged in!"
    					]
    				);
    			} else {
    				return response()->json("The entered data is incorrect!");
    			}
    		} else {
    			return response()->json("This user does not exist!");
    		}
    	}
    }

    public function Password_Recovery(Request $req) {
    	$validator = Validator::make($req->all(), [
    		'login_or_email' => 'required',
    		'recovery_key' => 'required|min:6|max:6',
    		'new_password' => 'required|min:6|max:20',
    	]);
    	if ($validator->fails()) {
    		return response()->json([
    			'error' => [
    				"code" => 422,
    				"message" => "Validation error",
    				"errors" => $validator->errors(),
    			]
    		], 422);
    	} else {
    		$user = User::where("login", $req->login_or_email)->first();
    		if (!$user) {
    			$user = User::where("email", $req->login_or_email)->first();
    		} 
    		if ($user) {
    			if ($req->recovery_key == $user->recovery_key) {
    				$user->recovery_key = rand(100000, 999999);
    				$user->password = $req->input('new_password');
    				$user->api_token = null;
    				$user->save();
    				return response()->json(
    					[
    						"message" => "Password restored!",
    						"recovery_key" => "New code: ".$user->recovery_key,
    					]
    				);
    			} else {
    				return response()->json("The entered data is incorrect!");
    			}
    		} else {
    			return response()->json("This user does not exist!");
    		}
    	}
    }

    public function Logout(Request $req) {
    	$validator = Validator::make($req->all(), [
    		'logout' => 'required',
    		'login' => 'required|min:4|max:16',
    	]);
    	if ($validator->fails()) {
    		return response()->json([
    			'error' => [
    				"code" => 422,
    				"message" => "Validation error",
    				"errors" => $validator->errors(),
    			]
    		], 422);
    	} else {
    		if ($req->logout == 'true') {
    			$user = User::where("login", $req->login)->first();
    			if ($user) {
    				$user->api_token = null;
    				$user->save();
    				return response()->json("You are logged out!");
    			} else {
    				return response()->json("This user was not found!");
    			}
    		} else {
    			return response()->json("You did not complete the logout request!");
    		}
    	}
    }
    
}
