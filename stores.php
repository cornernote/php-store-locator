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

$distance = isset($_GET['distance']) && (int)$_GET['distance'] ? (int)$_GET['distance'] : 1000;
$zipcode = isset($_GET['zipcode']) ? sprintf('%04d', (int)$_GET['zipcode']) : '0000';

if ($zipcode == '0000') {
    $_stores = mysql_query("
        SELECT
            `custom_store_locations`.*
        FROM
            `custom_store_locations`
    ");
}
else {
    $postcode = mysql_fetch_assoc(mysql_query("SELECT * FROM custom_location_data WHERE zipcode='$zipcode'"));
    $_stores = mysql_query("
        SELECT
            ((ACOS(SIN($postcode[lat] * PI() / 180) * SIN(lat * PI() / 180) + COS($postcode[lat] * PI() / 180) * COS(lat * PI() / 180) * COS(($postcode[lon] - lon) * PI() / 180)) * 180 / PI()) * 60 * 1.1515 * 1.609) AS `distance`,
            `custom_store_locations`.*
        FROM
            `custom_store_locations`
        HAVING
            `distance`<=$distance
        ORDER BY
            `distance` ASC
        LIMIT
            10
    ");
}
$stores = array();
while ($store = mysql_fetch_assoc($_stores)) {
    $stores[] = $store;
}
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
            var map = new google.maps.Map(document.getElementById("map_canvas"), {
                zoom:0,
                center:new google.maps.LatLng(0, 0),
                mapTypeId:google.maps.MapTypeId.ROADMAP
            });
            var markers = [];
            var infowindows = [];
            var positions = [];

        <?php foreach ($stores as $store) { ?>

            markers[<?php echo $store['id']; ?>] = new google.maps.Marker({
                position:new google.maps.LatLng(<?php echo $store['lat']; ?>, <?php echo $store['lon']; ?>),
                map:map,
                title:"<?php echo $store['name']; ?>",
                icon:'bisley_map_pointer.png'
            });

            <?php if ($zipcode != '0000') { ?>
                infowindows[<?php echo $store['id']; ?>] = new google.maps.InfoWindow({
                    content:'<?php echo addslashes($store['name'] . '<br/>' . $store['address'] . '<br/>' . $store['city'] . ', ' . $store['state'] . ', ' . $store['zipcode'] . '<br/>' . $store['country'] . '<br/>Phone: ' . $store['phone']); ?>' +
                            '<br/><a href="<?php echo ($zipcode == '0000') ? "store.php?id=$store[id]" : "store.php?id=$store[id]&zipcode=$zipcode"; ?>">visit store</a>'
                });
                google.maps.event.addListener(markers[<?php echo $store['id']; ?>], 'click', function () {
                    infowindows[<?php echo $store['id']; ?>].open(map, markers[<?php echo $store['id']; ?>]);
                });
                <?php } ?>
            <?php } ?>

            var bounds = new google.maps.LatLngBounds();
            for (var id in markers) {
                bounds.extend(markers[id].position);
            }
            map.fitBounds(bounds);
        }

    </script>
</head>
<body onload="initialize()">
<div id="store_locations" style="float:left; width:20%; height: 100%; overflow: scroll;">
    <form action="stores.php" method="get">
        Post Code: <input name="zipcode" size="5" value="<?php echo $_GET['zipcode']; ?>"/>
        <input type="submit" value="Search!"/>
    </form>
    <?php foreach ($stores as $store) { ?>
    <div class="store" style="margin: 5px; border:1px solid #ccc;">
        <?php echo $store['name'] . '<br/>' . $store['address'] . '<br/>' . $store['city'] . ', ' . $store['state'] . ', ' . $store['zipcode'] . '<br/>' . $store['country'] . '<br/>Phone: ' . $store['phone']; ?>
        <br/><a href="<?php echo ($zipcode == '0000') ? "store.php?id=$store[id]" : "store.php?id=$store[id]&zipcode=$zipcode"; ?>">visit
        store</a>
    </div>
    <?php } ?>
</div>
<div id="map_canvas" style="float:left; width:80%; height:100%"></div>
</body>
</html>