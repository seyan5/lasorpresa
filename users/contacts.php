<?php

ob_start();
session_start();
include("../admin/inc/config.php");
include("../admin/inc/functions.php");

?>
<?php include('navuser.php'); ?>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
        #map {
            height: 600px;
            width: 50%;
            margin-bottom: 20px;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 13rem !important;
            margin: 20px auto; /* Center the map horizontally */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Add subtle shadow for a clean look */
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

<style>
    /* Map container styling */
    

    /* Information panel styling */
    .info-panel {
        max-width: 800px; /* Restrict maximum width for readability */
        margin: 20px auto; /* Center the panel and add spacing */
        padding: 20px;
        border: 1px solid #ccc; /* Subtle border */
        border-radius: 10px; /* Rounded corners */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Soft shadow */
        background-color: #ffffff; /* Neutral background */
        font-family: Arial, sans-serif; /* Clean, modern font */
    }

    /* Info panel heading */
    .info-panel h1 {
        margin: 0;
        font-size: 3rem !important; /* Slightly larger font for emphasis */
        color: #333333; /* Darker shade for visibility */
        text-align: center; /* Center-align heading */
    }

    /* Info panel paragraph styling */
    .info-panel p {
        margin: 10px 0;
        font-size: 1.5rem !important; /* Standardized font size */
        color: #555555; /* Medium shade for readability */
        line-height: 1.5; /* Better spacing between lines */
    }

    /* Button styling */
    .info-panel button {
        display: block; /* Make the button occupy its own line */
        background-color: #007bff; /* Primary blue color */
        color: #ffffff; /* White text for contrast */
        border: none; /* Remove borders */
        padding: 10px 15px; /* Standard padding */
        border-radius: 5px; /* Rounded corners */
        font-size: 1.5rem !important; /* Standardized font size */
        cursor: pointer; /* Pointer cursor on hover */
        text-align: center; /* Center-align text */
        margin: 10px auto; /* Center the button horizontally */
        transition: background-color 0.3s; /* Smooth hover transition */
    }

    /* Button hover effect */
    .info-panel button:hover {
        background-color: #0056b3; /* Darker blue on hover */
    }

    /* Responsive styling */
    @media (max-width: 768px) {
        #map {
            height: 400px; /* Reduce height for smaller screens */
        }

        .info-panel {
            padding: 15px; /* Reduce padding on smaller screens */
        }

        .info-panel h1 {
            font-size: 1.5rem; /* Adjust heading size */
        }

        .info-panel p {
            font-size: 0.9rem; /* Adjust paragraph size */
        }

        .info-panel button {
            font-size: 0.9rem; /* Adjust button font size */
        }
    }
</style>
