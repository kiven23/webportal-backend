<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\ProductBrand as Brand;
use Session;
use Validator;

class ProductBrandController extends Controller
{

    public function __construct () {
      $this->middleware(['auth', 'product_brand_clearance']);

      // for active routing state
      \View::share('is_sc_product_brand_route', true);
    }

    public function index () {
    	$brands = Brand::orderBy('name', 'asc')->get();
    	return view('products.brands.index', compact('brands'));
    }

    public function create () {
    	return view('products.brands.create');
    }

    public function store (Request $req) {
        $rules = [
            'name' => 'required|unique:product_brands,name',
        ];
        $messages = [
            'name.unique' => 'The item ' . $req->name . ' is already in our database. ' . 'Please choose another name.',
        ];
    	$validator = Validator::make($req->all(), $rules, $messages);
    	if ($validator->fails()) {
    		$flash_message = [
    			'title' => 'Oops!',
    			'status' => 'danger',
    			'message' => 'Please correct all the errors below.',
    		];
    		Session::flash('create_fail', $flash_message);
    		return redirect()->back()
    						 ->withErrors($validator)
    						 ->withInput();
    	}

    	$brand = new Brand;
    	$brand->name = $req->name;
    	$brand->save();

    	$flash_message = [
			'title' => 'Well done!',
			'status' => 'success',
			'message' => $req->name . ' brand has been successfully added into our database.',
		];
    	Session::flash('create_success', $flash_message);

    	if ($req->savebtn == 0) {
    		return redirect()->route('brand.create');
    	} else {
    		return redirect()->route('brands');
    	}
    }

    public function edit ($id) {
    	$brand = Brand::find($id);
    	return view('products.brands.edit', compact('brand'));
    }

    public function update ($id, Request $req) {
        $rules = [
            'name' => 'required|unique:product_brands,name,'.$id,
        ];

        $message = [
            'name.unique' => 'The item ' . $req->name . ' is already in our database. ' . 'Please choose another name.',
        ];
    	$validator = Validator::make($req->all(), $rules, $message);
    	if ($validator->fails()) {
    		$flash_message = [
    			'title' => 'Oops!',
    			'status' => 'danger',
    			'message' => 'Please correct all the errors below.',
    		];
    		Session::flash('update_fail', $flash_message);
    		return redirect()->back()
    						 ->withErrors($validator)
    						 ->withInput();
    	}

    	$brand = Brand::find($id);
    	$brand->name = $req->name;
    	$brand->update();

    	$flash_message = [
			'title' => 'Well done!',
			'status' => 'success',
			'message' => 'One (1) record has been successfully updated.',
		];
    	Session::flash('update_success', $flash_message);
    	return redirect()->route('brands');
    }

    public function trash ($id) {
        $brand = Brand::where('id', $id)
                                     ->with('items')->first();
        return view('products.brands.trash', compact('brand'));
    }

    public function delete ($id) {
        try {
            $brand = Brand::find($id);
            $brand_name = $brand->name;
            $brand->delete();
        } catch (\Exception $e) {
            return redirect()->route('brands', ['err' => '1']);
        }
        $flash_message = [
            'title' => 'Well done!',
            'status' => 'success',
            'message' => $brand_name . ' has been successfully deleted from our records.',
        ];
        Session::flash('delete_success', $flash_message);
        return redirect()->route('brands');
    }














    // api
    public function all () {
      $brands = Brand::select('id', 'name')
                ->with(['items' => function ($qry) {
                  $qry->select('id', 'product_brand_id');
                }])
                ->get();
      return response()->json($brands, 200);
    }

    public function store_api (Request $req) {
      $validator = Validator::make($req->all(), [
        'name' => 'required|unique:product_brands,name',
      ]);

      if ($validator->fails()) {
        $errors = $validator->errors()->getMessages();
        $obj = $validator->failed();
        $result = [];
        foreach($obj as $input => $rules){
          $i = 0;
          foreach($rules as $rule => $ruleInfo){
            $key = $rule;
            $key = $input.'|'.strtolower($key);
            $result[$key] = $errors[$input][$i];
            $i++;
          }
        }
        return response()->json($result, 422);
      }

      $brand = new Brand;
      $brand->name = $req->name;
      $brand->save();

      $brand = Brand::select('id', 'name')
               ->with(['items' => function ($qry) {
                 $qry->select('id', 'product_brand_id');
               }])
               ->where('id', $brand->id)->first();

      return response()->json($brand, 200);
    }

    public function update_api (Request $req) {
      $validator = Validator::make($req->all(), [
        'name' => 'required|unique:product_brands,name,'.$req->id,
      ]);

      if ($validator->fails()) {
        $errors = $validator->errors()->getMessages();
        $obj = $validator->failed();
        $result = [];
        foreach($obj as $input => $rules){
          $i = 0;
          foreach($rules as $rule => $ruleInfo){
            $key = $rule;
            $key = $input.'|'.strtolower($key);
            $result[$key] = $errors[$input][$i];
            $i++;
          }
        }
        return response()->json($result, 422);
      }

      $brand = Brand::select('id', 'name')->where('id', $req->id)->first();
      $brand->name = $req->name;
      $brand->update();

      return response()->json($brand, 200);
    }

    public function delete_multiple (Request $req) {
      $ids = $req;
      $brand = Brand::whereIn('id', $ids)
               ->with(['items' => function ($qry) {
                 $qry->select('id', 'product_brand_id');
               }])
               ->select('id', 'name')->get();
      $response = $brand;
      Brand::whereIn('id', $ids)->delete();

      return response()->json($response, 200);
    }
}
