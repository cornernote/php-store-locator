<?php
/**
 * PHP Store Locator
 *
 * Copyright (c) 2012 Brett O'Donnell <brett@mrphp.com.au>
 * Source Code: https://github.com/cornernote/php-store-locator
 * Home Page: http://mrphp.com.au/blog/php-store-locator
 * License: GPLv3
 */

mysql_connect('localhost', 'root', '');
mysql_select_db('dbname');

$google_maps_api_key = 'YOUR_API_KEY';

$id = isset($_GET['id']) && (int)$_GET['id'] ? (int)$_GET['id'] : false;
$zipcode = isset($_GET['zipcode']) ? sprintf('%04d', (int)$_GET['zipcode']) : '0000';

$postcode = false;
if ($zipcode != '0000') {
    $postcode = mysql_fetch_assoc(mysql_query("SELECT * FROM custom_location_data WHERE zipcode='$zipcode'"));
}
$store = mysql_fetch_assoc(mysql_query("
    SELECT
        `custom_store_locations`.*
    FROM
        `custom_store_locations`
    WHERE
        `id`=$id
"));
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no"/>
    <style type="text/css">
        html {
            height: 100%
        }

        body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        #map_canvas {
            height: 100%
        }
    </style>
    <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
    <script type="text/javascript">
        function initialize() {
            var directionsDisplay = new google.maps.DirectionsRenderer();
            var directionsService = new google.maps.DirectionsService();
            var map = new google.maps.Map(document.getElementById("map_canvas"), {
                zoom:15,
                center:new google.maps.LatLng(<?php echo $store['lat']; ?>, <?php echo $store['lon']; ?>),
                mapTypeId:google.maps.MapTypeId.ROADMAP
            });
            var markers = [];
            var infowindows = [];
            var positions = [];

            markers[<?php echo $store['id']; ?>] = new google.maps.Marker({
                position:new google.maps.LatLng(<?php echo $store['lat']; ?>, <?php echo $store['lon']; ?>),
                map:map,
                title:"<?php echo $store['name']; ?>",
                icon:'bisley_map_pointer.png'
            });
            infowindows[<?php echo $store['id']; ?>] = new google.maps.InfoWindow({
                content:'<?php echo addslashes($store['name'] . '<br/>' . $store['address'] . '<br/>' . $store['city'] . ', ' . $store['state'] . ', ' . $store['zipcode'] . '<br/>' . $store['country'] . '<br/>Phone: ' . $store['phone']); ?>'
            });
            google.maps.event.addListener(markers[<?php echo $store['id']; ?>], 'click', function () {
                infowindows[<?php echo $store['id']; ?>].open(map, markers[<?php echo $store['id']; ?>]);
            });

        <?php if ($postcode) { ?>
            directionsDisplay.setMap(map);
            directionsDisplay.setPanel(document.getElementById("directions"));

            var request = {
                origin:new google.maps.LatLng(<?php echo $postcode['lat']; ?>, <?php echo $postcode['lon']; ?>),
                destination:new google.maps.LatLng(<?php echo $store['lat']; ?>, <?php echo $store['lon']; ?>),
                travelMode:google.maps.DirectionsTravelMode.DRIVING
            };
            directionsService.route(request, function (response, status) {
                if (status == google.maps.DirectionsStatus.OK) {
                    directionsDisplay.setDirections(response);
                }
            });
            <?php } ?>
        }
    </script>
</head>
<body onload="initialize()">
<div id="store_locations" style="float:left; width:20%; height: 100%; overflow: scroll;">
    <div class="store" style="margin: 5px; border:1px solid #ccc;">
        <?php echo $store['name'] . '<br/>' . $store['address'] . '<br/>' . $store['city'] . ', ' . $store['state'] . ', ' . $store['zipcode'] . '<br/>' . $store['country'] . '<br/>Phone: ' . $store['phone']; ?>
        <br/><a href="#">visit store</a>
    </div>
    <div id="directions"></div>
</div>
<div id="map_canvas" style="float:left; width:80%; height:100%"></div>
</body>
</html>