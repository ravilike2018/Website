<!DOCTYPE html>

<html>
  <head>
  <meta charset="utf-8">
  <meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no">
  <title>Create a FeatureLayer with client side graphics - 4.3</title>
  <style>
html, body, #viewDiv {
	padding: 0;
	margin: 0;
	height: 100vh;
	width: 100vw;
}
#infoDiv {
	position: absolute;
	bottom: 15px;
	right: 0;
	max-height: 80%;
	max-width: 300px;
	background-color: black;
	padding: 8px;
	border-top-left-radius: 5px;
	color: white;
	opacity: 0.8;
}
.esri-view-width-xlarge .esri-popup__main-container, .esri-view-width-large .esri-popup__main-container, .esri-view-width-medium .esri-popup__main-container, .esri-view-width-small .esri-popup__main-container {
    	width: 90vw !important;
	}
	.esri-view-height-less-than-medium .esri-popup__main-container, .esri-popup__main-container {
    	max-height: none !important;
		height: 80vh !important;
	}
	.esri-popup-renderer__content-element {
		max-height: none !important;
		height: calc(80vh - 100px) !important;
	}
</style>
  <link rel="stylesheet" href="https://js.arcgis.com/4.3/esri/css/main.css">
  <script src="https://js.arcgis.com/4.3/"></script>
  <script>

    require([
      "esri/views/MapView",
      "esri/Map",
      "esri/layers/FeatureLayer",
      "esri/widgets/Search",
      "esri/layers/support/Field",
      "esri/geometry/Point",
      "esri/renderers/SimpleRenderer",
      "esri/symbols/SimpleMarkerSymbol",
	  "esri/symbols/PictureMarkerSymbol",
      "esri/widgets/Legend",
      "esri/request",
      "dojo/_base/array",
	  "dojo/dom",
      "dojo/on",
      "dojo/domReady!"
	  
    ], function(MapView, Map, FeatureLayer, Search, Field, Point,
      SimpleRenderer, SimpleMarkerSymbol, PictureMarkerSymbol, Legend, esriRequest,
      arrayUtils, dom, on
    ) {
      var lyr, legend;

      /**************************************************
       * Define the specification for each field to create
       * in the layer
       **************************************************/
      var fields = [
      {
        name: "ID",
        alias: "ID",
        type: "oid"
      }, {
        name: "name",
        alias: "name",
        type: "string"
      }, {
        name: "date",
        alias: "date",
        type: "date"
      }, {
        name: "city",
        alias: "city",
        type: "string"
      }, {
        name: "type",
        alias: "type",
        type: "string"
      }, {
        name: "free",
        alias: "free",
        type: "string"
      }, {
        name: "open24",
        alias: "open24",
        type: "string"
      }];

      // Set up popup template for the layer
      var pTemplate = {
        title: "{name}",
        content: "<iframe style='width:100%; height:100%; border:none;' src='getPhotos.php?intID={ID}'></iframe>"
      };

      /**************************************************
       * Create the map and view
       **************************************************/

      var map = new Map({
        basemap: "topo"
      });

      // Create MapView
      var view = new MapView({
        container: "viewDiv",
        map: map,
        center: [-92.033333, 30.216667],
        zoom: 12,

        // customize ui padding for legend placement
        ui: {
          padding: {
            bottom: 15,
            right: 0
          }
        }
      });
	  
      /**************************************************
       * Define the renderer for landmarks
       **************************************************/
	   //create the Renderer
		var landmarkRenderer = new SimpleRenderer({
		  symbol: new PictureMarkerSymbol({
			  url: "http://codefest.vincentcanale.com/Landmarker.png",
			  width: "20px",
			  height: "35px"
			})
		});

      view.then(function() {
        // Request the data from the database
        getData()
          .then(createGraphics) // then send it to the createGraphics() method
          .then(createLayer) // when graphics are created, create the layer
//          .then(createLegend) // when layer is created, create the legend
          .otherwise(errback);
      });

      // Request the data
      function getData() {
        var url = "query.php";
        return esriRequest(url, {
          responseType: "json"
        });
      }
	  
      /**************************************************
       * Create graphics with returned geojson data
       **************************************************/
	   
      function createGraphics(response) {
        // raw GeoJSON data
        var geoJson = response.data;
        // Create an array of Graphics from each GeoJSON feature
        return arrayUtils.map(geoJson.features, function(feature, i) {
          return {
            geometry: new Point({
              x: feature.geometry.coordinates[0],
              y: feature.geometry.coordinates[1]
            }),
            // select only the attributes you care about
            attributes: {
              ID: feature.properties.ID,
              name: feature.properties.NameOnTheRegister,
              date: feature.properties.Date_listed,
              city: feature.properties.City,
              type: feature.properties.Type,
              free: feature.properties.Free,
              open24: feature.properties.AlwaysOpen
            }
          };
        });
      }

      /**************************************************
       * Create a FeatureLayer with the array of graphics
       **************************************************/

      function createLayer(graphics) {
        lyr = new FeatureLayer({
          source: graphics, // autocast as an array of esri/Graphic
          // create an instance of esri/layers/support/Field for each field object
          fields: fields, // This is required when creating a layer from Graphics
          objectIdField: "ID", // This must be defined when creating a layer from Graphics
          renderer: landmarkRenderer, // set the visualization on the layer
          spatialReference: {
            wkid: 4326
          },
          geometryType: "point", // Must be set when creating a layer from Graphics
          popupTemplate: pTemplate
        });
        map.add(lyr);
        return lyr;
      }
	  
	   /******************************************************************
       * Add Park Boundaries layer
       ******************************************************************/
	  
	  // Create the PopupTemplate
        var parkPopup = {
          title: "{NAME}",
          content: "<iframe style='width:100%; height:100%; border:none;' src='getPhotos.php?intID={OBJECTID }'></iframe>"
        };
		
		// Lafayette Park Boundaries 
        var ParkBoundaries = new FeatureLayer({
          url: "https://services.arcgis.com/fOr4AY8t0ujnJsua/arcgis/rest/services/ParkBoundaries/FeatureServer/",
		  layerId: 0,
		  outFields: ["*"],
		  opacity:.5,
		  popupTemplate: parkPopup
        });
        map.add(ParkBoundaries);
		
	   /******************************************************************
       * Add Art layer
       ******************************************************************/
	  
	  // Create the PopupTemplate
        var artPopup = {
          title: "{Venue}",
          content: "<iframe style='width:100%; height:100%; border:none;' src='getPhotos.php?intID={FID}'></iframe>"
        };
		
		//create the Renderer
		var artRenderer = new SimpleRenderer({
		  symbol: new PictureMarkerSymbol({
			  url: "http://codefest.vincentcanale.com/brush.png",
			  width: "18px",
			  height: "35px"
			})
		});
		
		// Lafayette Park Boundaries 
        var art = new FeatureLayer({
          url: "https://services.arcgis.com/xQcS4egPbZO43gZi/arcgis/rest/services/Lafayette_Public_Art/FeatureServer/",
		  layerId: 0,
		  outFields: ["*"],
		  popupTemplate: artPopup,
  		  renderer: artRenderer
        });
        map.add(art);
		
		/******************************************************************
       * Add Park Boundaries layer
       ******************************************************************/
	  
	  // Create the PopupTemplate
        var firePopup = {
          title: "{BLDG_NAME}",
          content: "<iframe style='width:100%; height:100%; border:none;' src='getPhotos.php?intID={OBJECTID}'></iframe>"
        };
		//create the Renderer
		var fireRenderer = new SimpleRenderer({
		  symbol: new PictureMarkerSymbol({
			  url: "http://codefest.vincentcanale.com/fire.png",
			  width: "20px",
			  height: "30px"
			})
		});
		
		// Lafayette Fire Stations
        var fireStations = new FeatureLayer({
          url: "https://services.arcgis.com/fOr4AY8t0ujnJsua/ArcGIS/rest/services/FireStations/FeatureServer/",
		  layerId: 0,
		  outFields: ["*"],
		  popupTemplate: firePopup,
  		  renderer: fireRenderer
        });
        map.add(fireStations);

      /******************************************************************
       * Add layer to layerInfos in the legend
       ******************************************************************/

      function createLegend(layer) {
        // if the legend already exists, then update it with the new layer
        if (legend) {
          legend.layerInfos = [{
            layer: layer,
            title: "Magnitude"
          }];
        } else {
          legend = new Legend({
            view: view,
            layerInfos: [
            {
              layer: layer,
              title: "Earthquake"
            }]
          }, "infoDiv");
        }
      }
	  
      // Executes if data retrieval was unsuccessful.
      function errback(error) {
        console.error("Creating legend failed. ", error);
      }
	  
	  
	   /******************************************************************
       * Add search to map
       ******************************************************************/ 
	  
	  var searchWidget = new Search({

        view: view,

        allPlaceholder: "Search landmark names",

        sources: [{

          featureLayer: lyr,

          searchFields: ["name"],

          displayField: "name",

          exactMatch: false,

          outFields: ["name"],

          name: "Landmarks",

          placeholder: "example: Cecil J. Picard Center",

        }, {

          featureLayer: fireStations,

          searchFields: ["BLDG_NAME"],

          displayField: "BLDG_NAME",

          exactMatch: false,

          outFields: ["BLDG_NAME"],

          name: "FireStations",

          placeholder: "example: SCOTT FIRE STATION #2",

        },{

          featureLayer: art,

          searchFields: ["Venue"],

          displayField: "Venue",

          exactMatch: false,

          outFields: ["Venue"],

          name: "Public Art",

          placeholder: "example: Souvenir Heights Neighborhood Mural",

        }, {

          featureLayer: ParkBoundaries,

          searchFields: ["NAME"],

          displayField: "NAME",

          exactMatch: false,

          outFields: ["NAME"],

          name: "City Parks",

          placeholder: "example: GIRARD PARK",

        }]

      });



      // Add the search widget to the top left corner of the view

      view.ui.add(searchWidget, {

        position: "top-right"

      });
    });

  </script>
  </head>

  <body>
<div id="viewDiv"></div>
</body>
</html>