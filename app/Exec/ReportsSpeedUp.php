<?php
namespace App\Exec;
use DB;
class ReportsSpeedUp {
    public static function creditportal() {
        return new ReportsSpeedUpNamespace\CreditReports();
    }
    public static function blacklisted() {
        return new ReportsSpeedUpNamespace\BlackListedReports();
    }
}
?>