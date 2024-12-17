<?php

$server = "sunshine.g00r.com.au";
$username = "jayz";
$password = "nyJ*BE%9r%T#U";
$databaseName = "Sunshine";

$conn = mysqli_connect($server,$username,$password,$databaseName);

if(!$conn){
    echo "Error connecting to database";
}
return $conn;

?>