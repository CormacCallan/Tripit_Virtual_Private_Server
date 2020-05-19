<?php
session_start();
//HTTP Request Variables
$_SESSION["URL_LATITUDE"] = $_GET["lat"];
$_SESSION["URL_LONGITUDE"] = $_GET["lng"];
$_SESSION["URL_PREFERENCE_1"] = $_GET['pref1'];
$_SESSION["URL_PREFERENCE_2"] = $_GET['pref2'];
$_SESSION["URL_PREFERENCE_3"] = $_GET['pref3'];
$_SESSION["URL_USER_TIME"] = $_GET['time'];


include_once '../Credentials.php';
include_once '../Place.php';
include_once '../Categories.php';
include_once '../Blacklist.php';
include_once './GetTime.php';
include_once 'database.php';
//utilities
include_once '../../resources/utilities/utility.php';
include_once '../v2/v2_handler.php';



$database = new Database();
$db = $database->getConnection();

try {
    if ($_SESSION["URL_USER_TIME"] == 69) {
        automateHTTPRequest();
    } else {
        if (empty($_SESSION["URL_PREFERENCE_1"]) || empty($_SESSION["URL_PREFERENCE_2"]) || empty($_SESSION["URL_PREFERENCE_3"])) {
            populatePreferences();
        } 
    }
} catch (Exception $ex) {
    echo 'TRIPIT_DEV: Failed to parse URL\nCaught exception: ', $e->getMessage(), "\n";
}

$p1 = $_SESSION["URL_PREFERENCE_1"];
$p2 = $_SESSION["URL_PREFERENCE_2"];
$p3 = $_SESSION["URL_PREFERENCE_3"];

$preferences = array($p1, $p2, $p3);



//Putting all field types into a single array to make API call easier.
$fieldTypesForAPI = array();

//An array that will contain Places API field types that match the preferences above.
$fieldTypes = array();
//Populating the Array
assignCategory($p1, $p2, $p3, $fieldTypes);





//Array with 
$placesArray = array();
//include '../QueryPlaceAPI.php';


//echo "</br>User TIME = " . $_SESSION['URL_USER_TIME'] . "</br>";



//insert($placesArray, $db);





$PlaceObjectArray = array();
$arrayToClient = array();
$finalArray = array();

function insert(&$placesArray, &$db){

    for ($i = 0; $i < count($placesArray) ; $i ++) {
        $place_id = $placesArray[$i]->place_id;
        $place_name = $placesArray[$i]->place_name;
        $place_type = $placesArray[$i]->place_type;
        $place_rating = $placesArray[$i]->rating;
        $place_lat = $placesArray[$i]->latitude;
        $place_lng = $placesArray[$i]->longitude;
        $place_icon = $placesArray[$i]->icon;
        $place_open = $placesArray[$i]->open;
        $place_cover_image = $placesArray[$i]->cover_image;
        $place_average_time = $placesArray[$i]->average_time;
                

        $stmtCheck = $db->prepare('SELECT * FROM places WHERE place_id=?');
        $stmtCheck->bindParam(1, $place_id, PDO::PARAM_STR);
        $stmtCheck->fetch(PDO::FETCH_ASSOC);
        $stmtCheck->execute();
        
        if($stmtCheck->rowCount() < 1){
            $query = "INSERT INTO places (place_id,place_name,place_type,place_rating,place_lat,place_lng,place_icon,place_open,place_cover_image,place_average_time) "
                    . "VALUES ('$place_id','$place_name','$place_type','$place_rating','$place_lat','$place_lng','$place_icon','$place_open','$place_cover_image','$place_average_time')";
            
            $stmtInsert = $db->prepare($query);
            $stmtInsert->bindParam(":place_id", $place_id);
            $stmtInsert->bindParam(":place_name", $place_name);
            $stmtInsert->bindParam(":place_type", $place_type);
            $stmtInsert->bindParam(":place_rating", $place_rating);
            $stmtInsert->bindParam(":place_lat", $place_lat);
            $stmtInsert->bindParam(":place_lng", $place_lng);
            $stmtInsert->bindParam(":place_icon", $place_icon);
            $stmtInsert->bindParam(":place_open", $place_open);
            $stmtInsert->bindParam(":place_cover_image", $place_cover_image);
            $stmtInsert->bindParam(":place_average_time", $place_average_time);

            $stmtInsert->execute(); 
        }
        else{
            echo "<br>Duplicate Found - Insert Skipped.";
        }
       
        
    }
    
}


function extractFromDatabase(&$db, &$PlaceObjectArray, &$preferences){
    
    foreach($preferences as $i){
 
        $stmtCheck = $db->prepare('SELECT * FROM places WHERE place_type=?');
        $stmtCheck->bindParam(1, $i, PDO::PARAM_STR);
        $stmtCheck->fetch(PDO::FETCH_ASSOC);
        $stmtCheck->execute();
        
        while ($row = $stmtCheck->fetch()) {
            $place_id = $row['place_id'];
            $place_name = $row['place_name'];
            $place_type = $row['place_type'];
            $place_rating = $row['place_rating'];
            $place_lat = $row['place_lat'];
            $place_lng = $row['place_lng'];
            $place_icon = $row['place_icon'];
            $place_open = $row['place_open'];
            $place_cover_image = $row['place_cover_image'];
            $place_average_time = $row['place_average_time'];

            $Place_Object = new Place($place_id, $place_name,$place_type, $place_rating, $place_lat, $place_lng,  $place_icon, $place_open, $place_cover_image,$place_average_time);
            //$Place_Object->setCoverImage($place_cover_image);
            $Place_Object->setAverageTime($place_average_time);
            array_push($PlaceObjectArray,$Place_Object);
        }
        
    }

}

function checkFoodPresent(&$preferences){
   foreach($preferences as $i){
           if($i == "Food"){
               return true;
               addFoodActivity();
           }
           else{
               return false;
           }
   }
}

//function addFoodActivity(&$arrayToClient){
//    
//    
//    
//}




function addFood(&$PlaceObjectArray,&$arrayToClient){
   $foodCount = 0; 
    
    foreach($PlaceObjectArray as $place){
        
        
        if (!checkFoodActivityExists($foodCount)) {
            if ($place->place_type == "Food" && $foodCount < 2 ) {
                $foodCount ++;
                array_push($arrayToClient, $place);
                
            }
        }
//        else if($foodCount >= 1 && $place->place_type != "Food"){
//                array_push($arrayToClient, $place);
//        }
    }
  
}

function addRemaining(&$PlaceObjectArray,&$arrayToClient){
    
    foreach($PlaceObjectArray as $place){
        
       
            if ($place->place_type != "Food") {
                //echo $place->place_type;
                array_push($arrayToClient, $place);

            }
        }

}
    






function getTimeToPlace(&$PlaceObjectArray, &$arrayToClient){
    $summaryTime = 0;
    
    for($i = 0; $i < count($PlaceObjectArray) - 1; $i ++){
    

        $walkingTime = findWalkingTimeV3($PlaceObjectArray[$i]->latitude, $PlaceObjectArray[$i]->longitude, $PlaceObjectArray[$i + 1]->latitude, $PlaceObjectArray[$i + 1]->longitude);
        $activityTime = $PlaceObjectArray[$i]->average_time;

        $tempTime = $activityTime + $walkingTime;

        $summaryTime = $tempTime + $summaryTime;

        if ($summaryTime < $_SESSION['URL_USER_TIME']) {
            array_push($arrayToClient, $PlaceObjectArray[$i]);
        } else {
            //echo "not adding anymore";
        }
        

        //echo "</br>->: ". $PlaceObjectArray[$i]->place_id  . " walking = ". $walkingTime . " + avg time " . $activityTime ."  total = " . $tempTime ." </br>"; 
        
        
        
    }
    
    //echo "</br></br>" . $summaryTime;
}


//shuffle($PlaceObjectArray);



//Get all the data from DB
extractFromDatabase($db,$PlaceObjectArray, $preferences);


//If food was requested add a food place and continue
if(checkFoodPresent($preferences)== true){
    addFood($PlaceObjectArray, $finalArray);
    addRemaining($PlaceObjectArray, $finalArray);
}
else{
    addRemaining($PlaceObjectArray, $finalArray);
}


//Find out how long it takes to get to one place
getTimeToPlace($finalArray,$arrayToClient);




$arrayObjectTitle = "PlaceObject";


returnJsonToClient($arrayObjectTitle, $arrayToClient);














/*
Trigger: Called if less than three preferences passed from the client.
 * This function will take the preferences passed from the client and comapre them with all preference options available.
 * It will then remove the ones already selected from an option pool. 
 * It will then populate the available preference slots with random preferences.
*/

function populatePreferences(){
   
    //Preferences that have been passed in, add them to an array if they are not empty.
    $URL_pref = array();
    if(!empty($_SESSION["URL_PREFERENCE_1"])){ array_push($URL_pref, $_SESSION["URL_PREFERENCE_1"]);}
    if(!empty($_SESSION["URL_PREFERENCE_2"])){ array_push($URL_pref, $_SESSION["URL_PREFERENCE_2"]);}
    if(!empty($_SESSION["URL_PREFERENCE_3"])){ array_push($URL_pref, $_SESSION["URL_PREFERENCE_3"]);}

    //All possible preferences provided by Tripit.
    $Possible_pref = array("Food", "History", "Leisure", "Entertainment", "Arts_Culture", "Sport", "PopularPlaces", "AfterDark");
    
    //Compare two new arrays above and
    //remove already selected preferences from the option pool.
    foreach ($URL_pref as $i) {

        foreach ($Possible_pref as $x) {
            
            if ($i == $x) {

                if (($key = array_search($x, $Possible_pref)) !== false) {
                    unset($Possible_pref[$key]);
                }

            }
        }
    }
    
    //Removed Elements have created and array with inconsistent indexes.
    $ResetIndexPreferences = array_values($Possible_pref);
    
    //How many additional preferences should this function provide.
    $difference = 3 - count($URL_pref);
    
    
    $Preferences = array();
    
    //For as many preferences needed;
    for($i =0; $i < $difference; $i ++){
        //Select a random index of the possible preferences remaining.
        $RandomIndex = rand(1, count($ResetIndexPreferences) - 1);
        //Add new preference to selection.
        array_push($Preferences,$ResetIndexPreferences[$RandomIndex]);
        //Set the recently added index's value to be the value index offest by 1 to prevent duplicates.
        $ResetIndexPreferences[$RandomIndex] = $ResetIndexPreferences[$RandomIndex -1];
    }

    //Combine the original passed preferences with the new random ones.
    for($i = 0; $i < count($Preferences); $i ++){
        array_push($URL_pref, $Preferences[$i]);
    }
    

    //Link the preferences to variables for server work.
    $_SESSION["URL_PREFERENCE_1"] = $URL_pref[0];
    $_SESSION["URL_PREFERENCE_2"] = $URL_pref[1];
    $_SESSION["URL_PREFERENCE_3"] = $URL_pref[2];
    
   
    //Continue with the program as normal, as if the correct amount of values where passed originally.
}


function automateHTTPRequest(){
    //echo "TEST MODE -> Dundalk Town Square";
    $_SESSION["URL_LATITUDE"] =  "53.997945";
    $_SESSION["URL_LONGITUDE"]=  "-6.4059567";
    $_SESSION["URL_PREFERENCE_1"] =  "Food";
    $_SESSION["URL_PREFERENCE_2"] =  "History";
    $_SESSION["URL_PREFERENCE_3"] = "Entertainment";
    $_SESSION["URL_USER_TIME"] =  240;
    
}


function printHTTPRequest(){
    echo "<br>Printing Request";
    echo "<br>LAT -> ". $_SESSION["URL_LATITUDE"] . "<br>LNG -> " . $_SESSION["URL_LONGITUDE"] . "<br>". $_SESSION["URL_PREFERENCE_1"] . ", " . $_SESSION["URL_PREFERENCE_2"] . ", " . $_SESSION["URL_PREFERENCE_3"] . "<br>MINs ->";// . $_SESSION["URL_USER_TIME"];
}

session_destroy();

?>

