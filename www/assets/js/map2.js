var raster = new ol.layer.Tile({
    source: new ol.source.OSM()
});

var map = new ol.Map({
    layers: [raster],
    target: 'map',
    view: new ol.View({
        center: [1849078.596618163,6308254.135275547],
        zoom: 13
    })
});

var features = new ol.Collection();
var vectorSource = new ol.source.Vector({features: features});
var featureOverlay = new ol.layer.Vector({
    source: vectorSource,
    style: new ol.style.Style({
        fill: new ol.style.Fill({
            color: 'rgba(0, 0, 0, 0.4)'
        }),
        stroke: new ol.style.Stroke({
            color: 'rgba(0, 0, 0, 1',
            width: 2
        }),
        image: new ol.style.Circle({
            radius: 7,
            fill: new ol.style.Fill({
                color: 'rgba(0, 0, 0, 1'
            })
        })
    })
});
featureOverlay.setMap(map);

var modify = new ol.interaction.Modify({
    features: features,
    source: vectorSource,
    // the SHIFT key must be pressed to delete vertices, so
    // that new vertices can be drawn at the same position
    // of existing vertices
    deleteCondition: function(event) {
        return ol.events.condition.shiftKeyOnly(event) &&
            ol.events.condition.singleClick(event);
    }
});
map.addInteraction(modify);

var draw; // global so we can remove it later
var typeSelect = 'polygon';

function addInteraction() {
    draw = new ol.interaction.Draw({
        features: features,
        source: vectorSource,
        type: /** @type {ol.geom.GeometryType} */ 'Polygon'
    });
    map.addInteraction(draw);
}


/**
 * Handle change event.
 */


addInteraction();


/**
 * Loads trips from DB into map vectorLayer
 */
function loadTrips(text) {
    var trip = JSON.parse(text);
    var polygon = new ol.geom.Polygon([trip]);
    console.log(polygon);

    // Create feature with polygon.
    var feature = new ol.Feature(polygon);

    // Create vector source and the feature to it.
    vectorSource.addFeature(feature);

};


/**
 * Search engine
 */
//var geocoder = new Geocoder('nominatim', {
//    provider: 'osm',
//    lang: 'cz-CZ', //en-US, fr-FR
//    placeholder: 'Vyhledat ...',
//    limit: 5,
//    keepOpen: true
//});
//map.addControl(geocoder);
//
//geocoder.on('addresschosen', function(evt){
//    var
//        feature = evt.feature,
//        coord = evt.coordinate,
//        address_html = feature.get('address_html')
//        ;
//    //content.innerHTML = '<p>'+address_html+'</p>';
//    //view.setPosition(coord);
//});
