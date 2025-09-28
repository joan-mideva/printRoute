// frontend/app.js

// Initialize the AngularJS module
var app = angular.module('printRouteApp', []);

// Main Controller for the landing page
app.controller('MainController', function($scope, $http) {
    $scope.message = "Welcome to printRoute!";
    
    // Function to fetch nearby shops from the backend
    $scope.loadShops = function() {
        // Example of an AJAX call to the backend
        /*
        $http.get('../backend/shop.php?action=get_nearby_shops&lat=...&lng=...')
            .then(function(response) {
                $scope.shops = response.data;
                // Code to add markers to the Google Map would go here
            });
        */
    };
});

// Placeholder for Google Map initialization
function initMap() {
    // Default location (Ahmedabad) if geolocation fails or is denied
    const defaultLocation = { lat: 23.0225, lng: 72.5714 };

    // --- 1. Get User's Current Location ---
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                // Success: Use the real location
                const userLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude,
                };
                console.log("User location found:", userLocation);
                createMap(userLocation);
            },
            () => {
                // Error or permission denied: Use the default location
                console.warn("Geolocation failed or was denied. Using default location.");
                createMap(defaultLocation);
            }
        );
    } else {
        // Browser doesn't support geolocation
        console.error("Your browser doesn't support geolocation.");
        createMap(defaultLocation);
    }
}

// --- 2. Create the Map and Add Markers ---
function createMap(location) {
    // Create the map, centered on the user's location
    const map = new google.maps.Map(document.getElementById("map"), {
        zoom: 14, // Zoom in a bit closer to see the neighborhood
        center: location,
    });

    // Add a marker for the user's current location
    new google.maps.Marker({
        position: location,
        map: map,
        title: "Your Location",
        icon: {
            path: google.maps.SymbolPath.CIRCLE,
            scale: 8,
            fillColor: "#4285F4", // Google Blue
            fillOpacity: 1,
            strokeWeight: 2,
            strokeColor: "#ffffff",
        },
    });

    // --- 3. Add Example Markers for Nearby Shops ---
    // In a real application, you would fetch these from your backend database.
    const exampleShops = [
        {
            position: { lat: 23.0031, lng: 72.5959 },
            title: "Sonal Xerox"
        },
        {
            position: { lat: 23.0028, lng: 72.5999 },
            title: "Janta Xerox - Digital Printing"
        },
        {
            position: { lat: 23.0084, lng: 72.5960 },
            title: "Krishna Xerox and Thesis Binding"
        },
        {
            position: { lat: 23.0053, lng: 72.5925 },
            title: "Radhey Xerox and Stationary"
        }
    ];

    // Loop through the example shops and create a marker for each one
    exampleShops.forEach((shop) => {
        new google.maps.Marker({
            position: shop.position,
            map: map,
            title: shop.title,
            // Custom icon for shops will be added later
        });
    });
}
