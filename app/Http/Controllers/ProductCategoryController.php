<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\ProductCategory as Category;
use Session;
use Validator;

class ProductCategoryController extends Controller
{

    public function __construct () {
      $this->middleware(['auth', 'product_category_clearance']);

      // for active routing state
      \View::share('is_sc_product_category_route', true);
    }

    public function index () {
    	$categories = Category::orderBy('name', 'asc')->get();
    	return view('products.categories.index', compact('categories'));
    }

    public function create () {
    	return view('products.categories.create');
    }

    public function store (Request $req) {
        $rules = [
            'name' => 'required|unique:product_categories,name',
        ];
        $messages = [
            'name.unique' => 'Product category ' . $req->name . ' is already in our database. ' . 'Please choose another name.',
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

    	$category = new Category;
    	$category->name = $req->name;
    	$category->save();

    	$flash_message = [
			'title' => 'Well done!',
			'status' => 'success',
			'message' => $req->name . ' category has been successfully added into our database.',
		];
    	Session::flash('create_success', $flash_message);

    	if ($req->savebtn == 0) {
    		return redirect()->route('category.create');
    	} else {
    		return redirect()->route('categories');
    	}
    }

    public function edit ($id) {
    	$category = Category::find($id);
    	return view('products.categories.edit', compact('category'));
    }

    public function update ($id, Request $req) {
        $rules = [
            'name' => 'required|unique:product_categories,name,'.$id,
        ];

        $message = [
            'name.unique' => 'Product category ' . $req->name . ' is already in our database. ' . 'Please choose another name.',
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

    	$category = Category::find($id);
    	$category->name = $req->name;
    	$category->update();

    	$flash_message = [
			'title' => 'Well done!',
			'status' => 'success',
			'message' => 'One (1) record has been successfully updated.',
		];
    	Session::flash('update_success', $flash_message);
    	return redirect()->route('categories');
    }

    public function trash ($id) {
        $category = Category::where('id', $id)
                                     ->with('items')->first();
        return view('products.categories.trash', compact('category'));
    }

    public function delete ($id) {
        try {
            $category = Category::find($id);
            $category_name = $category->name;
            $category->delete();
        } catch (\Exception $e) {
            return redirect()->route('categories', ['err' => '1']);
        }
        $flash_message = [
            'title' => 'Well done!',
            'status' => 'success',
            'message' => $category_name . ' has been successfully deleted from our records.',
        ];
        Session::flash('delete_success', $flash_message);
        return redirect()->route('categories');
    }


















    // api
    public function all () {
      $categories = Category::select('id', 'name')
                    ->with(['items' => function ($qry) {
                      $qry->select('id', 'product_category_id');
                    }])
                    ->get();
      return response()->json($categories, 200);
    }

    public function store_api (Request $req) {
      $validator = Validator::make($req->all(), [
        'name' => 'required|unique:product_categories,name',
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

      $category = new Category;
      $category->name = $req->name;
      $category->save();

      $category = Category::select('id', 'name')
                  ->with(['items' => function ($qry) {
                    $qry->select('id', 'product_category_id');
                  }])
                  ->where('id', $category->id)
                  ->first();

      return response()->json($category, 200);
    }

    public function update_api (Request $req) {
      $validator = Validator::make($req->all(), [
        'name' => 'required|unique:product_categories,name,'.$req->id,
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

      $category = Category::find($req->id);
      $category->name = $req->name;
      $category->update();

      $category = Category::select('id', 'name')
                  ->with(['items' => function ($qry) {
                    $qry->select('id', 'product_category_id');
                  }])            
                  ->where('id', $category->id)
                  ->first();

      return response()->json($category, 200);
    }

    public function delete_multiple (Request $req) {
      $ids = $req;
      $category = Category::whereIn('id', $ids)
                  ->with(['items' => function ($qry) {
                    $qry->select('id', 'product_category_id');
                  }])
                  ->select('id', 'name')->get();
      $response = $category;
      Category::whereIn('id', $ids)->delete();

      return response()->json($response, 200);
    }
}
