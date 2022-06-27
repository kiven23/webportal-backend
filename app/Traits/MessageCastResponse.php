<?php

namespace App\Traits;

use App\Setting;
use GuzzleHttp\Client;

trait MessageCastResponse {
	
	public static function response ($response) {
		$code = substr($response,0,5);
		// -----------------
		// GENERAL RESPONSES
		// -----------------
		// 20110,Invalid Credentials. Username / Password/ Session ID is invalid
		// 20120,Company is Inactive. Username / Password/ Session ID is valid but the client account is set as inactive
		// 20121,Account is Deactivated. Username / Password/ Session ID is valid but testing period has expired
		// 20122,Invalid IP Address, ip address. Client IP address used to access the MCPro Gateway is not permitted to access the Gateway
		// 20123,User is Inactive. Username / Password/ Session ID is valid but client user account is invalid
		// 20500,Error encountered. A general error has been encountered

		if ($code == 20110) {
			return ['status' => 'danger', 'message' => 'Invalid Credentials'];
		} elseif ($code == 20120) {
			return ['status' => 'danger', 'message' => 'Company is Inactive'];
		} elseif ($code == 20120) {
			return ['status' => 'danger', 'message' => 'Account is Deactivated'];
		} elseif ($code == 20121) {
			return ['status' => 'danger', 'message' => 'Invalid IP Address'];
		} elseif ($code == 20122) {
			return ['status' => 'danger', 'message' => 'User is Inactive'];
		} elseif ($code == 20123) {
			return ['status' => 'danger', 'message' => 'Error encountered'];
		}

		// -----------------------------------
		// COMMON TRANSACTION STATUS RESPONSES
		// -----------------------------------
		// 20300,Queued, transid, timestamp. Message has been received by MCPro for processing
		// 	 transid is a globally unique identifier
		// 	 timestamp is the date/time when the message was received by the MCPro system
		// 20310,Sending,transid,msgid,msisdn,timestamp. Message has been forwarded to the MCPro Messaging Server
		// 20320,Delivered, transid,msgid,msisdn,timestamp. Message has been sent
		// 20340,Not Delivered, transid,msgid,msisdn,timestamp. Message sending failed
		// 20214,Invalid Transaction ID. Transaction ID provided is invalid
		// 20215,Invalid Date Format/Range. Date range provided is invalid and date format is incorrect
		// 20217,Invalid Message ID. Message ID provided is invalid
		// 20218,No Data. There is no data to be returned

		elseif ($code == 20300) {
			return ['status' => 'success', 'message' => 'Message has been received by MCPro for processing'];
		} elseif ($code == 20310) {
			return ['status' => 'success', 'message' => 'Message has been forwarded to the MCPro Messaging Server'];
		} elseif ($code == 20320) {
			return ['status' => 'success', 'message' => 'Message has been sent'];
		} elseif ($code == 20340) {
			return ['status' => 'danger', 'message' => 'Message sending failed'];
		} elseif ($code == 20214) {
			return ['status' => 'danger', 'message' => 'Transaction ID provided is invalid'];
		} elseif ($code == 20215) {
			return ['status' => 'danger', 'message' => 'Date range provided is invalid and date format is incorrect'];
		} elseif ($code == 20217) {
			return ['status' => 'danger', 'message' => 'Message ID provided is invalid'];
		} elseif ($code == 20218) {
			return ['status' => 'danger', 'message' => 'There is no data to be returned'];
		}

	}

}