<?php
use \koolreport\widgets\google\PieChart;

$category_amount = array(
  array("category"=>"Books","sale"=>32000,"cost"=>20000,"profit"=>12000),
  array("category"=>"Accessories","sale"=>43000,"cost"=>36000,"profit"=>7000),
  array("category"=>"Phones","sale"=>54000,"cost"=>39000,"profit"=>15000),
  array("category"=>"Movies","sale"=>23000,"cost"=>18000,"profit"=>5000),
  array("category"=>"Others","sale"=>12000,"cost"=>6000,"profit"=>6000),
);
?>
<div class="report-content">
  <div class="text-center">
    <h1>Employee Per Position</h1>
    <p class="lead">Top 5 Employee Per Position</p>
  </div>
  <?php
    PieChart::create(array(
      "title"=>"Employee Per Position",
      "dataSource"=>$this->dataStore('employee_per_position'),
      // "dataSource"=>$category_amount,
      "columns"=>array(
        "position",
        "position_count"=>array(
          "type"=>"number",
        )
      )
    ));
  ?>
</div>