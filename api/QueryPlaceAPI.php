<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once '../Credentials.php';





//For loop that will call the Places API for each field type above and call the create place object function.
foreach ($fieldTypes as $key => $value) {
    foreach ($value as $category => $items) {
        // $category is Food/Sport/AfterDark etc
        foreach ($items as $inner_key => $item) {
            array_push($fieldTypesForAPI, $item);
            //echo  "<br>here<br>" .$item;
        }
    }
}


//print_r($fieldTypesForAPI);



for($i = 0; $i < count($fieldTypesForAPI); $i++){
    
        //echo "</br>Requests</br>";
        //echo $URL = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=". $_SESSION["URL_LATITUDE"] . "," . $_SESSION["URL_LONGITUDE"] . "&rankby=distance&type=" . $fieldTypesForAPI[$i]  . "&key=" . $_SESSION["PLACES_API_KEY"];
        //echo "</br>";
        //echo $URL  = "https://maps.googleapis.com/maps/api/place/textsearch/json?query=".$fieldTypesForAPI[$i] . "+in+Dundalk&radius=1000&key=AIzaSyBWybtApBsH98-1jOht7uh82w_2pJW1vKw";
        
        $APIresult = file_get_contents($URL);

        parseAPIResult($placesArray,$APIresult, $db);
        
        //echo "<pre>";  print_r($APIresult); echo "</pre>";
        
}



function parseAPIResult(&$placesArray, &$api_result, &$db){
    

    
        if($api_result !== false){
        $json_data = json_decode($api_result, true);
         
     
          
      
        for($i = 0; $i < 7; $i++){     

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
            
//            if (!empty($json_data['results'][$i]["opening_hours"]))
//            {
//                
//                if(!empty($json_data['results'][$i]["opening_hours"]["open_now"])){
//                    $open = "Open";
//                }
//                else{
//                    $open = "Closed";
//                }
//            }  
//            else{
//                $open = "No Hours Provided";
//            }
            
            $average_time = 20;
            
            if($place_id == "null"){
               //skip place 
            }
            else{
                
                $Place_Object = new Place($place_id, $place_name,$place_type, $rating, $latitude, $longitude,  $icon, $open, $cover_image,$average_time);
                $Place_Object->setCoverImage($cover_image);
                $Place_Object->setAverageTime($average_time);
                array_push($placesArray,$Place_Object);
                
                //print_r($placesArray);
                //echo "<br>insertin to DB";

//                $query = "INSERT INTO places  SET  place_id=:place_id, place_name=:place_name, place_type:place_type, place_rating:place_rating, place_lat:place_lat, place_lng:place_lng, place_icon:place_icon, place_open:place_open, place_cover_image:place_cover_image, place_average_time:place_average_time";
//                $query = "INSERT INTO places (place_id,place_name, place_type, place_rating, place_lat, place_lng, place_icon, place_open, place_cover_image, place_average_time) "
//                        . "VALUES (?,?,?,?,?,?,?,?,?,?)";


            }
            
        }

            
        
    }
    
}


