<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\ProductItem as Item;
use App\ProductBrand as Brand;
use App\ProductCategory as Category;
use App\ComputerwareTicket as CompTicket;
use Session;
use Validator;

class ProductItemController extends Controller
{

    public function __construct () {
      $this->middleware(['auth', 'product_item_clearance']);

      // for active routing state
      \View::share('is_sc_product_item_route', true);
    }

    public function index () {
    	$items = Item::orderBy('model', 'asc')->get();
    	return view('products.items.index', compact('items', 'brands', 'categories'));
    }

    public function create () {
    	$brands = Brand::orderBy('name', 'asc')->get();
    	$categories = Category::orderBy('name', 'asc')->get();
    	return view('products.items.create', compact('brands', 'categories'));
    }

    public function store (Request $req, $computerware_id) {
        $validator = Validator::make($req->all(), [
          		  'model' => 'required|unique:product_items,model',
          	]);

    	  // BRAND DUPLICATE
        $check_brand = Brand::where('name', $req->brand_input)->first();
        if (count($check_brand) > 0) {
            $duplicate = 'Duplicate! Please choose another name.';
            $validator->after(function ($validator) use ($duplicate) {
                $validator->getMessageBag()->add('brand_duplicate', $duplicate);
            });
        }
        // BRAND EMPTY
        if (!$req->brand_select && $req->brand_input == '') {
            $duplicate = 'The Brand input is required.';
            $validator->after(function ($validator) use ($duplicate) {
                $validator->getMessageBag()->add('brand_input', $duplicate);
            });
        }

        // CATEGORY DUPLICATE
        $check_category = Category::where('name', $req->category_input)->first();
        if (count($check_category) > 0) {
            $empty = 'Duplicate! Please choose another name.';
            $validator->after(function ($validator) use ($empty) {
                $validator->getMessageBag()->add('category_duplicate', $empty);
            });
        }
        // CATEGORY EMPTY
        if (!$req->category_select && $req->category_input == '') {
            $empty = 'The Category input is required.';
            $validator->after(function ($validator) use ($empty) {
                $validator->getMessageBag()->add('category_input', $empty);
            });
        }

    	  if ($validator->fails()) {
        		$flash_message = [
        			'title' => 'Oops!',
        			'status' => 'danger',
        			'message' => 'Please correct all the errors below.',
        		];
        		Session::flash('create_fail', $flash_message);

            // BRAND
            if ($req->brand_select) {
                Session::flash('brand_select', 1);
            } else {
                Session::flash('brand_select', 0);
            }

            // CATEGORY
            if ($req->category_select) {
                Session::flash('category_select', 1);
            } else {
                Session::flash('category_select', 0);
            }
      		    return redirect()->back()
      						 ->withErrors($validator)
      						 ->withInput();
    	  }

      	// BRAND
      	if ($req->brand_input) {
            $brand = new Brand;
            $brand->name = $req->brand_input;
            $brand->save();
        }

        // CATEGORY
        if ($req->category_input) {
            $category = new Category;
            $category->name = $req->category_input;
            $category->save();
        }

      	$item = new Item;

      	// BRAND
      	if ($req->brand_select) {
              $item->product_brand_id = $req->brand_select;
        } else {
            $item->product_brand_id = $brand->id;
        }

	      // CATEGORY
        if ($req->category_select) {
            $item->product_category_id = $req->category_select;
        } else {
            $item->product_category_id = $category->id;
        }

      	$item->model = $req->model;
      	$item->save();

      	$flash_message = [
    			'title' => 'Well done!',
    			'status' => 'success',
    			'message' => 'Item with ' . $req->model . ' model has been successfully added into our database.',
    		];
      	Session::flash('create_success', $flash_message);

      	if ($req->savebtn == 0) {
      		return redirect()->route('item.create');
      	} else if ($req->savebtn == 1) {
      		return redirect()->route('items');
      	} else if ($req->savebtn == 2) {
            Session::flash('item', $item->id);
            return redirect()->route('ticket.computerware.create');
        } else if ($req->savebtn == 3) {
            Session::flash('item', $item->id);
            return redirect()->route('ticket.computerware.create');
        } else if ($req->savebtn == 4) {
            Session::flash('item', $item->id);
            return redirect()->route('ticket.computerware.edit', ['id' => $computerware_id]);
        }
    }

    public function edit ($id) {
    	$item = Item::find($id);
    	$brands = Brand::orderBy('name', 'asc')->get();
    	$categories = Category::orderBy('name', 'asc')->get();
    	return view('products.items.edit', compact('item', 'brands', 'categories'));
    }

    public function update ($id, Request $req) {
    	$validator = Validator::make($req->all(), [
        		'model' => 'required|unique:product_items,model,'.$id,
        	]);

    	// BRAND DUPLICATE
        $check_brand = Brand::where('name', $req->brand_input)->first();
        if (count($check_brand) > 0) {
            $duplicate = 'Duplicate! Please choose another name.';
            $validator->after(function ($validator) use ($duplicate) {
                $validator->getMessageBag()->add('brand_duplicate', $duplicate);
            });
        }
        // BRAND EMPTY
        if (!$req->brand_select && $req->brand_input == '') {
            $duplicate = 'The Brand input is required.';
            $validator->after(function ($validator) use ($duplicate) {
                $validator->getMessageBag()->add('brand_input', $duplicate);
            });
        }

        // CATEGORY DUPLICATE
        $check_category = Category::where('name', $req->category_input)->first();
        if (count($check_category) > 0) {
            $empty = 'Duplicate! Please choose another name.';
            $validator->after(function ($validator) use ($empty) {
                $validator->getMessageBag()->add('category_duplicate', $empty);
            });
        }
        // CATEGORY EMPTY
        if (!$req->category_select && $req->category_input == '') {
            $empty = 'The Category input is required.';
            $validator->after(function ($validator) use ($empty) {
                $validator->getMessageBag()->add('category_input', $empty);
            });
        }

    	if ($validator->fails()) {
    		$flash_message = [
    			'title' => 'Oops!',
    			'status' => 'danger',
    			'message' => 'Please correct all the errors below.',
    		];
    		Session::flash('create_fail', $flash_message);

            // BRAND
            if ($req->brand_select) {
                Session::flash('brand_select', 1);
            } else {
                Session::flash('brand_select', 0);
            }

            // CATEGORY
            if ($req->category_select) {
                Session::flash('category_select', 1);
            } else {
                Session::flash('category_select', 0);
            }
    		return redirect()->back()
    						 ->withErrors($validator)
    						 ->withInput();
    	}

    	// BRAND
    	if ($req->brand_input) {
            $brand = new Brand;
            $brand->name = $req->brand_input;
            $brand->save();
        }

        // CATEGORY
        if ($req->category_input) {
            $category = new Category;
            $category->name = $req->category_input;
            $category->save();
        }

    	$item = Item::find($id);

    	// BRAND
    	if ($req->brand_select) {
            $item->product_brand_id = $req->brand_select;
        } else {
            $item->product_brand_id = $brand->id;
        }

		// CATEGORY
        if ($req->category_select) {
            $item->product_category_id = $req->category_select;
        } else {
            $item->product_category_id = $category->id;
        }

    	$item->model = $req->model;
    	$item->update();

    	$flash_message = [
			'title' => 'Well done!',
			'status' => 'success',
			'message' => 'One (1) record has been successfully updated.',
		];
    	Session::flash('update_success', $flash_message);

        if ($req->updatebtn == 0) {
            return redirect()->route('items');
        } else {
            Session::flash('item', $item->id);
            return redirect()->route('ticket.computerware.create');
        }
    }

    public function trash ($id) {
        $item = Item::where('id', $id)
                                     ->with('brand')
                                     ->with('category')
                                     ->first();
        return view('products.items.trash', compact('item'));
    }

    public function delete ($id) {
        try {
            $item = Item::find($id);
            $item_model = $item->model;
            $item->delete();
        } catch (\Exception $e) {
            return redirect()->route('items', ['err' => '1']);
        }
        $flash_message = [
            'title' => 'Well done!',
            'status' => 'success',
            'message' => 'Item with model ' . $item_model . ' has been successfully deleted from our records.',
        ];
        Session::flash('delete_success', $flash_message);
        return redirect()->route('items');
    }




    // FOR COMPUTERWARE TICKET
    public function computerware_create_newitem () {
        $brands = Brand::orderBy('name', 'asc')->get();
        $categories = Category::orderBy('name', 'asc')->get();
        return view('products.items.computerwares.create.newitem', compact('brands', 'categories'));
    }

    public function computerware_edit_newitem ($id) {
        $computerware = CompTicket::find($id);
        $brands = Brand::orderBy('name', 'asc')->get();
        $categories = Category::orderBy('name', 'asc')->get();
        return view('products.items.computerwares.edit.newitem', compact('brands', 'categories', 'computerware'));
    }





















    // api
    public function all () {
      $items = \DB::table('product_items')
                    ->select('product_items.id',
                              \DB::raw('(SELECT COUNT(computerware_tickets.id) FROM computerware_tickets
                              WHERE computerware_tickets.product_item_id=product_items.id
                              GROUP BY computerware_tickets.product_item_id) AS computerwares'),
                             'product_brands.id AS brand_id',
                             'product_categories.id AS category_id',
                             'product_items.model',
                             'product_brands.name AS brand',
                             'product_categories.name AS category',
                             \DB::raw('CONCAT(product_brands.name," ",product_categories.name," (",product_items.model,")") AS item'))
                    ->join('product_brands', 'product_items.product_brand_id', '=', 'product_brands.id')
                    ->join('product_categories', 'product_items.product_category_id', '=', 'product_categories.id')
                    ->get();
      return response()->json($items, 200);
    }

    public function store_api (Request $req) {
      $validator = Validator::make($req->all(), [
        'model' => 'required|unique:product_items,model',
        'brand' => 'required',
        'category' => 'required',
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

      $item = new Item;
      $item->product_brand_id = $req->brand;
      $item->product_category_id = $req->category;
      $item->model = $req->model;
      $item->save();

      $item = \DB::table('product_items')
                    ->select('product_items.id',
                              \DB::raw('(SELECT COUNT(computerware_tickets.id) FROM computerware_tickets
                              WHERE computerware_tickets.product_item_id=product_items.id
                              GROUP BY computerware_tickets.product_item_id) AS computerwares'),
                             'product_brands.id AS brand_id',
                             'product_categories.id AS category_id',
                             'product_items.model',
                             'product_brands.name AS brand',
                             'product_categories.name AS category')
                    ->join('product_brands', 'product_items.product_brand_id', '=', 'product_brands.id')
                    ->join('product_categories', 'product_items.product_category_id', '=', 'product_categories.id')
                    ->where('product_items.id', $item->id)
                    ->first();

      return response()->json($item, 200);
    }

    public function update_api (Request $req) {
      $validator = Validator::make($req->all(), [
        'model' => 'required|unique:product_items,model,'.$req->id,
        'brand' => 'required',
        'category' => 'required',
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

      $item = Item::find($req->id);
      $item->product_brand_id = $req->brand;
      $item->product_category_id = $req->category;
      $item->model = $req->model;
      $item->update();

      $item = \DB::table('product_items')
                  ->select('product_items.id',
                            \DB::raw('(SELECT COUNT(computerware_tickets.id) FROM computerware_tickets
                            WHERE computerware_tickets.product_item_id=product_items.id
                            GROUP BY computerware_tickets.product_item_id) AS computerwares'),
                           'product_brands.id AS brand_id',
                           'product_categories.id AS category_id',
                           'product_items.model',
                           'product_brands.name AS brand',
                           'product_categories.name AS category')
                  ->join('product_brands', 'product_items.product_brand_id', '=', 'product_brands.id')
                  ->join('product_categories', 'product_items.product_category_id', '=', 'product_categories.id')
                  ->where('product_items.id', $item->id)
                  ->first();

      return response()->json($item, 200);
    }

    public function delete_multiple (Request $req) {
      $ids = $req;
      $item = Item::select('id',
                           'product_brand_id',
                           'product_category_id',
                           'model')
                    ->with(['brand' => function ($qry) {
                      $qry->select('id', 'name');
                    }])
                    ->with(['category' => function ($qry) {
                      $qry->select('id', 'name');
                    }])
                    ->whereIn('id', $ids)
                    ->get();
      $response = $item;
      Item::whereIn('id', $ids)->delete();

      return response()->json($response, 200);
    }
}
