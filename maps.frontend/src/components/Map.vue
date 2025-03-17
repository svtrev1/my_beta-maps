<template>
  <div id="map-container">
    <div id="map" style="height: 100%; width: 100%"></div>
    <div id="info-panel" :class="{ 'visible': isPanelVisible }">
      <div class="info-content">
        <button @click="closePanel" class="close-btn">×</button>
        <div v-if="selectedFeature" class="info-grid">
          <template v-if="selectedFeature.get('name')">
            <div class="info-label">Наименование остановки:</div>
            <div class="info-value">{{ selectedFeature.get('name') }}</div>
          </template>
          <template v-if="selectedFeature.get('id')">
            <div class="info-label">id:</div>
            <div class="info-value">{{ selectedFeature.get('id') }}</div>
          </template>
          <template v-if="selectedFeature.get('numbus')">
            <div class="info-label">Номера автобусов:</div>
            <div class="info-value">{{ selectedFeature.get('numbus') }}</div>
          </template>
          <template v-if="selectedFeature.get('numtaxi')">
            <div class="info-label">Номера маршрутных автобусов:</div>
            <div class="info-value">{{ selectedFeature.get('numtaxi') }}</div>
          </template>
          <template v-if="selectedFeature.get('street')">
            <div class="info-label">Местоположение остановки:</div>
            <div class="info-value">{{ selectedFeature.get('street') }}</div>
          </template>
          <template v-if="selectedFeature.get('financing')">
            <div class="info-label">Финансирование:</div>
            <div class="info-value">{{ selectedFeature.get('financing') }}</div>
          </template>
          <template v-if="selectedFeature.get('year')">
            <div class="info-label">Год замены:</div>
            <div class="info-value">{{ selectedFeature.get('year') }}</div>
          </template>
          <template v-if="selectedFeature.get('inform_typ')">
            <div class="info-label">Тип информации:</div>
            <div class="info-value">{{ getInformationType(selectedFeature.get('inform_typ')) }}</div>
          </template>
          <template v-if="selectedFeature.get('comfort_ty')">
            <div class="info-label">Тип комфортности:</div>
            <div class="info-value">{{ getComfortType(selectedFeature.get('comfort_ty')) }}</div>
          </template>
          <template v-if="selectedFeature.get('comments')">
            <div class="info-label">Комментарии:</div>
            <div class="info-value">{{ selectedFeature.get('comments') }}</div>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import * as ol from 'ol';
import 'ol/ol.css';
import OSM from 'ol/source/OSM';
import TileLayer from 'ol/layer/Tile';
import VectorLayer from 'ol/layer/Vector';
import VectorSource from 'ol/source/Vector';
import { Style } from 'ol/style';
import { fromLonLat } from 'ol/proj';
import { GeoJSON } from 'ol/format';
import Icon from 'ol/style/Icon';

export default {
  name: 'MapComponent',
  data() {
    return {
      isPanelVisible: false,
      selectedFeature: null,
    };
  },
  mounted() {
    this.initMap();
  },
  methods: {
    initMap() {
      const map = new ol.Map({
        target: 'map',
        layers: [
          new TileLayer({
            source: new OSM(),
          }),
        ],
        view: new ol.View({
          center: fromLonLat([73.42, 61.25]), 
          zoom: 12,
        }),
      });

      const vectorSource = new VectorSource({
        format: new GeoJSON(),
        url: 'http://betamaps.admsurgut.ru/geoserver/ne/ows?service=WFS&version=1.0.0&request=GetFeature&typeName=ne:busstation&outputFormat=application/json',
      });

      const vectorLayer = new VectorLayer({
        source: vectorSource,
        style: (feature) => {
          return new Style({
            image: new Icon({
              src: this.getIcon(feature),
              scale: 0.05,
            }),
          });
        },
      });


      map.addLayer(vectorLayer);

      map.on('click', (event) => {
        const feature = map.forEachFeatureAtPixel(event.pixel, (feature) => {
          return feature;
        });

        if (feature) {
          this.selectedFeature = feature;
          this.isPanelVisible = true;
        }
      });
    },
    getIcon(feature) {
      const comfortTy = feature.get('comfort_ty');
      const type = feature.get('type');
      const affilation = feature.get('affilation');
      const replace = feature.get('replace');

      if (type === 1) {
        return '/icons/busTrue.svg'; 
      } else if (type === 2) {
        return '/icons/busFalse.svg'; 
      } else if (type === 3) {
        return '/icons/busType3.svg';
      } else if (type === 4) {
        return '/icons/busType4.svg'; 
      } else {
        return '/icons/busType5.svg'; 
      }

    },
    closePanel() {
      this.isPanelVisible = false;
      this.selectedFeature = null;
    },
    getInformationType(type) {
      switch (type) {
        case 1:
          return 'Электронное табло';
        case 2:
          return 'Жидкокристаллический экран';
        default:
          return 'Нет данных';
      }
    },
    getComfortType(type) {
      switch (type) {
        case 1:
          return 'Теплая умная';
        case 2:
          return 'Обычная';
        default:
          return 'Нет данных';
      }
    },
  },
};
</script>

<style scoped>
#map-container {
  position: relative;
  width: 100vw;
  height: 100vh;
}

#info-panel {
  position: absolute;
  top: 0;
  left: -500px;
  width: 500px;
  height: 100%;
  background: white;
  box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
  transition: left 0.3s ease-in-out;
  z-index: 1000;
  color: black;
}

#info-panel.visible {
  left: 0;
}

.info-content {
  padding: 20px;
}

.close-btn {
  position: absolute;
  top: 10px;
  right: 10px;
  background: none;
  border: none;
  font-size: 20px;
  cursor: pointer;
}

.info-grid {
  display: grid;
  grid-template-columns: 1fr 2fr;
  gap: 10px;
  align-items: start;
}

.info-label {
  font-weight: bold;
  text-align: left;
  word-break: break-word;
}

.info-value {
  text-align: left;
  word-break: break-word;
}
</style>