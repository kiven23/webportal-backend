<?php
    $mysqli = new mysqli("127.0.0.1","root","M15@2dwin0n7y","webportal_8");
    if ($mysqli -> connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
    exit();
    }
    $myArray = [];
    $sql = "SELECT * FROM custom_db";
    $result = $mysqli -> query($sql);
    while($row = $result->fetch_array()) {
        $database_list[] = $row;
    }
    $mysqli -> close();
