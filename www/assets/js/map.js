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


///**
// * Elements that make up the popup.
// */
//var container = document.getElementById('popup');
//var content = document.getElementById('popup-content');
//var closer = document.getElementById('popup-closer');
//
///**
// * Add a click handler to hide the popup.
// * @return {boolean} Don't follow the href.
// */
//closer.onclick = function() {
//    overlay.setPosition(undefined);
//    closer.blur();
//    return false;
//};
//
///**
// * Create an overlay to anchor the popup to the map.
// */
//var overlay = new ol.Overlay(/** @type {olx.OverlayOptions} */ ({
//    element: container,
//    autoPan: true,
//    autoPanAnimation: {
//        duration: 250
//    }
//}));

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
var continuePolygonMsg = 'Dvojklikem dokončíte výlet';


/**
 * Message to show when the user is drawing a line.
 * @type {  string}
 */
var continueLineMsg = 'Click to continue drawing the line';


/** @type { string} */
var helpMsg = 'Klikněte pro vytvoření nového výletu';


/**
 * Handle pointer move.
 * @param { ol.MapBrowserEvent} evt
 */
var pointerMoveHandler = function(evt) {
    if (evt.dragging) {
        return;
    }

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

/**
 * Draw / select switch
 */

window.app = {};
var app = window.app;

/**
 * @constructor
 * @extends {ol.control.Control}
 * @param {Object=} opt_options Control options.
 */
app.interSwitchControl = function(opt_options) {

    var options = opt_options || {};

    var button = document.createElement('button');
    button.innerHTML = '<i class="fa fa-hand-pointer-o"></i>';
    button.className = 'button-switch';

    var this_ = this;
    var handleInterSwitch = function(e) {
        swapInteraction();
    };

    button.addEventListener('click', handleInterSwitch, false);
    button.addEventListener('touchstart', handleInterSwitch, false);

    var element = document.createElement('div');
    element.className = 'control-switch ol-unselectable ol-control';
    element.appendChild(button);

    ol.control.Control.call(this, {
        element: element,
        target: options.target
    });

};
ol.inherits(app.interSwitchControl, ol.control.Control);



var map = new ol.Map({
    controls: ol.control.defaults({
        attributionOptions: /** @type {olx.control.AttributionOptions} */ ({
            collapsible: false
        })
    }).extend([
        new app.interSwitchControl()
    ]),
    layers: [raster, vectorLayer],
    //overlays: [overlay],
    target: 'map',
    view: new ol.View({
        center: [1738178.0232049078,6367357.330211753],
        zoom: 9
    }),
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

//map.removeInteraction(ol.interaction.DragRotate);

// select interaction working on "click"
var selectClick = new ol.interaction.Select({
    condition: ol.events.condition.singleClick
});

// select interaction working on "pointermove"
var selectPointerMove = new ol.interaction.Select({
    condition: ol.events.condition.pointerMove
});

var selectShiftClick = new ol.interaction.Select({
    condition: function(mapBrowserEvent) {
        return ol.events.condition.altKeyOnly(mapBrowserEvent) && ol.events.condition.click(mapBrowserEvent);
    }
});

var select = selectClick;

select.on('select', function(e) {
    if (e.selected[0]) {
        var chosen = e.selected[0];

        var id = chosen.getId();
        var coordinates = chosen.getGeometry().getInteriorPoint().getCoordinates();

        var popup = new ol.Overlay({
            element: document.getElementById('popup')
        });
        map.addOverlay(popup);
        var data = chosen.get('data');

        var element = popup.getElement();

        $(element).popover('destroy');
        popup.setPosition(coordinates);
        // the keys are quoted to prevent renaming in ADVANCED mode.
        $(element).popover({
            'placement': 'top',
            'animation': false,
            'html': true,
            //'content': ''+data['date']+' '+data['duration']+'  '+data['length']+' ',
            'content': '<table class="table-map"><tr><td class="left"> ' + data['date'] + ' </td><td> '
            + humanizeDuration(data['duration']) + ' </td><td> ' + humanizeLength(data['length']) + ' </td></tr></table>',
            'title': '<strong><table class="table-map-head"><tr><td class="">' +wholeTruncate(data['name']) + '</td>' +
            '<td class="icon"><div onclick="editTrip(' + id + ')" class="link link-wrapper"><i class="fa fa-lg fa-pencil"></i></div></td>' +
            '<td class="icon"><div onclick="deleteTrip(' + id + ')" class="link link-wrapper right"><i class="fa fa-lg fa-trash-o"></i></div></td></tr></table></strong>'
        });

        $.get('http://localhost/tripMap/www/map/test', function (data) {
            console.log(data);
        });

        $(element).popover('show');
    } else {
        select.getFeatures().clear();
        var popup = new ol.Overlay({
            element: document.getElementById('popup')
        });
        map.addOverlay(popup);
        var element = popup.getElement();

        $(element).popover('destroy');
    }
});

function editTrip(id) {
    var feature = vectorSource.getFeatureById(id);
    var data = feature.get('data');

    $('#newTripModal').modal('show');
    $('input[name="id"]').val(id);
    $('input[name="name"]').val(data['name']);
    $('textarea[name="text"]').val(data['text']);
    $('input[name="duration"]').val(data['duration']);
    $('input[name="lenght"]').val(data['length']);
    $('input[name="date"]').val(reformatCzEn(data['date']));
    $('select[name="category"]').val(data['categoryId']);
}


function updateEditedTrip() {
    var id = $('input[name="id"]').val();
    var feature = vectorSource.getFeatureById(id);
    var data = feature.get('data');

    data['name'] = $('input[name="name"]').val();
    data['text'] = $('textarea[name="text"]').val();
    data['duration'] = $('input[name="duration"]').val();
    data['length'] = $('input[name="lenght"]').val();
    data['date'] = reformatEnCz($('input[name="date"]').val());
    data['categoryId'] = $('select[name="category"]').val();

    var properties = {data:data};

    feature.setProperties(properties);

    var coordinates = feature.getGeometry().getInteriorPoint().getCoordinates();

    var popup = new ol.Overlay({
        element: document.getElementById('popup')
    });

    map.addOverlay(popup);

    var element = popup.getElement();

    $(element).popover('destroy');
    popup.setPosition(coordinates);
    // the keys are quoted to prevent renaming in ADVANCED mode.
    $(element).popover({
        'placement': 'top',
        'animation': false,
        'html': true,
        //'content': ''+data['date']+' '+data['duration']+'  '+data['length']+' ',
        'content': '<table class="table-map"><tr><td class="left"> '+data['date']+' </td><td> '+humanizeDuration(data['duration'])
        +' </td><td> '+humanizeLength(data['length'])+' </td></tr></table>',
        'title' : '<strong><table class="table-map-head"><tr><td class="">'+wholeTruncate(data['name'])+'</td>' +
        '<td class="icon"><div onclick="editTrip(' + id + ')" class="link link-wrapper"><i class="fa fa-lg fa-pencil"></i></div></td>' +
        '<td class="icon"><div onclick="deleteTrip(' + id + ')" class="link link-wrapper right"><i class="fa fa-lg fa-trash-o"></i></div></td></tr></table></strong>'
    });

    $(element).popover('show');
}

$.get('http://localhost/tripMap/www/map/test', function(data) {
    console.log(data);
});

//var disableDraw = selectPointerMove;
//
//map.addInteraction(disableDraw);
//
//disableDraw.on('select', function (e) {
//    var chosen = e.selected;
//});

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

addInteraction();

map.on('pointermove', pointerMoveHandler);

$(map.getViewport()).on('mouseout', function() {
    $(helpTooltipElement).addClass('hidden');
});

var draw; // global so we can remove it later
function addInteraction() {
    var type = 'Polygon';
    //console.log(type);
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
        //condition: function(mapBrowserEvent) {
        //    return ol.events.condition.altKeyOnly(mapBrowserEvent) && ol.events.condition.click(mapBrowserEvent);
        //}
    });

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
                //if (geom instanceof ol.geom.Polygon) {
                //    output = formatArea(/** @type { ol.geom.Polygon} */ (geom));
                //    tooltipCoord = geom.getInteriorPoint().getCoordinates();
                //} else if (geom instanceof ol.geom.LineString) {
                //    output = formatLength( /** @type { ol.geom.LineString} */ (geom));
                //    tooltipCoord = geom.getLastCoordinate();
                //}
                //measureTooltipElement.innerHTML = output;
                //measureTooltip.setPosition(tooltipCoord);
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
            //console.log(evt);
            //output = formatArea(/** @type { ol.geom.Polygon} */ (geom))
            console.log(geom.sketchCoords_);
            console.log(JSON.stringify(geom.sketchCoords_[0]));
            //newTrip(JSON.stringify(geom.S));
            newTrip(JSON.stringify(geom.sketchCoords_[0]));
            vectorSource.forEachFeature( function(feature) {
               console.log(feature.getGeometry().getCoordinates());
            });
        }, this);
}

$(document).ready(function() {
    console.log('Jsem tu!');
    map.addInteraction(draw);
    map.addInteraction(modify);
});

$(document).keyup(function(e) {
    var key = e.keyCode;
    if (key === 13 || key == 18) {
        console.log(e);
        swapInteraction();
    }
    if (key == 27) {
        var interactions = map.getInteractions();
        var swapControl = document.getElementsByClassName('control-switch');
        interactions.forEach(function(interaction) {
            if (interaction == draw) {
                map.removeInteraction(draw);
                map.addInteraction(draw);
                sketch = null;
                helpMsg = 'Klikněte pro vytvoření nového výletu';
            }
            if (interaction == select) {
                select.getFeatures().clear();
                var popup = new ol.Overlay({
                    element: document.getElementById('popup')
                });
                map.addOverlay(popup);
                var element = popup.getElement();

                $(element).popover('destroy');
            }
        });
    }
});

function swapInteraction () {
    sketch = null;
    var interactions = map.getInteractions();
    var swapControl = document.getElementsByClassName('control-switch');
    interactions.forEach(function(interaction) {
        if (interaction == draw) {
            console.log(interaction);
            map.removeInteraction(draw);
            map.removeInteraction(modify);
            map.addInteraction(select);
            helpMsg = 'Klikni na výlet pro bližší info';
            var button = document.getElementsByClassName('button-switch');
            $('button.button-switch').html('<i class="fa fa-pencil"></i>');
        }
        if (interaction == select) {
            console.log(interaction);
            map.removeInteraction(select);
            map.addInteraction(draw);
            map.addInteraction(modify);
            helpMsg = 'Klikněte pro vytvoření nového výletu';
            $('button.button-switch').html('<i class="fa fa-hand-pointer-o"></i>');
        }
    });
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
function loadTrip(text, id, red, green, blue, data) {
    var trip = JSON.parse(text);
    var polygon = new ol.geom.Polygon([trip]);
    //console.log(polygon);

    // Create feature with polygon.
    var feature = new ol.Feature(polygon);
    polygon.on('change', function(e) {
        changeTrip(JSON.stringify(e.target.l), id);
        //console.log(e.target.l);
    });

    var coordinates = feature.getGeometry().getInteriorPoint().getCoordinates();

    var properties = {data:data};

    feature.setId(id);

    feature.setProperties(properties);

    //console.log(polygon.getProperties());


    feature.setStyle(new ol.style.Style({
        fill: new ol.style.Fill({
            color: 'rgba('+red+','+green+','+blue+', 0.42)'
            //color: 'rgba(0, 0, 0, 0.4)',
        }),
        stroke: new ol.style.Stroke({
            color: 'rgba(0, 0, 0, 1)',
            //color: 'rgba('+red+','+green+','+blue+', 1)',
            width: 2.42
        })
    }));

     //Create vector source and the feature to it. function(e){console.log(e.g.B.geometry.A);console.log(id)}
    features.push(feature);
};

//Controls
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Search engine
 */
var geocoder = new Geocoder('Nominatim', {
    provider: 'osm',
    lang: 'cz-CZ', //en-US, fr-FR
    placeholder: 'Vyhledat ...',
    limit: 5,
    keepOpen: true,
    //debug: true
});
map.addControl(geocoder);

geocoder.on('addresschosen', function(evt){
    //console.log(evt);
    var feature = evt.feature,
        coord = evt.coordinate,
        address_html = feature.get('address_html');
    content.innerHTML = '<p>'+address_html+'</p>';
    overlay.setPosition(coord);
});

$('#newTripModal').on('hidden.bs.modal', function () {
    var feature = features.pop();
    if (feature.getId()) {
        features.push(feature);
    }
})

//Help functions
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////

Date.prototype.ddmmyyyy = function() {
    var yyyy = this.getFullYear().toString();
    var mm = (this.getMonth()+1).toString(); // getMonth() is zero-based
    var dd  = this.getDate().toString();
    //return yyyy + (mm[1]?mm:"0"+mm[0]) + (dd[1]?dd:"0"+dd[0]); // padding
    return dd +'. ' + mm + '. ' + yyyy;
};

//String to number converter
function getValue(string) {
    var idx = string.indexOf(' ');
    return Number(string.substr(0,idx));
}

function reformatCzEn(date) {
    console.log('Reformat Cz -> En');
    console.log(date);
    console.log(date.substr(8,4)+'-'+date.substr(4,2)+'-'+date.substr(0,2));
    return date.substr(8,4)+'-'+date.substr(4,2)+'-'+date.substr(0,2);
}

function reformatEnCz(date) {
    console.log('Reformat En -> Cz');
    console.log(date);
    console.log(date.substr(8,2)+'. '+date.substr(5,2)+'. '+date.substr(0,4));
    return date.substr(8,2)+'. '+date.substr(5,2)+'. '+date.substr(0,4);
}

function humanizeDuration(duration) {
    if (duration > 4) {
        return duration + ' dní';
    } else if (duration == 1) {
        return duration + ' den';
    } else {
        return duration + ' dny';
    }
}

function humanizeLength(length) {
    if (length) {
        return length+' km';
    } else {
        return '';
    }
}

function wholeTruncate(string) {
    console.log(string);
    if (string.length > 27) {
        return string.substr(0,25)+'...';
    } else {
        return string;
    }
}