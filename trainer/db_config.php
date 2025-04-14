<?php
$sname="localhost:3306";
$username="root";
$password="";
$db_name="miniproject_db";
$conn=mysqli_connect($sname, $username, $password, $db_name);

if(!$conn){
    echo "Connection Failed!";
}