<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Company;
use App\Announcement;

use DB;
use Session;
use Validator;

class AnnouncementController extends Controller
{

    public function __construct () {
      $this->middleware(['auth', 'announcement_clearance']);

      // for active routing state
      \View::share('is_announcement_route', true);
    }

    public function index () {
      $announcements = Announcement::select('id', 'company_id', 'created_by', 'title', 'body')
              ->with(['company' => function ($qry) {
                $qry->select('id', 'name');
              }])
              ->with(['created_by' => function ($qry) {
                $qry->select('id', DB::raw('CONCAT(first_name, " ", last_name) AS name'));
              }])
              ->get();
      return view('announcements.index', compact('announcements'));
    }

    public function view () {
      $announcements = Announcement::select('id', 'company_id', 'created_by', 'title', 'body')
              ->with(['company' => function ($qry) {
                $qry->select('id', 'name');
              }])
              ->with(['created_by' => function ($qry) {
                $qry->select('id', DB::raw('CONCAT(first_name, " ", last_name) AS name'));
              }])
              ->where('company_id', \Auth::user()->company->id)
              ->get();
      return view('announcements.view', compact('announcements'));
    }

    public function create () {
      $companies = Company::select('id', 'name')->get();
      return view('announcements.create', compact('companies'));
    }

    public function store (Request $req) {
      $validator = Validator::make($req->all(), [
        'title' => 'required',
        'body' => 'required',
      ]);

      if ($validator->fails()) {
        $flash_message = [
          'title' => 'Oops!',
          'status' => 'danger',
          'message' => 'Please correct all the errors below.',
        ];
        Session::flash('create_fail', $flash_message);
        return redirect()->back()->withInput()->withErrors($validator);
      }

      $announcement = new Announcement;
      $announcement->created_by = \Auth::user()->id;
      $announcement->company_id = $req->company;
      $announcement->title = $req->title;
      $announcement->body = $req->body;
      $announcement->save();

      $flash_message = [
        'title' => 'Well Done!!!',
        'status' => 'success',
        'message' => 'New record has been successfully added into our records.',
      ];
      Session::flash('create_success', $flash_message);
      if ($req->savebtn == 0) {
        return redirect()->route('announcement.create');
      } else { return redirect()->route('announcements.index'); }
    }

    public function edit ($id) {
      $companies = Company::select('id', 'name')->get();
      $announcement = Announcement::select('id', 'company_id', 'title', 'body')
                      ->where('id', $id)
                      ->where('created_by', \Auth::user()->id)
                      ->firstOrFail();
      return view('announcements.edit', compact('announcement', 'companies'));
    }

    public function update ($id, Request $req) {
      $validator = Validator::make($req->all(), [
        'title' => 'required',
        'body' => 'required',
      ]);

      if ($validator->fails()) {
        $flash_message = [
          'title' => 'Oops!',
          'status' => 'danger',
          'message' => 'Please correct all the errors below.',
        ];
        Session::flash('update_fail', $flash_message);
        return redirect()->back()->withInput()->withErrors($validator);
      }

      $announcement = Announcement::where('id', $id)
                      ->where('created_by', \Auth::user()->id)
                      ->firstOrFail();
      $announcement->company_id = $req->company;
      $announcement->title = $req->title;
      $announcement->body = $req->body;
      $announcement->update();

      $flash_message = [
        'title' => 'Well Done!!!',
        'status' => 'success',
        'message' => 'One (1) record has been successfully updated.',
      ];
      Session::flash('update_success', $flash_message);
      return redirect()->route('announcements.index');
    }

    public function trash ($id) {
      $announcement = Announcement::select('id', 'company_id', 'title', 'body')
                      ->with(['company' => function ($qry) {
                        $qry->select('id', 'name');
                      }])
                      ->where('id', $id)
                      ->where('created_by', \Auth::user()->id)
                      ->firstOrFail();
      return view('announcements.trash', compact('announcement'));
    }

    public function delete ($id) {
      $announcement = Announcement::where('id', $id)
                      ->where('created_by', \Auth::user()->id)
                      ->firstOrFail();
      $announcement->delete();

      $flash_message = [
        'title' => 'Well Done!!!',
        'status' => 'success',
        'message' => 'One (1) record has been successfully deleted from our records.',
      ];
      Session::flash('delete_success', $flash_message);

      return redirect()->route('announcements.index');
    }
}