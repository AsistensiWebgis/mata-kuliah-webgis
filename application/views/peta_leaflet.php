<!-- <h1><center>Judul Peta</center></h1> -->
<!-- Install Leaflet-->
<div class="content">
  <div id="map" style="width: 100%; height: 100vh; color:black;"></div>
</div>
<script>

// Add Custom layers
var prov = new L.LayerGroup();

// Add Faskes Layer
var faskes = new L.LayerGroup();

//add river layers
var sungai = new L.LayerGroup();

// add province polygoon
var provin = new L.LayerGroup();

var berau = new L.LayerGroup();


var map = L.map('map', {
  center: [2.141725, 117.484784],
  zoom: 10,
  zoomControl: false,
  layers:[]
  });
var GoogleSatelliteHybrid= L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {maxZoom: 22, attribution: 'Latihan Web GIS'}).addTo(map);
var OpenStreetMap_Mapnik = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
	maxZoom: 19,
	attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
  });
var GoogleMaps = new
  L.TileLayer('https://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
    opacity: 1.0,
    attribution: 'Latihan Web GIS'
  });
var GoogleRoads = new
  L.TileLayer('https://mt1.google.com/vt/lyrs=h&x={x}&y={y}&z={z}',{
    opacity: 1.0,
    attribution: 'Latihan Web GIS'
  });
// Install leaflet.groupedlayercontrol
var baseLayers = {
  'Google Satellite Hybrid': GoogleSatelliteHybrid,
  'OpenStreetMap' : OpenStreetMap_Mapnik,
  'GoogleMaps': GoogleMaps,
  'GoogleRoads' : GoogleRoads
  };

// Make variabel for new layers
var groupedOverlays = {
  "Peta Dasar":{'Ibu Kota Provinsi' :prov, 'Jaringan Sungai' : sungai, "Provinsi": provin},
  "Peta Khusus":{'Fasilitas Kesehatan' :faskes}
  };

// Make groupedLayers
L.control.groupedLayers(baseLayers, groupedOverlays).addTo(map);

// Install Minimap
var osmUrl='https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}';
var osmAttrib='Map data &copy; OpenStreetMap contributors';
var osm2 = new L.TileLayer(osmUrl, {minZoom: 0, maxZoom: 13, attribution: osmAttrib });
var rect1 = {color: "#ff1100", weight: 3};
var rect2 = {color: "#0000AA", weight: 1, opacity:0, fillOpacity:0};
var miniMap = new L.Control.MiniMap(osm2, {toggleDisplay: true, position : "bottomright",
aimingRectOptions : rect1, shadowRectOptions: rect2}).addTo(map);

// Install Search Widget
L.Control.geocoder({position :"topleft", collapsed:true}).addTo(map);

// Install Koordinat
/* GPS enabled geolocation control set to follow the user's location */
var locateControl = L.control.locate({
  position: "topleft",
  drawCircle: true,
  follow: true,
  setView: true,
  keepCurrentZoomLevel: true,
  markerStyle: {
    weight: 1,
    opacity: 0.8,
    fillOpacity: 0.8},
  circleStyle: {
    weight: 1,
    clickable: false},
  icon: "fa fa-location-arrow",
  metric: false,
  strings: {
    title: "My location",
    popup: "You are within {distance} {unit} from this point",
    outsideMapBoundsMsg: "You seem located outside the boundaries of the map"},
  locateOptions: {
    maxZoom: 18,
    watch: true,
    enableHighAccuracy: true,
    maximumAge: 10000,
    timeout: 10000}
  }).addTo(map);

// Install Control ZoomBar
var zoom_bar = new L.Control.ZoomBar({position: 'topleft'}).addTo(map);

// Install Leaflet coordinates
L.control.coordinates({
  position:"bottomleft",
  decimals:2,
  decimalSeperator:",",
  labelTemplateLat:"Latitude: {y}",
  labelTemplateLng:"Longitude: {x}"
  }).addTo(map);
/* scala */
L.control.scale({metric: true, position: "bottomleft"}).addTo(map);

// Install Mata Angin
var north = L.control({position: "bottomleft"});
north.onAdd = function(map) {
  var div = L.DomUtil.create("div", "info legend");
  div.innerHTML = '<img src="<?=base_url()?>assets/arah-mata-angin.png"style=width:75px;>';
  return div; }
  north.addTo(map);
// Add Marker and GeoJSON Layer
  $.getJSON("<?=base_url()?>assets/provinsi.geojson",function(data){
    var ratIcon = L.icon({
      iconUrl: '<?=base_url()?>assets/Marker-1.png',
      iconSize: [12,10]
    });
    L.geoJson(data,{
      pointToLayer: function(feature,latlng){
        var marker = L.marker(latlng,{icon: ratIcon});
        marker.bindPopup(feature.properties.CITY_NAME);
        return marker;
      }
      }).addTo(prov);
  });

// Add Legend
const legend = L.control.Legend({
  position: "bottomright",
  title: "Keterangan",
  collapsed: true,
  symbolWidth: 24,
  opacity: 1,
  column: 1,
  legends: [{label: "Ibu Kota Provinsi",
             type: "image",
             url: "<?=base_url()?>/assets/Marker-1.png",},
            {label: "Jaringan Sungai",
             type: "polyline",
             color: "#f2051d",
             fillColor: "#f2051d",
             weight: 2},
            {title: "Jaringan Sungai"},
            {label: "Polygon Provinsi",
            font: 29,
            type: "polygon",
            sides: 4,
            color: "#FF0000",
            fillColor: "#FF0000",
            weight: 2}]
  })
  .addTo(map);

  // Add Faskes geoJson
  // Add rsu
  $.getJSON("<?=base_url()?>assets/rsu.geojson",function(data){
    var ratIcon = L.icon({
      iconUrl: '<?=base_url()?>assets/Marker-3.png',
      iconSize: [12,10]
      });
    L.geoJson(data,{
      pointToLayer: function(feature,latlng){
      var marker = L.marker(latlng,{icon: ratIcon});
      marker.bindPopup(feature.properties.NAMOBJ);
      return marker; }
      }).addTo(faskes);
    });

  // add puskesmas
  $.getJSON("<?=base_url()?>assets/poliklinik.geojson",function(data){
    var ratIcon = L.icon({
    iconUrl: '<?=base_url()?>assets/Marker-4.png',
    iconSize: [12,10]
    });
  L.geoJson(data,{
    pointToLayer: function(feature,latlng){
    var marker = L.marker(latlng,{icon: ratIcon});
    marker.bindPopup(feature.properties.NAMOBJ);
    return marker;}
    }).addTo(faskes);
  });

  // add puskesmas
  $.getJSON("<?=base_url()?>assets/puskesmas.geojson",function(data){
    var ratIcon = L.icon({
      iconUrl: '<?=base_url()?>assets/Marker-5.png',
      iconSize: [12,10]
    });
    L.geoJson(data,{
      pointToLayer: function(feature,latlng){
      var marker = L.marker(latlng,{icon: ratIcon});
      marker.bindPopup(feature.properties.NAMOBJ);
      return marker;
    }
    }).addTo(faskes);
  });

  // add driver
  $.getJSON("<?=base_url()?>/assets/sungai.geojson",function(kode){
    L.geoJson( kode, {
      style: function(feature){
        var color,
        kode = feature.properties.kode;
        if ( kode < 2 ) color = "#f2051d";
        else if ( kode > 0 ) color = "#f2051d";
        else color = "#f2051d"; // no data
        return { color: "#999", weight: 5, color: color, fillOpacity: .8 };
      },
      onEachFeature: function( feature, layer ){
        layer.bindPopup
        ()
      }}).addTo(sungai);
    });

  //add poligon Provinsi
  $.getJSON("<?=base_url()?>/assets/kab_berau.geojson",function(kode){
    L.geoJson( kode, {
      style: function(feature){
        var fillColor,
        kode = feature.properties.OBJECTID;
        if ( kode > 21 ) fillColor = "#006837";
        else if (kode>20) fillColor="#fec44f"
        else if (kode>19) fillColor="#c2e699"
        else if (kode>18) fillColor="#fee0d2"
        else if (kode>17) fillColor="#756bb1"
        else if (kode>16) fillColor="#8c510a"
        else if (kode>15) fillColor="#01665e"
        else if (kode>14) fillColor="#e41a1c"
        else if (kode>13) fillColor="#636363"
        else if (kode>12) fillColor= "#762a83"
        else if (kode>11) fillColor="#1b7837"
        else if (kode>10) fillColor="#d53e4f"
        else if (kode>9) fillColor="#67001f"
        else if (kode>8) fillColor="#c994c7"
        else if (kode>7) fillColor="#fdbb84"
        else if (kode>6) fillColor="#dd1c77"
        else if (kode>5) fillColor="#3182bd"
        else if ( kode > 4 ) fillColor ="#f03b20"
        else if ( kode > 3 ) fillColor = "#31a354";
        else if ( kode > 2 ) fillColor = "#78c679";
        else if ( kode > 1 ) fillColor = "#c2e699";
        else if ( kode > 0 ) fillColor = "#ffffcc";
        else fillColor = "#f7f7f7"; // no data
        return { color: "#999", weight: 1, fillColor: fillColor, fillOpacity: .6 };
      },
      onEachFeature: function( feature, layer ){
        layer.bindPopup(feature.properties.NAMOBJ, '1')
      }
    }).addTo(provin);
  });
</script>
