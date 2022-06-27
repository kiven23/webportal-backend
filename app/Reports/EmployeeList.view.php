<?php
use \koolreport\widgets\koolphp\Table;
?>
<div class="report-content">
  <div class="text-center">
    <h1>Employee Lists</h1>
    <p class="lead">List of employees from database</p>
  </div>
  <?php
  Table::create(array(
      "dataSource"=>$this->dataStore('users'),
      "headers"=>array(
        array(
          "Employee Information"=>array("colSpan"=>4),
          "User Information"=>array("colSpan"=>1),
        )
      ),
      "columns"=>array(
        "branch"=>array(
          "label"=>"Branch"
        ),
        "department"=>array(
          "label"=>"Department"
        ),
        "position"=>array(
          "label"=>"Position"
        ),
        "name"=>array(
          "label"=>"Employee"
        ),
        "email"=>array(
          "label"=>"E-mail",
          "formatValue"=>function($value,$row) {
            return "<span style='color:blue'>".$value."</span>";
          }  
        ),
      ),
      "paging"=>array(
        "pageSize"=>10,
        "pageIndex"=>0,
      ),
      "cssClass"=>array(
          "table"=>"table-bordered table-condensed"
      ),
      "removeDuplicate"=>array("branch", "department", "position"),
  ));
  ?>
</div>