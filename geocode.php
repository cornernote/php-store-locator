<?php
/**
 * PHP Store Locator
 *
 * Copyright (c) 2012 Brett O'Donnell <brett@mrphp.com.au>
 * Source Code: https://github.com/cornernote/php-store-locator
 * Home Page: http://mrphp.com.au/blog/php-store-locator
 * License: GPLv3
 */

set_time_limit(60 * 60 * 24);
mysql_connect('localhost', 'root', '');
mysql_select_db('dbname');

$google_maps_api_key = 'YOUR_API_KEY';

$addresses = mysql_query("SELECT * FROM custom_location_data WHERE lat=0");
while ($address = mysql_fetch_assoc($addresses)) {
    $url = "http://maps.google.com/maps/geo?output=xml&key=$google_maps_api_key&q=" . urlencode($address['city'] . ', ' . $address['zipcode'] . ', ' . $address['state'] . ', ' . $address['country']);
    $xml = simplexml_load_file($url) or die("url not loading");

    $status = $xml->Response->Status->code;
    if (strcmp($status, "200") == 0) {
        $coordinates = $xml->Response->Placemark->Point->coordinates;
        $coordinatesSplit = split(",", $coordinates);
        mysql_query("UPDATE custom_location_data SET lat='$coordinatesSplit[1]', lon='$coordinatesSplit[0]' WHERE id='$address[id]'");
        echo 'geocodes update<br/>';
    }
    else if (strcmp($status, "620") == 0) {
        echo 'sent geocodes too fast<br/>';
        sleep(5);
    }
    else {
        echo 'failure to geocode ' . $status . '<br/>';
        print '<pre>';
        print_r($address);
        print '</pre>';
    }
    flush();
}


$addresses = mysql_query("SELECT * FROM custom_store_locations WHERE lat=0");
while ($address = mysql_fetch_assoc($addresses)) {
    $url = "http://maps.google.com/maps/geo?output=xml&key=$google_maps_api_key&q=" . urlencode($address['city'] . ', ' . $address['zipcode'] . ', ' . $address['state'] . ', ' . $address['country']);
    $xml = simplexml_load_file($url) or die("url not loading");

    $status = $xml->Response->Status->code;
    if (strcmp($status, "200") == 0) {
        $coordinates = $xml->Response->Placemark->Point->coordinates;
        $coordinatesSplit = split(",", $coordinates);
        mysql_query("UPDATE custom_store_locations SET lat='$coordinatesSplit[1]', lon='$coordinatesSplit[0]' WHERE id='$address[id]'");
        echo 'geocodes update<br/>';
    }
    else if (strcmp($status, "620") == 0) {
        echo 'sent geocodes too fast<br/>';
        sleep(5);
    }
    else {
        echo 'failure to geocode ' . $status . '<br/>';
        print '<pre>';
        print_r($address);
        print '</pre>';
    }
    flush();
}
