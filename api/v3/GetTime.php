<?php

include_once  '../QueryRouteAPI.php';

//
$originLat = "54.0045042";
$originLng = "-6.3979302";
$destinationLat = "54.0045329";
$destinationLng = "-6.4013169";

function findWalkingTimeV3(&$originLat, &$originLng, &$destinationLat, &$destinationLng){
    
    return getDistanceLatLng($originLat, $originLng, $destinationLat, $destinationLng);
    
}