<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$originLat =  htmlspecialchars($_GET["originLat"]);
$originLng =  htmlspecialchars($_GET["originLng"]);
$destinationLat =  htmlspecialchars($_GET["destinationLat"]);
$destinationLng =  htmlspecialchars($_GET["destinationLng"]);

include_once  '../QueryRouteAPI.php';





$travelMode = "Walking";

class travel{
    public $originLat;
    public $originLng;
    public $travelMode;
    public $travelTime;
    //public $travelDistance;
    public $destinationLat;
    public $destinationLng;
    
    function __construct($originLat,$originLng,$travelMode, $travelTime,$destinationLat,$destinationLng) {
        $this->originLat = $originLat;
        $this->originLng = $originLng;
        $this->travelMode = $travelMode;
        $this->travelTime = $travelTime;
        $this->destinationLat = $destinationLat;
        $this->destinationLng = $destinationLng;
    }
}



$walkingTime = findWalkingTime($originLat, $originLng, $destinationLat, $destinationLng). " mins";
    
    
function findWalkingTime(&$originLat, &$originLng, &$destinationLat, &$destinationLng){
    
    return getDistanceLatLng($originLat, $originLng, $destinationLat, $destinationLng);
    
}
    



    

