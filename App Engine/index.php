<?php 
	session_start();
	require_once 'php/google-api-php-client/vendor/autoload.php';
    $client = new Google_Client();
	$client->useApplicationDefaultCredentials();
	$client->addScope(Google_Service_Bigquery::BIGQUERY);
	$bigquery = new Google_Service_Bigquery($client);
	$projectId = 's3658817-assignment2';
?>

<!DOCTYPE HTML>
<html>
	<head>
		<title>KripTech Flight Tracker</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="stylesheet" href="css/main.css" />
		<style>
			#map {
			  width: 100%;
			  height: 400px;
			  background-color: grey;
			}
		  </style>
	</head>
	<body>

		<!-- Header -->
			<header id="header">
				<div class="logo"><a href="#">KripTech <span><br>Flight Tracker</span></a></div>
			</header>

		<!-- Main -->
			<section id="main">
				<div class="inner">

				<!-- One -->
					<section id="one" class="wrapper style1">

						<div class="image fit flush">
							<div id="map"></div>

<script > 
    function processFlightAPI() {
        var locations = [];
        const userAction = async () => {
            const response = await fetch('https://opensky-network.org/api/states/all');
            const myJson = await response.json(); //extract JSON from the http response
            flightData = myJson.states;
            for (var i = 0; i < flightData.length; i++) {
                callsign = flightData[i][1].trim();
                origin_country = flightData[i][2];
                long = flightData[i][5];
                lat = flightData[i][6];
                altitude = flightData[i][7];
                velocity = flightData[i][9]; // m/s

                locations.push(['<div id="content">' +
                    '<div id="siteNotice">' +
                    '</div>' +
                    '<h1 id="firstHeading" class="firstHeading">' + callsign + '</h1>' +
                    '<div id="bodyContent">' +
                    '<p>Originating Country: ' + origin_country + '<br>' +
                    'Altitude: ' + altitude + '<br>' +
                    'Velocity: ' + velocity + '<br>' +
                    'Latitude: ' + lat + '<br>' +
                    'Longitude: ' + long + '<br></p>' +
                    '</div>' +
                    '</div>', lat, long
                ]);
            }
            createMap(locations);
        }
        userAction();
    }
var map;

processFlightAPI();

function createMap(locations) {


    var infowindow = new google.maps.InfoWindow();

    var marker, i;

    for (i = 0; i < locations.length; i++) {
        var image = 'images/plane.png';
        marker = new google.maps.Marker({
            position: new google.maps.LatLng(locations[i][1], locations[i][2]),
            map: map,
            icon: image
        });

        google.maps.event.addListener(marker, 'click', (function(marker, i) {
            return function() {
                infowindow.setContent(locations[i][0]);
                infowindow.open(map, marker);
            }
        })(marker, i));
    }
}

function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 2,
        center: {
            lat: 12,
            lng: 30
        }
    });
} 
</script> 

<script async defer src = "https://maps.googleapis.com/maps/api/js?key=&callback=initMap" ></script>
						</div>
						<header class="special">
							<h2>Search for an aircraft by callsign</h2>
							<form method="POST">
							<input type="text" name="callsign" id="callsign" style="text-align:center">
							<br>
							<input type="submit" value="Search">
							</form>
						</header>
						<div class="content">
                        <?php
	
		if(isset($_POST['callsign'])){
        $request = new Google_Service_Bigquery_QueryRequest();
		$str = '';
		$request->setQuery("SELECT * FROM flight_info_all.flight_info WHERE callsign LIKE '%".$_POST['callsign']."%' ");
		
		$response = $bigquery->jobs->query($projectId, $request);
		$rows = $response->getRows();
		$_SESSION['dataResponse'] = $rows;

		$str = "<table>".
		"<tr>" .
		"<th>Callsign</th>" .
		"<th>Origin Country</th>" .
		"<th>Longitude</th>" .
		"<th>Latitude</th>" .
		"</tr>";
		
		foreach ($rows as $row)
		{
			$str .= "<tr>";
            $i = 0;
			foreach ($row['f'] as $field)
			{
                if ($i != 0 && $i < 5){
                $str .= "<td>" . $field['v'] . "</td>";
                }

                $i++;
			}
			$str .= "</tr>";
		}

		$str .= '</table></div>';

		echo $str;
	}
    ?>
    <center>
    <iframe src='https://minnit.chat/KripTechFlightTracker?embed&&nickname=' style='border:none;width:90%;height:300px;' allowTransparency='true'></iframe><br><a href='https://minnit.chat/KripTechFlightTracker' target='_blank'>HTML5 Chatroom powered by Minnit Chat</a>
    </center>
    </div>
                        </div>
					</section>

		<!-- Scripts -->
			<script src="js/jquery.min.js"></script>
			<script src="js/jquery.poptrox.min.js"></script>
			<script src="js/skel.min.js"></script>
			<script src="js/util.js"></script>
			<script src="js/main.js"></script>

	</body>
</html>
<?php

