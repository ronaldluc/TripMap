var wgs84Sphere = new ol.Sphere(6378137);

var raster = new ol.layer.Tile({
    source: new ol.source.OSM()
});


var features = new ol.Collection;
var vectorSource = new ol.source.Vector({features: features});

var vectorLayer = new ol.layer.Vector({
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


/**
 * Currently drawn feature.
 * @type { ol.Feature }
 */
var sketch;


/**
 * The help tooltip element.
 * @type { Element }
 */
var helpTooltipElement;


/**
 * Overlay to show the help messages.
 * @type { ol.Overlay }
 */
var helpTooltip;


/**
 * The measure tooltip element.
 * @type {  Element }
 */
var measureTooltipElement;


/**
 * Overlay to show the measurement.
 * @type {  ol.Overlay }
 */
var measureTooltip;


/**
 * Message to show when the user is drawing a polygon.
 * @type {  string }
 */
var continuePolygonMsg = 'Klikněte pro dokončení výletu';


/**
 * Message to show when the user is drawing a line.
 * @type {  string}
 */
var continueLineMsg = 'Click to continue drawing the line';


/**
 * Handle pointer move.
 * @param { ol.MapBrowserEvent} evt
 */
var pointerMoveHandler = function(evt) {
    if (evt.dragging) {
        return;
    }
    /** @type { string} */
    var helpMsg = 'Klikněte pro vytvoření nového výletu';

    if (sketch) {
        var geom = (sketch.getGeometry());
        if (geom instanceof ol.geom.Polygon) {
            helpMsg = continuePolygonMsg;
        } else if (geom instanceof ol.geom.LineString) {
            helpMsg = continueLineMsg;
        }
    }

    helpTooltipElement.innerHTML = helpMsg;
    helpTooltip.setPosition(evt.coordinate);

    $(helpTooltipElement).removeClass('hidden');
};


var map = new ol.Map({
    layers: [raster, vectorLayer],
    target: 'map',
    view: new ol.View({
        center: [1849078.596618163,6308254.135275547],
        zoom: 13
    })
});

// select interaction working on "click"
var selectClick = new ol.interaction.Select({
    condition: ol.events.condition.click
});

// select interaction working on "pointermove"
var selectPointerMove = new ol.interaction.Select({
    condition: ol.events.condition.pointerMove
});

var select = selectClick;

map.addInteraction(select);
select.on('select', function(e) {
    $('#status').html('&nbsp;' + e.target.getFeatures().getLength() +
        ' selected features (last operation selected ' + e.selected.length +
        ' and deselected ' + e.deselected.length + ' features)');
});


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


addInteraction();

map.on('pointermove', pointerMoveHandler);

$(map.getViewport()).on('mouseout', function() {
    $(helpTooltipElement).addClass('hidden');
});

var draw; // global so we can remove it later
function addInteraction() {
    var type = 'Polygon';
    console.log(type);
    draw = new ol.interaction.Draw({
        source: vectorSource,
        type: /** @type { ol.geom.GeometryType} */ (type),
        style: new ol.style.Style({
            fill: new ol.style.Fill({
                color: 'rgba(0, 0, 0, 0.2)'
            }),
            stroke: new ol.style.Stroke({
                color: 'rgba(0, 0, 0, 0.5)',
                lineDash: [10, 10],
                width: 2
            }),
            image: new ol.style.Circle({
                radius: 5,
                stroke: new ol.style.Stroke({
                    color: 'rgba(0, 0, 0, 0.7)'
                }),
                fill: new ol.style.Fill({
                    color: 'rgba(0, 0, 0, 0.8)'
                })
            })
        })
    });
    map.addInteraction(draw);

    createMeasureTooltip();
    createHelpTooltip();

    var listener;
    draw.on('drawstart',
        function(evt) {
            // set sketch
            sketch = evt.feature;

            /** @type { ol.Coordinate|undefined} */
            var tooltipCoord = evt.coordinate;

            listener = sketch.getGeometry().on('change', function(evt) {
                var geom = evt.target;
                var output;
                if (geom instanceof ol.geom.Polygon) {
                    output = formatArea(/** @type { ol.geom.Polygon} */ (geom));
                    tooltipCoord = geom.getInteriorPoint().getCoordinates();
                } else if (geom instanceof ol.geom.LineString) {
                    output = formatLength( /** @type { ol.geom.LineString} */ (geom));
                    tooltipCoord = geom.getLastCoordinate();
                }
                measureTooltipElement.innerHTML = output;
                measureTooltip.setPosition(tooltipCoord);
            });
        }, this);

    draw.on('drawend',
        function(evt) {
            measureTooltipElement.className = 'tooltip tooltip-static';
            measureTooltip.setOffset([0, -7]);
            // unset sketch
            sketch = null;
            // unset tooltip so that a new one can be created
            measureTooltipElement = null;
            createMeasureTooltip();
            ol.Observable.unByKey(listener);
            // get polygon coords
            var geom = evt.target;
            console.log(evt);
            console.log(JSON.stringify(geom.S));
            newTrip(JSON.stringify(geom.S));
        }, this);
}

/**
 * Creates a new help tooltip
 */
function createHelpTooltip() {
    if (helpTooltipElement) {
        helpTooltipElement.parentNode.removeChild(helpTooltipElement);
    }
    helpTooltipElement = document.createElement('div');
    helpTooltipElement.className = 'tooltip hidden';
    helpTooltip = new ol.Overlay({
        element: helpTooltipElement,
        offset: [15, 0],
        positioning: 'center-left'
    });
    map.addOverlay(helpTooltip);
}


/**
 * Creates a new measure tooltip
 */
function createMeasureTooltip() {
    if (measureTooltipElement) {
        measureTooltipElement.parentNode.removeChild(measureTooltipElement);
    }
    measureTooltipElement = document.createElement('div');
    measureTooltipElement.className = 'tooltip tooltip-measure';
    measureTooltip = new ol.Overlay({
        element: measureTooltipElement,
        offset: [0, -15],
        positioning: 'bottom-center'
    });
    map.addOverlay(measureTooltip);
}


/**
 * Let user change the geometry type.
 * @param { Event} e Change event.
 */
//typeSelect.onchange = function(e) {
//    map.removeInteraction(draw);
//    addInteraction();
//};


/**
 * format length output
 * @param { ol.geom.LineString} line
 * @return { string}
 */
var formatLength = function(line) {
    var length;
    var coordinates = line.getCoordinates();
    length = 0;
    var sourceProj = map.getView().getProjection();
    for (var i = 0, ii = coordinates.length - 1; i < ii; ++i) {
        var c1 = ol.proj.transform(coordinates[i], sourceProj, 'EPSG:4326');
        var c2 = ol.proj.transform(coordinates[i + 1], sourceProj, 'EPSG:4326');
        length += wgs84Sphere.haversineDistance(c1, c2);
    }
    var output;
    if (length > 100) {
        output = (Math.round(length / 1000 * 100) / 100) +
            ' ' + 'km';
    } else {
        output = (Math.round(length * 100) / 100) +
            ' ' + 'm';
    }
    return output;
};


/**
 * format length output
 * @param { ol.geom.Polygon} polygon
 * @return { string}
 */
var coordinates;
var formatArea = function(polygon) {
    var area;
    var sourceProj = map.getView().getProjection();
    var geom = /** @type { ol.geom.Polygon} */(polygon.clone().transform(
        sourceProj, 'EPSG:4326'));
    coordinates = geom.getLinearRing(0).getCoordinates();
    area = Math.abs(wgs84Sphere.geodesicArea(coordinates));
    var output;
    output = (Math.round(area / 1000000 * 100) / 100)

    return output;
};


addInteraction();




/**
 * Loads trips from DB into map vectorLayer
 */
function loadTrip(text, id) {
    var trip = JSON.parse(text);
    var polygon = new ol.geom.Polygon([trip]);
    console.log(polygon);

    // Create feature with polygon.
    var feature = new ol.Feature(polygon);
    polygon.on('change', function(e) {
        //changeTrip(JSON.stringify(e.g.B.geometry.A), id)
        console.log(e);
    });

     //Create vector source and the feature to it. function(e){console.log(e.g.B.geometry.A);console.log(id)}
    features.push(feature);


};


/**
 * Search engine
 */
var geocoder = new Geocoder('nominatim', {
    provider: 'osm',
    lang: 'cz-CZ', //en-US, fr-FR
    placeholder: 'Vyhledat ...',
    limit: 5,
    keepOpen: true
});
map.addControl(geocoder);

geocoder.on('addresschosen', function(evt){
    var
        feature = evt.feature,
        coord = evt.coordinate,
        address_html = feature.get('address_html')
        ;
    overlay.setPosition(coord);
});