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
    	}
		$user = User::where("login", $req->login)->first();
		
        if(!$user) {    
            return response()->json("This user does not exist!"); 
        }
		if($user->api_token == null) {
            return response()->json("This user is not authorized!");
        }
        if ($user->rank != 'admin') {
            return response()->json("This user does not have administrator rights!");
        }
		$product = new Product();
		$product->product = $req->input('product');
    	$product->price = $req->input('price');
    	$product->description = $req->input('description');

    	$product->save();
    	return response()->json([
    		'message' => "Product added!",
		]);
    }
    public function Edit_Product (Request $req) {
    	$validator = Validator::make($req->all(), [
    		'login' => 'required',
    		'product' => 'required',
    		'сhange_on' => 'required|unique:products,price', //Изменить на "что"
    		'change_to' => 'required', //Изменить в "чём-то"
    	]);
    	if ($validator->fails()) {
    		return response()->json([
    			'error' => [
    				"code" => 422,
    				"message" => "Validation error",
    				"errors" => $validator->errors(),
    			]
    		], 422);
    	}
		$product = Product::where("product", $req->product)->first();
		$user = User::where("login", $req->login)->first();
		if(!$product) {
            return response()->json("This product does not exist!");
        }
		if(!$user) {
            return response()->json("This user does not exist!"); 
        }
		if($user->api_token == null) {
            return response()->json("This user is not authorized!");
        }
        if ($user->rank != 'admin') {
            return response()->json("This user does not have administrator rights!");
        }
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
    	}
		$product = Product::where("product", $req->product)->first();
		$user = User::where("login", $req->login)->first();

		if(!$product) {
            return response()->json("This product does not exist!");
        }
		if(!$user) {
            return response()->json("This user does not exist!"); 
        }
		if($user->api_token == null) {
            return response()->json("This user is not authorized!");
        }
        if ($user->rank != 'admin') {
            return response()->json("This user does not have administrator rights!");
        }
		Product::where("product", $req->product)->take(1)->delete();
		return response()->json("This product has been removed!");
    }
    public function Search_Products (Request $req) { 
        $validator = Validator::make($req->all(), [
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
        }
        $req->product = "%".$req->product."%";
        $product = Product::where("product", 'like', $req->product)->get();
        if(!$product) {
            return response()->json("This product does not exist!");
        }
        return response()->json($product);
    }
}
