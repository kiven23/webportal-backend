<?php
namespace App\Reports;
class EmployeeList extends \koolreport\KoolReport
{
    use \koolreport\laravel\Friendship;
    // use \koolreport\bootstrap3\Theme;
    // By adding above statement, you have claim the friendship between two frameworks
    // As a result, this report will be able to accessed all databases of Laravel
    // There are no need to define the settings() function anymore
    // while you can do so if you have other datasources rather than those
    // defined in Laravel.
    function setup () {
        // Let say, you have "sale_database" is defined in Laravel's database settings.
        // Now you can use that database without any futher setitngs.
        
        $this->src("mysql") // use any of your preferred connection type in config/database.php
        ->query("
            SELECT
              CONCAT(u.first_name,' ',u.last_name) AS name,
              u.email,
              b.name AS branch,
              d.name AS department,
              p.name AS position
            FROM users as u
            INNER JOIN user_employments as ue on u.id=ue.user_id
            INNER JOIN branches as b on ue.branch_id=b.id
            INNER JOIN departments as d on ue.department_id=d.id
            INNER JOIN positions as p on ue.position_id=p.id
            ORDER BY branch, department, position, name
        ")
        ->pipe($this->dataStore("users")); 
    }
}