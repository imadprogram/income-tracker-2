<?php


$hostname = "localhost";
$username = "root";
$password = "";
$dbname = "smart_wallet_2";

try{
    $connect = mysqli_connect($hostname , $username, $password, $dbname);
}catch(mysqli_sql_exception){
    echo "failed connecting !!!";
}

?>