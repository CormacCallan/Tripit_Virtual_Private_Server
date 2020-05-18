<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

include_once 'database.php';
include_once '../Credentials.php';
include_once '../Place.php';
include_once '../Categories.php';
include_once '../Blacklist.php';

$database = new Database();
$db = $database->getConnection();



$lat = "53.997945";

$lng = "-6.4059567";

$pref1 = "Food";

$pref2 = "History";

$pref3 = "Entertainment";

$time = 240;


//Google Places API
$_SESSION["PLACES_API_KEY"]= "AIzaSyBWybtApBsH98-1jOht7uh82w_2pJW1vKw";




$type = "cafe";


    
        
        $URL = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=". $lat . "," . $lng . "&type=" .  $type  . "&rankby=distance" . "&key=" . $_SESSION["PLACES_API_KEY"];
        
        $APIresult = file_get_contents($URL);

        parseAPIResult($APIresult);

        echo "<pre>";  print_r($APIresult); echo "</pre>";



$dbcount = 0;
function parseAPIResult(&$api_result){
    
    
        if($api_result !== false){
        $json_data = json_decode($api_result, true);

     
          
      
        for($i = 0; $i < 6; $i++){     

            //Validating JSON Object to ensure all fields are present.
            
            
            if (!empty($json_data['results'][$i]["place_id"]))
            {
                $place_id = $json_data['results'][$i]["place_id"];
            }  
            else{
                $place_id = "null";
            }
            
            if (!empty($json_data['results'][$i]["name"]))
            {
                $place_name = $json_data['results'][$i]["name"];
            }  
            else{
                $place_name = "No Place Name Found";
            }
            
            
            if (!empty($json_data['results'][$i]["types"][0]))
            {
                $place_type = $json_data['results'][$i]["types"][0];
            }  
            else{
                $place_type = "No Types Found";
            }
            
            
                        
            if (!empty($json_data['results'][$i]["geometry"]["location"]["lat"]))
            {
                $latitude = $json_data['results'][$i]["geometry"]["location"]["lat"];
            }  
            else{
                $latitude = "No Lat Found";
            }
            
            if (!empty($json_data['results'][$i]["geometry"]["location"]["lng"]))
            {
                 $longitude = $json_data['results'][$i]["geometry"]["location"]["lng"];
            }  
            else{
                $longitude = "No Lng Found";
            }

            
            if (!empty($json_data['results'][$i]["rating"]))
            {

                $rating = $json_data['results'][$i]["rating"];

            }  
            else{
                $rating = "No Rating";
            }
            

            if (!empty($json_data['results'][$i]["icon"]))
            {
                $icon = $json_data['results'][$i]["icon"];
            }
            else{
                $icon =  "No Icon Provided";
            }
            
            if (!empty($json_data['results'][$i]["photos"]))
            {
                
                if(!empty($json_data['results'][$i]["photos"][0]["photo_reference"])){
                    $cover_image = $json_data['results'][$i]["photos"][0]["photo_reference"];
                }
                else{
                    $cover_image =  "Photos = True but no Reference";
                }
            }
            else{
                $cover_image =  "No Photos Provided";
            }
            
            $open = "Open";
            
            if (!empty($json_data['results'][$i]["opening_hours"]))
            {
                
                if(!empty($json_data['results'][$i]["opening_hours"]["open_now"])){
                    $open = "Open";
                }
                else{
                    $open = "Closed";
                }
            }  
            else{
                $open = "No Hours Provided";
            }
            
            $average_time = 20;
            
            if($place_id == "null"){
               //skip place 
            }
            else {
                echo "<br>creating place object";
                $Place_Object = new Place($place_id, $place_name, $place_type, $rating, $latitude, $longitude, $icon, $open, $cover_image, $average_time);
                $Place_Object->setCoverImage($cover_image);
                $Place_Object->setAverageTime($average_time);
                array_push($placesArray, $Place_Object);
                
                print_r($placesArray);
                echo "<br>insertin to DB";
                // query to insert record
                //$query = "INSERT INTO activities  SET  place_id=:place_id, place_name=:place_name, place_type:place_type, place_rating:place_rating, place_lat:place_lat, place_lng:place_lng, place_icon:place_icon, place_open:place_open, place_cover_image:place_cover_image, place_average_time:place_average_time";
//                $query = "INSERT INTO activities (place_id,place_name, place_type, place_rating, place_lat, place_lng, place_icon, place_open, place_cover_image, place_average_time) "
//                        . "VALUES (?,?,?,?,?,?,?,?,?,?)";
//
//                                $query = "INSERT INTO activities (place_id) "
//                        . "VALUES (?)";
//                // prepare query
//                $stmt = $pdo->prepare($query);
//                $stmt->execute([$place_id]);
//               echo "<br>completed";
                
                        $sql = "insert into places (place_id) values (1234) ";

    
   
         $stmt = $db->prepare($sql);
    

  

        // execute query
        $stmt->execute();
                
            }
            
        }

    }
    
}





?>