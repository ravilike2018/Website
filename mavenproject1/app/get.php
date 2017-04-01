<?php
if($_GET['sqlSELECT']){
	$SELECT = $_GET['sqlSELECT'];
}else{
	$SELECT = "*";
}

if($_GET['sqlWHERE']){
	$WHERE = " WHERE ".$_GET['sqlWHERE'];
}else{
	$WHERE = "";
}

$SQL = "SELECT ".$SELECT." FROM `photos`".$WHERE;

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


print_r( json_encode(databaseQwery($SQL)) );

?>