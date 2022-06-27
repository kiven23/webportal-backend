<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

use App\File;
use App\AccessChartUserMap AS AccessUser;

use DB;
use Mail;

trait EmailTrait {

	public function po_file_send_to_email ($file_id) {
    $file = File::with(['from_user' => function ($qry) {
              $qry->select('id', 'extn_email1', \DB::raw('CONCAT(first_name," ",last_name) AS name'));
            }])
            ->with(['to_user' => function ($qry) {
              $qry->select('id', \DB::raw('CONCAT(first_name," ",last_name) AS name'));
            }])
            ->with(['to_company' => function ($qry) {
              $qry->select('id', 'name');
            }])
            ->where('id', $file_id)
            ->where('customer_id', null)
            ->first();

    $sender = $file->from_user->name;
    $sender_email = $file->from_user->extn_email1 ? $file->from_user->extn_email1 : 'marylouursante@addessa.com';
    if ($file->to) {
      $to = $file->to_user->first_name . ' ' . $file->to_user->last_name;
    } else {
      $to = $file->to_company->name;
    }

    if ($file->to) {
      $email_addresses = \DB::table('file_settings')
                        ->select('user_id',
                                  \DB::raw('(SELECT CONCAT_WS(",",extn_email1,extn_email2,extn_email3) FROM users WHERE id=user_id) AS emails'))
                        ->where('email_notif', 1)
                        ->where('user_id', $file->to)
                        ->first();
      $email_addresses = $email_addresses ? explode(",",$email_addresses->emails) : null;
    } else {
      $addresses = \DB::table('file_settings AS fs')
                        ->select('u.extn_email1',
                                  'u.extn_email2',
                                  'u.extn_email3')
                        ->join('users AS u', 'u.id', '=', 'fs.user_id')
                        ->join('companies AS c', 'c.id', '=', 'u.company_id')
                        ->where('fs.email_notif', 1)
                        ->where('c.id', $file->company_id)
                        ->get();
      $email_addresses = [];
      foreach ($addresses as $address) {
        array_push($email_addresses, $address->extn_email1, $address->extn_email2, $address->extn_email3);
      }
      // filter for null and get only unique values then flatten into array again
      $email_addresses = collect(array_unique(array_filter($email_addresses)))->flatten()->all();
    }

    if (!empty($email_addresses)) {
      $data = array(
        'sender' => 'ADDESSA SUPPLIER PORTAL',
        'subject' => $file->remarks,
        'file' => $file->file,
        'to' => $to,
        'from' => $sender,
        'from_email' => $sender_email,
      );

      Mail::send('purchase_orders.files.emails.send', $data, function ($message) use ($data, $email_addresses) {
        $message->from($data['from_email'], $data['sender']);
        $message->to($email_addresses)->subject($data['subject']);
      });

      if (Mail::failures()) {
        return "Fail in sending email";
      }
    }
	}

}

?>