var south = L.latLng(-8576 / 2, -8576 / 2),
    north = L.latLng(8576 / 2, 8576 / 2),
    bounds = L.latLngBounds(south, north);

var map = L.map('map', {
    center: [51.505, -0.09],
    zoom: 2,
    minZoom: 2,
    maxBounds: bounds
});

var geocoder = L.Control.Geocoder.nominatim();
if (typeof URLSearchParams !== 'undefined' && location.search) {
    var params = new URLSearchParams(location.search);
    var geocoderString = params.get('geocoder');
    if (geocoderString && L.Control.Geocoder[geocoderString]) {
        console.log('Using geocoder', geocoderString);
        geocoder = L.Control.Geocoder[geocoderString]();
    } else if (geocoderString) {
        console.warn('Unsupported geocoder', geocoderString);
    }
}

var control = L.Control.geocoder({
    query: '',
    placeholder: 'Recherchez ici ...',
    geocoder: geocoder
}).addTo(map);

var marker;

setTimeout(function() {
    control.setQuery('');
}, 12000);

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
        }),

        Precipitation = L.tileLayer('https://tile.openweathermap.org/map/precipitation_new/{z}/{x}/{y}.png?appid=94c6cf0868fa5cb930a5e2d71baf0dbf', {
            maxZoom: 18
        }),

        Wind = L.tileLayer('https://tile.openweathermap.org/map/wind_new/{z}/{x}/{y}.png?appid=94c6cf0868fa5cb930a5e2d71baf0dbf', {
            maxZoom: 18
        }),

        Pressure = L.tileLayer('https://tile.openweathermap.org/map/pressure_new/{z}/{x}/{y}.png?appid=94c6cf0868fa5cb930a5e2d71baf0dbf', {
            maxZoom: 18
        }),

        Clouds = L.tileLayer('https://tile.openweathermap.org/map/clouds_new/{z}/{x}/{y}.png?appid=94c6cf0868fa5cb930a5e2d71baf0dbf', {
            maxZoom: 18
        });
        Temp.addTo(map);

var overlays = {"Température": Temp, "Précipitations": Precipitation, "Nuages": Clouds, "Pression de l'air": Pressure, "Vitesse du vent": Wind};
L.control.layers(overlays, null, {collapsed:false}).addTo(map);

var greenIcon = new L.Icon({
    iconUrl: 'images/marker-icon-2x-green.png',
    shadowUrl: 'images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
  });

if(typeof favs !== 'undefined') {
    var i = 0;
    $.each(favs, function(key, value) {        
        $.get('ajax.php', {
            lat: value.lat,
            lng: value.lon
        }, function(data, status) {
            var jsonF = JSON.parse(data);
            window['fav'+i] = L.marker([value.lat, value.lon], {icon: greenIcon}).addTo(map);
            window['fav'+i].bindPopup(jsonF.meteo);
            window['fav'+i].on('click', function() {
                $('#city').val(jsonF.city);
            });
        });
        i++;
    });
}

map.on('click', function(e) {
    var clickedlat = e.latlng.lat;
    var clickedlng = e.latlng.lng;
    if(clickedlat && clickedlng) {
        $.get('ajax.php', {
        lat: clickedlat,
        lng: clickedlng
        }, function(data, status) {
            var jsonM = JSON.parse(data);
            if (marker) {
                marker.setLatLng([clickedlat, clickedlng]);
                if(jsonM.meteo == "Ville introuvable") {
                    marker.setPopupContent(jsonM.meteo);
                } else {
                    marker.setPopupContent(jsonM.meteo + " <input height='22' width='12' type='image' id='add-fav' src='images/marker-icon-2x-green.png' alt='marker add'/>");
                }                
                marker.openPopup();
                map.panTo([marker.getLatLng().lat,marker.getLatLng().lng]);
                if(typeof id !== 'undefined') {
                    $("#add-fav").on('click', function() {
                        $.get('ajax.php', {
                            latitude: marker.getLatLng().lat,
                            longitude: marker.getLatLng().lng,
                            id: id
                        }, function(dataC, status) {
                            marker.setPopupContent(jsonM.meteo)
                            var json = JSON.parse(dataC);
                            window['fav'+i] = L.marker([json.newLat, json.newLon], {icon: greenIcon}).addTo(map);
                            window['fav'+i].bindPopup(jsonM.meteo);
                            window['fav'+i].on('click', function() {
                                $('#city').val(jsonM.city);
                            });
                            i++;
                        });
                    });
                } else {
                    $("#add-fav").on('click', function() {
                        window.location.href = "connexion.html";
                    });
                }
                $('#city').val(jsonM.city);
            } else {
                marker = L.marker([clickedlat, clickedlng]);
                if(jsonM.meteo == "Ville introuvable") {
                    marker.bindPopup(jsonM.meteo);
                } else {
                    marker.bindPopup(jsonM.meteo + " <input height='22' width='12' type='image' id='add-fav' src='images/marker-icon-2x-green.png' alt='marker add'/>");
                }
                marker.addTo(map);
                marker.openPopup();
                map.panTo([marker.getLatLng().lat,marker.getLatLng().lng]);
                if(typeof id !== 'undefined') {
                    $("#add-fav").on('click', function() {
                        $.get('ajax.php', {
                            latitude: marker.getLatLng().lat,
                            longitude: marker.getLatLng().lng,
                            id: id
                        }, function(dataC, status) {
                            marker.setPopupContent(jsonM.meteo)
                            var json = JSON.parse(dataC);
                            window['fav'+i] = L.marker([json.newLat, json.newLon], {icon: greenIcon}).addTo(map);
                            window['fav'+i].bindPopup(jsonM.meteo);
                            window['fav'+i].on('click', function() {
                                $('#city').val(jsonM.city);
                            });
                            i++;
                        });
                    });
                } else {
                    $("#add-fav").on('click', function() {
                        window.location.href = "connexion.html";
                    });
                }
                $('#city').val(jsonM.city);
            }
        });
    }    
});

$("#city").on('keydown paste focus mousedown', function(e){
    if(e.keyCode != 9)
        e.preventDefault();
});

const legend = L.control.Legend({
    position: "bottomleft",
    collapsed: false,
    symbolWidth: 24,
    opacity: 0.8,
    column: 1,
    legends: [{
        label: "Votre marqueur",
        type: "image",
        url: "images/marker-icon-2x-blue.png",
    }, {
        label: "Villes favorites",
        type: "image",
        url: "images/marker-icon-2x-green.png"
    }]
}).addTo(map);
