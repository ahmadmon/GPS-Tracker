//Init Overlays
let overlayMaps = {};

let OSMBase = L.tileLayer("http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    maxZoom: 20,
    attribution:
        '&copy; <a href="http://openstreetmap.org/copyright">OpenStreetMap</a>',
});
let OpenStreetMap_HOT = L.tileLayer(
    "https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png",
    {
        maxZoom: 19,
        attribution:
            '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>, <a href="http://hot.openstreetmap.org/" target="_blank">Humanitarian OSM Team</a>',
    }
);
let OpenTopoMap = L.tileLayer(
    "https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png",
    {
        maxZoom: 17,
        attribution:
            'Map data: &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, <a href="http://viewfinderpanoramas.org">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)',
    }
);
let CyclOSM = L.tileLayer(
    "https://dev.{s}.tile.openstreetmap.fr/cyclosm/{z}/{x}/{y}.png",
    {
        maxZoom: 20,
        attribution:
            '<a href="https://github.com/cyclosm/cyclosm-cartocss-style/releases" title="CyclOSM - Open Bicycle render">CyclOSM</a> | Map data: &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    }
);

let googleStreets = L.tileLayer(
    "http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}",
    {
        maxZoom: 22,
        subdomains: ["mt0", "mt1", "mt2", "mt3", "www"],
        attribution: "google street view",
    }
);
let googleHybrid = L.tileLayer(
    "http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}",
    {
        maxZoom: 22,
        subdomains: ["mt0", "mt1", "mt2", "mt3", "www"],
        attribution: "google hybrid view",
    }
);
let GoogleSat = L.tileLayer(
    "http://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}",
    {
        maxZoom: 22,
        subdomains: ["mt0", "mt1", "mt2", "mt3", "www"],
        attribution: "google satelite view",
    }
);

let Stadia_AlidadeSmooth = L.tileLayer(
    "https://tiles.stadiamaps.com/tiles/alidade_smooth/{z}/{x}/{y}{r}.png",
    {
        maxZoom: 20,
        attribution:
            '&copy; <a href="https://stadiamaps.com/">Stadia Maps</a>, &copy; <a href="https://openmaptiles.org/">OpenMapTiles</a> &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors',
    }
);
let Stadia_AlidadeSmoothDark = L.tileLayer(
    "https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.png",
    {
        maxZoom: 20,
        attribution:
            '&copy; <a href="https://stadiamaps.com/">Stadia Maps</a>, &copy; <a href="https://openmaptiles.org/">OpenMapTiles</a> &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors',
    }
);
let Stadia_OSMBright = L.tileLayer(
    "https://tiles.stadiamaps.com/tiles/osm_bright/{z}/{x}/{y}{r}.png",
    {
        maxZoom: 20,
        attribution:
            '&copy; <a href="https://stadiamaps.com/">Stadia Maps</a>, &copy; <a href="https://openmaptiles.org/">OpenMapTiles</a> &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors',
    }
);
let Stadia_Outdoors = L.tileLayer(
    "https://tiles.stadiamaps.com/tiles/outdoors/{z}/{x}/{y}{r}.png",
    {
        maxZoom: 20,
        attribution:
            '&copy; <a href="https://stadiamaps.com/">Stadia Maps</a>, &copy; <a href="https://openmaptiles.org/">OpenMapTiles</a> &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors',
    }
);

let baseMaps = {
    "نقشه پیشفرض": OSMBase,
    "OSM Terrain": OpenTopoMap,
    "OSM HOT": OpenStreetMap_HOT,
    "OSM CyclOSM": CyclOSM,

    "Google Street": googleStreets,
    "Google Hybrid": googleHybrid,
    "Google SAT": GoogleSat,

    // "Stadia Light Gray": Stadia_AlidadeSmooth,
    // "Stadia Dark Gray": Stadia_AlidadeSmoothDark,
    // "Stadia OSM Bright": Stadia_OSMBright,
    // "Stadia Outdoors": Stadia_Outdoors,
};
