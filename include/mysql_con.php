 <?php
 function Open_Connection($con){

switch ($con) {
	case 'loacal_host':
		$servername = "localhost";
		$username = "root";
		$password = "";
		$dbname = "jtabletestdb";

	break;
	
	default:
		$servername = "localhost";
		$username = "root";
		$password = "";

	break;
}

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully";
    }
catch(PDOException $e)
    {
    echo "Connection failed: " . $e->getMessage();
    }
    return $conn;
}
?> 