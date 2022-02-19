<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

function getConexion()
{
    $servername = "localhost:3308";
    $username = "root";
    $password = "";
    $dbname = "premoraagencias";
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    } else {
        return $conn;
    }
}

function executeQuery($strQuery)
{
    if( $strQuery!='' ){
        $conn = getConexion();
        $result = mysqli_query($conn, $strQuery);
        return $result;
    }else{
        return false;
    }
}
