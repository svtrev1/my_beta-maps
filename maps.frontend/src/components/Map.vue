<template>
  <div id="map" style="height: 100%; width: 100%"></div>
</template>

<script>
import * as ol from 'ol';
import 'ol/ol.css';
import OSM from 'ol/source/OSM';
import TileLayer from 'ol/layer/Tile';
import VectorLayer from 'ol/layer/Vector';
import VectorSource from 'ol/source/Vector';
import { Circle as CircleStyle, Fill, Stroke, Style } from 'ol/style';
import { Point } from 'ol/geom';
import { fromLonLat } from 'ol/proj';
import { GeoJSON } from 'ol/format';

export default {
  name: 'MapComponent',
  mounted() {
    this.initMap();
  },
  methods: {
    initMap() {
      // Настроим карту
      const map = new ol.Map({
        target: 'map',
        layers: [
          new TileLayer({
            source: new OSM(),
          }),
        ],
        view: new ol.View({
          center: fromLonLat([37.6173, 55.7558]), // Москва, можно заменить на нужные координаты
          zoom: 12,
        }),
      });

      // Получаем точки из GeoServer (GeoJSON)
      const vectorSource = new VectorSource({
        format: new GeoJSON(),
        url: 'http://betamaps.admsurgut.ru/geoserver/ne/ows?service=WFS&version=1.0.0&request=GetFeature&typeName=ne:busstation_4326&outputFormat=application/json',
             });

      // Настроим стиль для точек
      const style = new Style({
        image: new CircleStyle({
          radius: 5,
          fill: new Fill({ color: 'red' }),
          stroke: new Stroke({
            color: 'white',
            width: 2,
          }),
        }),
      });

      // Добавляем слой с точками на карту
      const vectorLayer = new VectorLayer({
        source: vectorSource,
        style: style,
      });

      map.addLayer(vectorLayer);
    },
  },
};
</script>

<style scoped>
#map {
  width: 100%;
  height: 100%;
}
</style>
