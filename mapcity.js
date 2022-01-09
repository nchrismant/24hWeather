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

L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
    maxZoom: 18,
    id: 'mapbox/streets-v11',
    tileSize: 512,
    zoomOffset: -1,
    accessToken: 'pk.eyJ1IjoibmF0aGFuLS0iLCJhIjoiY2t3NnY0a2dxMHRwNjJ4cWxyOXB2eDVtaSJ9.0Dji_rE_4lPmnIgOAOwm9Q'
}).addTo(map);

var Temp = L.tileLayer('https://tile.openweathermap.org/map/temp_new/{z}/{x}/{y}.png?appid=94c6cf0868fa5cb930a5e2d71baf0dbf', {
            maxZoom: 18,
            id: 'temp'
        });

Temp.addTo(map);
