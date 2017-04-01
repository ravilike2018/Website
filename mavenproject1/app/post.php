<?php

$json = file_get_contents('php://input');
$obj = json_decode($json, true);
print_r($obj);

$SQL = "INSERT INTO photos (Photo,Description,intID) VALUES ('".$obj['Photo']."','".$obj['Description']."',".$obj['IntID'].");";
databaseQwery($SQL);

function databaseQwery($sql){
	$username="vincentc_code";
	$password="Fl00rJava";
	$database="vincentc_codefest";
    //open connection to mysql db
    $connection = mysqli_connect("localhost",$username,$password,$database) or die("Error " . mysqli_error($connection));
    //fetch table rows from mysql db
    $result = mysqli_query($connection, $sql) or die("Error in: ".$sql." : ". mysqli_error($connection));
    //create an array
    $emparray = array();
    while($row =mysqli_fetch_assoc($result))
    {
        $emparray[] = $row;
    }
    //close the db connection
    mysqli_close($connection);
	return $emparray;
}
?>