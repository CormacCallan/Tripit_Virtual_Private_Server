<?php
session_start();
//HTTP Request Variables
$_SESSION["URL_LATITUDE"] = $_GET["lat"];
$_SESSION["URL_LONGITUDE"] = $_GET["lng"];
$_SESSION["URL_PREFERENCE_1"] = $_GET['pref1'];
$_SESSION["URL_PREFERENCE_2"] = $_GET['pref2'];
$_SESSION["URL_PREFERENCE_3"] = $_GET['pref3'];
$_SESSION["URL_USER_TIME"] = $_GET['time'];




function br(){echo "<br>";}



try {
    if ($_SESSION["URL_PREFERENCE_1"]|| empty($_SESSION["URL_PREFERENCE_2"]) || empty($_SESSION["URL_PREFERENCE_3"]) ) {
        populatePreferences();
        echo "populated preferecens";
        printHTTPRequest();
    } else {
        echo "continuing as normal";
        printHTTPRequest();
    }
} catch (Exception $ex) {
    echo 'TRIPIT_DEV: Failed to parse URL\nCaught exception: ',  $e->getMessage(), "\n";
}





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
    //parseHTTPRequest();
}


function automateHTTPRequest(&$latitude, &$longitude, &$preference1, &$preference2, &$preference3, &$userTime){
    echo "running default";
    $latitude =  "53.997945";
    $longitude =  "-6.4059567";
    $preference1 =  "Food";
    $preference2 =  "History";
    $preference3 = "Entertainment";
    $userTime =  240;
}


function printHTTPRequest(){
    echo "<br>Printing Request";
    echo "<br>LAT -> ". $_SESSION["URL_LATITUDE"] . "<br>LNG -> " . $_SESSION["URL_LONGITUDE"] . "<br>". $_SESSION["URL_PREFERENCE_1"] . ", " . $_SESSION["URL_PREFERENCE_2"] . ", " . $_SESSION["URL_PREFERENCE_3"] . "<br>MINs ->" . $_SESSION["URL_USER_TIME"];
}

session_destroy();

?>

