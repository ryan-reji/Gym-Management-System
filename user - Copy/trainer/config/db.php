<?php
$sname="localhost:3306";
$username="root";
$password="";
$db_name="miniproject_db";
$conn=mysqli_connect($sname, $username, $password, $db_name);
ini_set('session.gc_maxlifetime', 86400); // 1 day session lifetime
session_set_cookie_params(86400); 
session_start();

if(!$conn){
    echo "Connection Failed!";
}