<?php

ob_start();
session_start();
include("../admin/inc/config.php");
include("../admin/inc/functions.php");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <title>La Sorpresa Flower Shop</title>
    <style>
        #map {
            height: 600px;
            width: 50%;
            margin-bottom: 20px;
            border-radius: 10px;
            overflow: hidden;
        }

        .info-panel {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            background-color: #f9f9f9;
            font-family: Arial, sans-serif;
        }

        .info-panel h1 {
            margin: 0;
            font-size: 1.5rem;
            color: #333;
        }

        .info-panel p {
            margin: 10px 0;
            font-size: 0.9rem;
            color: #666;
        }

        .info-panel button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 0.9rem;
            cursor: pointer;
            text-align: center;
        }

        .info-panel button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <!-- Map container -->
    <div id="map"></div>

    <!-- Information panel -->
    <div class="info-panel">
        <h1>La Sorpresa Flower Shop</h1>
        <p><strong>Address:</strong> Tanza, Cavite 4108 Paradahan I, Philippines</p>
        <p><strong>Serving:</strong> Tanza, Philippines · Trece Martires · Indang, Philippines · Naic, Philippines · Imus, Philippines · Kawit, Philippines · General Trias, Philippines · Rosario, Philippines · Dasmariñas, Philippines</p>
        <button onclick="openDirections()">Get Directions</button>
    </div>

    <script>
        // Initialize the map and set its view to the exact location of the shop
        var map = L.map('map').setView([14.3246558, 120.8600958], 15);

        // Add OpenStreetMap tiles
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        // Add a marker at the exact shop's location
        var shopMarker = L.marker([14.3246558, 120.8600958]).addTo(map);

        // Bind a popup to the marker
        shopMarker.bindPopup("<b>La Sorpresa Flower Shop</b><br>Tanza, Cavite 4108 Paradahan I, Philippines").openPopup();

        // Function to open Google Maps for directions
        function openDirections() {
            window.open(
                "https://www.google.com/maps/dir/?api=1&destination=14.3246558,120.8600958",
                "_blank"
            );
        }
    </script>

</body>

</html>
