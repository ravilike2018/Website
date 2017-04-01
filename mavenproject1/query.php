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
$SQL = "SELECT ".$SELECT." FROM `landmarks`".$WHERE;


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

$jsonData =json_encode( databaseQwery($SQL) );
$original_data = json_decode($jsonData, true);
$features = array();
foreach($original_data as $key => $value) {
    $features[] = array(
        'type' => 'Feature',
        'properties' => array('ID' => $value['ID'],'NameOnTheRegister' => $value['NameOnTheRegister'],'Date_listed' => $value['Date_listed'],'City' => $value['City'],'Type' => $value['Type'],'Free' => $value['Free'],'AlwaysOpen' => $value['AlwaysOpen']),
        'geometry' => array(
             'type' => 'Point', 
             'coordinates' => array(
                  $value['Latitude'], 
                  $value['Longitude'], 
                  1
             ),
         ),
    );
}
$new_data = array(
    'type' => 'FeatureCollection',
    'features' => $features,
);

$final_data = json_encode($new_data, JSON_PRETTY_PRINT);
print_r($final_data);


?>