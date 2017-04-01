<?php
$intID = $_GET['intID'];

$SQL = "SELECT * FROM `photos` WHERE `intID` = ".$intID;

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
$responces = databaseQwery($SQL);
echo "<style>
		table {
			margin:0 auto;
			border-style:solid;
			border-width:1px;
			border-color:#eee;
			border-collapse:collapse;
			width:100%;
		}

		table tr:nth-child(even) {
			background-color:#eee;
		}
	
	</style><table><tbody>";
foreach($responces as $responce){
	echo "<tr>";
	echo "<td><img src='data:image/png;base64, ".$responce['Photo']."' style='width:60vw;'/></td>";
	echo "<td>".$responce['Description']."</td>";
	echo "</tr>";
}
echo "</tbody></table>";
?>