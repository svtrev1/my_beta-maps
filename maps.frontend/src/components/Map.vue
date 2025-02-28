<template>
    <div id="map" class="map"></div>
  </template>
  
  <script>
  import 'ol/ol.css';
  import { Map, View } from 'ol';
  import { Tile as TileLayer } from 'ol/layer';
  import { OSM } from 'ol/source';
  
  export default {
    name: 'Map',
    mounted() {
      this.initializeMap();
    },
    methods: {
      initializeMap() {
        new Map({
          target: 'map',
          layers: [
            new TileLayer({
              source: new OSM(),
            }),
          ],
          view: new View({
            center: [0, 0], // Координаты центра карты (например, 0, 0 для начала)
            zoom: 4, // Уровень масштабирования
          }),
        });
      },
    },
  };
  </script>
  
  <style scoped>
  .map {
    width: 100%; height: 100%; position: relative;
  }
  </style>
  