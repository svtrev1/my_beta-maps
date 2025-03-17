<template>
  <div id="map-container">
    <div id="map" style="height: 100%; width: 100%"></div>
    
    <div id="info-panel" :class="{ 'visible': isPanelVisible }">
      <div class="info-content">
        <button @click="closePanel" class="close-btn">×</button>
        <div v-if="mode === 'add'">
          
          <p class="add-title">Добавление новой остановки</p>
          
          <!-- Шаг 1: Выбор точки -->
          <p v-if="addStep === 1" class="add-instruction">Кликните на карту, чтобы установить точку!</p>
          
          <!-- Шаг 2: Подтверждение точки -->
          <div v-if="addStep === 2">
            <p class="add-instruction">Вы указали точку! Подтвердить?</p>
            <button @click="confirmPoint" class="confirm-btn">Да</button>
          </div>
          
          <!-- Шаг 3: Ввод текста остановки -->
          <div v-if="addStep === 3">
            <p class="add-instruction">Впишите текст остановки:</p>
            <input v-model="newPointText" type="text" class="text-input" placeholder="Название остановки" />
            <button @click="submitStopText" class="confirm-btn">Done!</button>
          </div>
          
          <!-- Шаг 4: Завершение -->
          <div v-if="addStep === 4">
            <p class="add-success">Остановка добавлена!</p>
            <button @click="exitAddMode" class="confirm-btn">Выйти из режима добавления</button>
          </div>
        </div>
        <div v-else-if="selectedFeature" class="info-grid">
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
import { Feature } from 'ol';
import TileLayer from 'ol/layer/Tile';
import VectorLayer from 'ol/layer/Vector';
import VectorSource from 'ol/source/Vector';
import { Style, Circle, Fill, Stroke } from 'ol/style';
import { fromLonLat } from 'ol/proj';
import { GeoJSON } from 'ol/format';
import Icon from 'ol/style/Icon';
import Point from 'ol/geom/Point';

export default {
  name: 'MapComponent',
  data() {
    return {
      isPanelVisible: false,
      selectedFeature: null,
      mode: null,
      addPoint: null,
      addLayer: null,
      addStep: 1,
      newPointText: null,
    };
  },
  mounted() {
    this.initMap();
  },
  methods: {
    initMap() {
      this.map = new ol.Map({
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

      this.map.addLayer(vectorLayer);
      
      this.addLayer = new VectorLayer({
        source: new VectorSource(),
        style: new Style({
          image: new Circle({
            radius: 6,
            fill: new Fill({ color: 'red' }),
            stroke: new Stroke({ color: 'white', width: 2 }),
          }),
        }),
      });

      this.map.addLayer(this.addLayer);

      this.map.on('click', (event) => {
        if (this.mode === 'add' && (this.addStep === 1 || this.addStep === 2)) {
          this.setAddPoint(event.coordinate);
        } else {
          this.handleFeatureClick(event);
        }
      });
    },
    handleFeatureClick(event) {
      if (this.mode === 'add') return;
      const feature = this.map.forEachFeatureAtPixel(event.pixel, (feature) => {
        return feature;
      });
      if (feature && this.mode !== 'add') {
        this.selectedFeature = feature;
        this.isPanelVisible = true;
      }
    },
    setAddPoint(coordinate) {
      this.addPoint = coordinate;
      this.addLayer.getSource().clear();
      this.addLayer.getSource().addFeature(
        new ol.Feature({
          geometry: new Point(coordinate),
        })
      );
      this.isPanelVisible = true;
      this.addStep = 2;
    },
    confirmPoint() {
      this.addStep = 3;
    },
    submitStopText() {
      if (this.newPointText.trim()) {
        console.log('Текст остановки:', this.newPointText);
        this.addStep = 4; // Переход к шагу завершения
      }
      else {
        alert('Пожалуйста, введите текст остановки.');
      }
    },
    exitAddMode() {
      this.mode = 'default';
      this.addStep = 1; // Сброс шага
      this.newPointText = ''; // Очистка текста
      this.addPoint = null; // Очистка точки
      this.addLayer.getSource().clear(); // Очистка слоя
      this.isPanelVisible = false; // Закрытие панели
      this.$router.push('/'); // Перенаправление на главную страницу
    },
    getIcon(feature) {
      const type = feature.get('type');
      switch (type) {
        case 1: return '/icons/busTrue.svg';
        case 2: return '/icons/busFalse.svg';
        case 3: return '/icons/busType3.svg';
        case 4: return '/icons/busType4.svg';
        default: return '/icons/busType5.svg';
      }
    },
    closePanel() {
      this.isPanelVisible = false;
      this.selectedFeature = null;
      this.addPoint = null;
      this.addLayer.getSource().clear();
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
  watch: {
    "$route.query.mode": {
      immediate: true, // Вызываем при загрузке
      handler(newMode) {
        this.mode= newMode || "default";
        if (this.mode === 'add')
        {
          this.isPanelVisible = true;
        }
        console.log(`Текущий режим: ${this.mode}`);
      }
    }
  }
};
</script>

    


<style scoped>
#map-container {
  position: relative;
  width: 100vw;
  height: 100%; /* Убираем лишний отступ снизу */
  margin-top: 40px;  /* Учитываем высоту хедера */
  z-index: 5;
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
  z-index: 100;
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
.add-title {
  font-weight: bold;
  font-size: 18px;
  margin-bottom: 10px;
}

.add-instruction {
  font-size: 16px;
  margin-bottom: 10px;
}

.add-success {
  font-size: 16px;
  color: green;
  margin-bottom: 10px;
}

.confirm-btn {
  background-color: #4CAF50;
  color: white;
  border: none;
  padding: 10px 20px;
  cursor: pointer;
  font-size: 16px;
}

.text-input {
  width: 100%;
  padding: 10px;
  margin-bottom: 10px;
  font-size: 16px;
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