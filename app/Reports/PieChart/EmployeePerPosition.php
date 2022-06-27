<?php
namespace App\Reports\PieChart;
class EmployeePerPosition extends \koolreport\KoolReport
{
    use \koolreport\laravel\Friendship;
    use \koolreport\bootstrap3\Theme;

    function setup () {
        // Let say, you have "sale_database" is defined in Laravel's database settings.
        // Now you can use that database without any futher setitngs.
        
        $this->src("mysql") // use any of your preferred connection type in config/database.php
        ->query("
            SELECT
              p.name AS position,
              COUNT(ue.position_id) AS position_count
            FROM users as u
            INNER JOIN user_employments as ue on u.id=ue.user_id
            INNER JOIN positions as p on ue.position_id=p.id
            GROUP BY ue.position_id
            ORDER BY position_count DESC
            LIMIT 5
        ")
        ->pipe($this->dataStore("employee_per_position")); 
    }
}