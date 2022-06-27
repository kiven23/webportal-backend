<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Inventory;
use App\InventoryMap;
use App\Branch;
use App\User;

use DB;
use Excel;
use Alert;
use Session;
use Validator;
use Illuminate\Support\Collection;

class InventoryController extends Controller
{

    public function __construct () {
      $this->middleware(['auth', 'inventory_clearance']);

      // for active routing state
      \View::share('is_inventory_recon_route', true);
    }

    public function index () {
      if (\Auth::user()->branch->machine_number === 103) {
          $inventories = Inventory::with('inventory_maps')->get();
      } else {
          $inventories = Inventory::where('branch_id', \Auth::user()->branch->id)->get();
      }

    	return view('inventories.index', compact('inventories'));
    }

    public function create () {
    	$branches = Branch::orderBy('name', 'asc')->get();
    	return view('inventories.create', compact('branches'));
    }

    public function store (Request $req) {
        $validator = Validator::make($req->all(), [
                'file' => 'required',
                'branch_id' => 'required',
            ]);

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

    	$csv_path = $req->file->getRealPath();

        // for checking WHS CODE
        function multi_strpos($string, $check, $getResults = false)
        {
          $result = array();
          $checks = (array) $check;

          foreach ($check as $s)
          {
            $pos = strpos($string, $s);

            if ($pos !== false)
            {
              if ($getResults)
              {
                $result[$s] = $pos;
              }
              else
              {
                return $pos;
              }
            }
          }

          return empty($result) ? false : $result;
        }



    	try {

          $inventory = new Inventory;
          $inventory->user_id = \Auth::user()->id;
          $inventory->branch_id = $req->branch_id;
          $inventory->save();

          // Insert the data from a file
          Excel::load($csv_path, function($reader) use ($inventory) {
              foreach ($reader->toArray() as $csv) {
                  $inventory_map = new InventoryMap;
                  $inventory_map->inventory_id = $inventory->id;
                  $inventory_map->brand = $csv['brand'];
                  $inventory_map->model = $csv['model'];
                  $inventory_map->serial = $csv['serial'];
                  $inventory_map->price = $csv['price'];

                  // CHECK WHS CODE FROM SERIAL COLUMN
                  $check   = '';
                  // $serial_filter = Branch::pluck('whscode');
                  $whscodes = Branch::pluck('whscode');
                  $code_arr = [];
                  foreach ($whscodes as $key => $whscode) {
                    $wcode = array_pad(explode(',', $whscode), count($whscode), null);
                    for ($i = 0; $i < count($wcode); $i++) {
                      array_push($code_arr, $wcode[$i]);
                    }
                  }
                  $wcodes = array_values(array_filter($code_arr));
                  $checks = $wcodes;

                  if (is_array($found = multi_strpos($inventory_map->serial, $checks, true))) {
                      $inventory_map->quantity = 1;
                  }
                  $inventory_map->save();
              }
          });

    			$flash_message = [
                    'title' => 'Well done!',
                    'status' => 'success',
                    'message' => 'The file was successfully imported.',
                ];
    			Session::flash('create_success', $flash_message);

          if ($req->savebtn == 0) {
            return redirect()->route('inventory.create');
          } else {
            return redirect()->route('inventories');
          }

    	} catch (\Exception $e) {
        Inventory::find($inventory->id)->delete();
        InventoryMap::where('inventory_id', $inventory->id)->delete();
        $flash_message = [
            'title' => 'Oops!',
            'status' => 'danger',
            'message' => $e->getMessage() . '. Please upload a correct file.',
        ];
    		Session::flash('create_fail', $flash_message);
        return redirect()->back();
      }
    }

    public function trash ($id) {
        $inventory = Inventory::find($id);
        return view('inventories.trash', compact('inventory'));
    }

    public function delete ($id) {
        Inventory::find($id)->delete();
        InventoryMap::where('inventory_id', $id)->delete();
        $flash_message = [
            'title' => 'Well done!',
            'status' => 'success',
            'message' => 'File has been successfully deleted.',
        ];
        Session::flash('delete_success', $flash_message);
        return redirect()->route('inventories');
    }

    public function breakdown_view ($id) {
        $inventory_maps = InventoryMap::orderBy('model', 'asc')
                        ->orderBy('brand', 'asc')
                        ->where('inventory_id', $id)
                        ->where('quantity', null)
                        ->get();

        $inventory_maps2 = DB::table('inventory_maps')
                     ->where('inventory_id', $id)
                     ->where('quantity', 1)
                     ->select('brand', 'model', 'quantity_branch', DB::raw('count(model) as quantity'), 'price')
                     ->groupBy('model', 'brand', 'quantity_branch', 'price')
                     ->get();

        $inventory_maps3 = DB::table('inventory_maps')
                     ->where('inventory_id', $id)
                     ->select('brand', DB::raw('sum(price) as total'))
                     ->groupBy('brand')
                     ->orderBy('total', 'desc')
                     ->get();

        // insert total price per brand
        foreach ($inventory_maps as $key => $inventory_map) {
            foreach ($inventory_maps3 as $inventory_map3) {
                if ($inventory_map['brand'] == $inventory_map3->brand) {
                    $inventory_map['total_price'] = $inventory_map3->total;
                }
            }
        }
        foreach ($inventory_maps2 as $key => $inventory_map2) {
            foreach ($inventory_maps3 as $inventory_map3) {
                if ($inventory_map2->brand == $inventory_map3->brand) {
                    $inventory_map2->total_price = $inventory_map3->total;
                }
            }
        }

        foreach ($inventory_maps2 as $inventory_map2) {
            $inventory_maps->push([
                                'inventory_id' => $id,
                                'brand' => $inventory_map2->brand,
                                'model' => $inventory_map2->model,
                                'serial' => 0,
                                'serial_branch' => 0,
                                'quantity' => $inventory_map2->quantity,
                                'quantity_branch' => $inventory_map2->quantity_branch,
                                'price' => $inventory_map2->price,
                                'total_price' => $inventory_map2->total_price,
                            ]);
        }
        $inventory_maps = $inventory_maps->sortByDesc('price')->sortByDesc('total_price');
        $branch_name = Inventory::where('id', $id)->first()->branch->name;
        return view('inventories.breakdown_view', compact('inventory_maps', 'branch_name'));
    }

    public function discrepancy ($id) {
        $branch = Inventory::find($id)->branch;

        $upload_date = InventoryMap::where('inventory_id', $id)->where('updated_at', '!=', null)->pluck('updated_at')->first();
        $upload_date = \Carbon\Carbon::parse($upload_date)->format('m-d-y');

        $inventory_maps = new Collection;
        $inventory_maps = InventoryMap::orderBy('model', 'asc')
                        ->where('inventory_id', $id)
                        ->where('quantity', null)
                        ->select('inventory_id',
                                 'brand',
                                 'model',
                                 'total_price',
                                 DB::raw('count(serial) as quantity'),
                                 DB::raw('count(serial_branch) as quantity_branch'),
                                 DB::raw('group_concat(serial) as serial'),
                                 DB::raw('group_concat(serial_branch) as serial_branch'))
                        ->groupBy('inventory_id',
                                  'brand',
                                  'model',
                                  'total_price')
                        ->get();

        $inventory_maps2 = DB::table('inventory_maps')
                     ->where('inventory_id', $id)
                     ->where('quantity', 1)
                     ->select('brand', 'model', 'quantity_branch', DB::raw('count(model) as quantity'), 'total_price')
                     ->groupBy('model', 'brand', 'quantity_branch', 'total_price')
                     ->get();

        $inventory_maps3 = DB::table('inventory_maps')
                     ->where('inventory_id', $id)
                     ->where('serial', null)
                     ->where('serial_branch', null)
                     ->where('quantity', null)
                     ->select('brand', 'model', 'quantity_branch', 'total_price')
                     ->groupBy('model', 'brand', 'quantity_branch', 'total_price')
                     ->get();

        $inventory_maps4 = DB::table('inventory_maps')
                     ->where('inventory_id', $id)
                     ->select('brand', DB::raw('sum(price) as total'))
                     ->groupBy('brand')
                     ->orderBy('total', 'desc')
                     ->get();

        // insert total price per brand
        foreach ($inventory_maps as $key => $inventory_map) {
            foreach ($inventory_maps4 as $inventory_map4) {
                if ($inventory_map['brand'] == $inventory_map4->brand) {
                    $inventory_map['total_price'] = $inventory_map4->total;
                }
            }
        }
        foreach ($inventory_maps2 as $key => $inventory_map2) {
            foreach ($inventory_maps4 as $inventory_map4) {
                if ($inventory_map2->brand == $inventory_map4->brand) {
                    $inventory_map2->total_price = $inventory_map4->total;
                }
            }
        }
        foreach ($inventory_maps3 as $key => $inventory_map3) {
            foreach ($inventory_maps4 as $inventory_map4) {
                if ($inventory_map3->brand == $inventory_map4->brand) {
                    $inventory_map3->total_price = $inventory_map4->total;
                }
            }
        }

        foreach ($inventory_maps2 as $inventory_map2) {
            $inventory_maps->push([
                                'inventory_id' => $id,
                                'brand' => $inventory_map2->brand,
                                'model' => $inventory_map2->model,
                                'serial' => 0,
                                'serial_branch' => 0,
                                'quantity' => $inventory_map2->quantity,
                                'quantity_branch' => $inventory_map2->quantity_branch,
                                'total_price' => $inventory_map2->total_price
                            ]);
        }

        foreach ($inventory_maps3 as $inventory_map3) {
            $inventory_maps->push([
                                'inventory_id' => $id,
                                'brand' => $inventory_map3->brand,
                                'model' => $inventory_map3->model,
                                'serial' => 0,
                                'serial_branch' => 0,
                                'quantity' => 0,
                                'quantity_branch' => $inventory_map3->quantity_branch,
                                'total_price' => $inventory_map3->total_price
                            ]);
        }

        $inventory_maps = $inventory_maps->sortByDesc('total_price');
        return view('inventories.discrepancy', compact('branch', 'inventory_maps', 'upload_date'));
    }


    // BRANCH
    public function get_raw ($id) {
        $inventory_maps = DB::table('inventory_maps')
                        ->select('inventory_id', 'brand', 'model')
                        ->where('inventory_id', $id)
                        ->orderBy('inventory_id', 'brand', 'asc')
                        ->groupBy('inventory_id', 'brand', 'model')
                        ->get();
        return view('inventories.branch.get_raw', compact('inventory_maps'));
    }

    public function view ($id) {
        $inventory_maps = new Collection;
        $inventory_maps = InventoryMap::orderBy('model', 'asc')
                        ->where('inventory_id', $id)
                        ->where('quantity', null)
                        ->select('inventory_id',
                                 'brand',
                                 'model',
                                 'quantity',
                                 'quantity_branch',
                                 DB::raw('group_concat(serial) as serial'),
                                 DB::raw('group_concat(serial_branch) as serial_branch'))
                        ->groupBy('inventory_id',
                                  'brand',
                                  'model',
                                  'quantity',
                                  'quantity_branch')
                        ->get();
        $inventory_maps2 = DB::table('inventory_maps')
                     ->where('inventory_id', $id)
                     ->where('quantity', 1)
                     ->select('brand', 'model', 'quantity_branch', 'quantity')
                     ->groupBy('model', 'brand', 'quantity_branch', 'quantity')
                     ->get();

        foreach ($inventory_maps2 as $inventory_map2) {
            $inventory_maps->push([
                                'inventory_id' => $id,
                                'brand' => $inventory_map2->brand,
                                'model' => $inventory_map2->model,
                                'serial' => 0,
                                'serial_branch' => 0,
                                'quantity' => $inventory_map2->quantity,
                                'quantity_branch' => $inventory_map2->quantity_branch
                            ]);
        }
        $inventory_maps = $inventory_maps->sortBy('brand');
        return view('inventories.branch.view', compact('inventory_maps'));
    }

    public function import_branch ($id) {
        $inventory = Inventory::find($id);
        return view('inventories.branch.import', compact('inventory'));
    }

    public function import_proceed ($id, Request $req) {
        $validator = Validator::make($req->all(), [
                'file' => 'required',
            ]);

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

        $csv_path = $req->file->getRealPath();

        try {

            // reset inventory
            $inventory_maps_reset  = InventoryMap::where('inventory_id', $id)->get();
            foreach ($inventory_maps_reset as $inventory_map_reset) {
                $inventory_map_reset->serial_branch = null;
                $inventory_map_reset->quantity_branch = null;
                $inventory_map_reset->update();
            }
            // delete inventory (branch side)
            InventoryMap::where('inventory_id', $id)->where('serial', null)->delete();

            // Insert the data from a file
            Excel::load($csv_path, function($reader) use ($id) {
                foreach ($reader->toArray() as $csv) {
                    $inventory_maps = InventoryMap::where('inventory_id', $id)
                                     ->where('brand', $csv['brand'])
                                     ->where('model', $csv['model'])
                                     ->get();

                    if (count($inventory_maps) > 0) {
                        foreach ($inventory_maps as $inventory_map) {
                            // check if serial and quantity is null
                            if (!$csv['quantity'] && $csv['serial']) {
                                if ($csv['serial'] == $inventory_map->serial) {
                                    $inventory_map->inventory_id = $id;
                                    $inventory_map->serial_branch = $csv['serial'];
                                    $inventory_map->update();
                                }
                            } elseif ($csv['quantity'] && !$csv['serial']) {
                                if ($inventory_map->quantity_branch === null) {
                                    $inventory_map->inventory_id = $id;
                                    $inventory_map->quantity_branch = $csv['quantity'];
                                    $inventory_map->update();
                                } else {
                                    $inventory_map->inventory_id = $id;
                                    $inventory_map->quantity_branch += $csv['quantity'];
                                    $inventory_map->update();
                                }
                            }
                        }

                        // check if serial
                        if ($csv['serial']) {
                            // check table where serial data was matched with the serial_branch
                            $inventory_map_checks = InventoryMap::where('inventory_id', $id)
                                                    ->where('quantity', null)
                                                    ->where('brand', $inventory_map->brand)
                                                    ->where('model', $inventory_map->model)
                                                    ->where('serial_branch', $csv['serial'])
                                                    ->get();
                            // check if doesn't have record
                            if (count($inventory_map_checks) <= 0) {
                                // then add new if no records found
                                $inventory_map_new = new InventoryMap;
                                $inventory_map_new->inventory_id = $id;
                                $inventory_map_new->brand = $csv['brand'];
                                $inventory_map_new->model = $csv['model'];
                                $inventory_map_new->price = $inventory_map->price;
                                $inventory_map_new->serial_branch = $csv['serial'];
                                $inventory_map_new->save();
                            }
                        } elseif (!$csv['serial'] && $csv['quantity']) { // v2.7
                            // check table where serial data was matched with the serial_branch
                            $inventory_map_checks = InventoryMap::where('inventory_id', $id)
                                                    ->where('quantity', null)
                                                    ->where('brand', $inventory_map->brand)
                                                    ->where('model', $inventory_map->model)
                                                    ->where('serial', $inventory_map->serial)
                                                    ->get();
                            // check if doesn't have record
                            if (count($inventory_map_checks) > 0) {
                                // then add new if no records found
                                $inventory_map_new = new InventoryMap;
                                $inventory_map_new->inventory_id = $id;
                                $inventory_map_new->brand = $csv['brand'];
                                $inventory_map_new->model = $csv['model'];
                                $inventory_map_new->price = $inventory_map->price;
                                $inventory_map_new->serial_branch = 'NEW';
                                $inventory_map_new->save();
                            }
                        }
                    } else {
                        // if no matching brand and model
                        $inventory_map_new = new InventoryMap;
                        $inventory_map_new->inventory_id = $id;
                        $inventory_map_new->brand = $csv['brand'];
                        $inventory_map_new->model = $csv['model'];
                        $inventory_map_new->serial_branch = $csv['serial'];
                        $inventory_map_new->quantity_branch = $csv['quantity'];
                        $inventory_map_new->save();
                    }
                }
            });

            $flash_message = [
                'title' => 'Well done!',
                'status' => 'success',
                'message' => 'The file was successfully imported.',
            ];
            Session::flash('create_success', $flash_message);
            return redirect()->route('inventories');
        } catch (\Exception $e) {
            $flash_message = [
                'title' => 'Oops!',
                'status' => 'danger',
                'message' => $e->getMessage() . '. Please upload a correct file.',
            ];
            Session::flash('update_fail', $flash_message);
            return redirect()->back();
        }
    }

    public function create_branch ($id) {
        $inventory_map = InventoryMap::where('inventory_id', $id)->first();
        return view('inventories.branch.create', compact('inventory_map'));
    }

    public function duplicate_branch ($id) {
        $inventory_map = InventoryMap::find($id);
        return view('inventories.branch.duplicate', compact('inventory_map'));
    }

    public function edit_branch ($id, Request $req) {
        $inventory_maps = InventoryMap::orderBy('model', 'asc')
                        ->orderBy('brand', 'asc')
                        ->where('inventory_id', $id)
                        ->where('quantity', null)
                        ->get();
        $inventory_maps2 = DB::table('inventory_maps')
                     ->where('inventory_id', $id)
                     ->where('quantity', 1)
                     ->select('brand', 'model', 'quantity_branch', DB::raw('count(model) as quantity'))
                     ->groupBy('model', 'brand', 'quantity_branch')
                     ->get();

        foreach ($inventory_maps2 as $inventory_map2) {
            $inventory_maps->push([
                                'inventory_id' => $id,
                                'brand' => $inventory_map2->brand,
                                'model' => $inventory_map2->model,
                                'serial' => 0,
                                'serial_branch' => 0,
                                'quantity' => $inventory_map2->quantity,
                                'quantity_branch' => $inventory_map2->quantity_branch
                            ]);
        }
        $inventory_maps = $inventory_maps->sortBy('brand');
        return view('inventories.branch.edit', compact('inventory_maps'));
    }

    public function store_branch ($id, Request $req) {
        $rules = [
            'brand' => 'required',
            'model' => 'required',
            'serial_branch' => 'required|unique:inventory_maps,serial,'.$id.'|unique:inventory_maps,serial_branch,'.$id,
        ];

        $messages = [
            'serial_branch.unique' => 'This Serial is already in the database.',
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

        $inventory_map = new InventoryMap;
        $inventory_map->inventory_id = $id;
        $inventory_map->brand = $req->brand;
        $inventory_map->model = $req->model;
        $inventory_map->serial_branch = $req->serial_branch;
        $inventory_map->save();

        $flash_message = [
            'title' => 'Well done!',
            'status' => 'success',
            'message' => 'New column was successfully added.',
        ];
        Session::flash('create_success', $flash_message);
        return redirect()->back();
    }

    public function update_branch ($id, Request $req) {
        $inventory_map = InventoryMap::find($id);
        if ($req->btn == 'owned') {
            $inventory_map->serial_branch = $inventory_map->serial;
        } else {
            $inventory_map->serial_branch = null;
        }
        $inventory_map->update();
        // return response()->json('yay!');
    }

    public function update_branch_qty ($id) {
        $qty = $_POST['qty'];
        $model = $_POST['model'];
        $inventory_maps = InventoryMap::where('inventory_id', $id)
                         ->where('model', $model)
                         ->get();
        foreach ($inventory_maps as $inventory_map) {
            $inventory_map->quantity_branch = $qty;
            $inventory_map->update();
        }
    }

    public function trash_branch ($id) {
        $inventory_map = InventoryMap::find($id);
        return view('inventories.branch.trash', compact('inventory_map'));
    }

    public function delete_branch ($id) {
        $inventory_map = InventoryMap::find($id);
        $inventory_id = $inventory_map->inventory_id;

        $flash_message = [
            'title' => 'Well done!',
            'status' => 'success',
            'message' => 'Item with serial ' . $inventory_map->serial_branch . ' was successfully deleted.',
        ];
        $inventory_map->delete();

        Session::flash('delete_success', $flash_message);
        return redirect()->route('inventory.edit_branch', ['id' => $inventory_id]);
    }
}
