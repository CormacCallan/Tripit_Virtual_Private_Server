<?php
include_once 'database.php';

$database = new Database();
$db = $database->getConnection();

//$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // select all query
        $sql = "insert into places (place_id) values (1234) ";

    
   
         $stmt = $db->prepare($sql);
    

  

        // execute query
        $stmt->execute();
?>
