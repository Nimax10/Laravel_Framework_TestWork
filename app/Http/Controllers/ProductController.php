<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Product;
use Validator;

class ProductController extends Controller
{
    public function Create_Product (Request $req) {
    	$validator = Validator::make($req->all(), [
    		'login' => 'required',
    		'product' => 'required|unique:products,product',
    		'price' => 'required',
    		'description' => 'required',
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
    		if($user) {
    				if($user->api_token != null) {
    					$product = new Product();
			    		$product->product = $req->input('product');
				    	$product->price = $req->input('price');
				    	$product->description = $req->input('description');

				    	$product->save();
				    	return response()->json([
				    		'message' => "Product added!",
	    				]);
    				} else {
    					return response()->json("This user is not authorized!");
    				}
	    	} else {
    			return response()->json("This user does not exist!"); 
    		}
    	}
    }
    public function Edit_Product (Request $req) {
    	$validator = Validator::make($req->all(), [
    		'login' => 'required',
    		'product' => 'required',
    		'сhange_on' => 'required|unique:products,price',
    		'change_to' => 'required',
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
    		$product = Product::where("product", $req->product)->first();
    		$user = User::where("login", $req->login)->first();
    		if($product) {
    			if($user) {
    				if($user->api_token != null) {
    					if ($req->change_to == 'product') {
    						$product->product = $req->input('сhange_on');
    					} elseif ($req->change_to == 'price') {
    						$product->price = $req->input('сhange_on');
    					} elseif ($req->change_to == 'description') {
    						$product->description = $req->input('сhange_on');
    					} else {
    						return response()->json("The entered data is incorrect!");
    					}
		    			$product->save();
		    			return response()->json(
		    					[
		    						"message" => "Product changed!",
		    						"recovery_key" => "New data: ".$req->input('сhange_on'),
		    					]
		    				);
    				} else {
    					return response()->json("This user is not authorized!");
    				}
    			} else {
    				return response()->json("This user does not exist!"); 
    			}
    		} else {
    			return response()->json("This product does not exist!");
    		}
    	}
    }
    public function Delete_Product (Request $req) { 
    	$validator = Validator::make($req->all(), [
    		'login' => 'required',
    		'product' => 'required',
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
    		$product = Product::where("product", $req->product)->first();
    		$user = User::where("login", $req->login)->first();
    		if($product) {
    			if($user) {
    				if($user->api_token != null) {
    					Product::where("product", $req->product)->take(1)->delete();
    					return response()->json("This product has been removed!");
    				} else {
    					return response()->json("This user is not authorized!");
    				}
    			} else {
    				return response()->json("This user does not exist!"); 
    			}
    		} else {
    			return response()->json("This product does not exist!");
    		}
    	}
    }
}
