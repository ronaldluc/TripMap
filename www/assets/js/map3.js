/**
 * Created by norik on 16.2.16.
 */
var raster = new ol.layer.Tile({
    source: new ol.source.OSM()
});

var vector = new ol.layer.Vector({
    source: new ol.source.Vector({})
});

var select = new ol.interaction.Select({
    wrapX: false
});

var modify = new ol.interaction.Modify({
    features: select.getFeatures()
});

var map = new ol.Map({
    interactions: ol.interaction.defaults().extend([select, modify]),
    layers: [raster, vector],
    target: 'map',
    view: new ol.View({
        center: [1849078.596618163,6308254.135275547],
        zoom: 13
    })
});


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
    vector.addFeature(feature);

};