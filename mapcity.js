var south = L.latLng(-8576 / 2, -8576 / 2),
    north = L.latLng(8576 / 2, 8576 / 2),
    bounds = L.latLngBounds(south, north);

var citylat = citypos[0].lat
var citylon = citypos[0].lon


var map = L.map('mapcity', {
    zoom: 11,
    minZoom: 2,
    maxBounds: bounds
});

map.setView([citylat, citylon]);

L.tileLayer('/proxy.php?service=mapbox&z={z}&x={x}&y={y}', {
    maxZoom: 18,
    tileSize: 512,
    zoomOffset: -1
}).addTo(map);

var Temp = L.tileLayer('/proxy.php?service=openweather&z={z}&x={x}&y={y}', {
            maxZoom: 18,
            id: 'temp'
        });

Temp.addTo(map);
