<template>
  <div id="map-container">
    <div id="map" style="height: 100%; width: 100%"></div>
    
    <div id="info-panel" :class="{ 'visible': isPanelVisible }">
      <div class="info-content">
        <button @click="closePanel" class="close-btn">×</button>
        <div v-if="mode === 'add'" class="bg-white p-6 rounded-lg max-w-md mx-auto space-y-4">
  
          <!-- Заголовок -->
          <h2 class="text-xl font-bold text-gray-800 text-center">Добавление новой остановки</h2>

          <!-- Шаг 1: Выбор точки -->
          <div v-if="addStep === 1" class="text-center">
            <p class="text-gray-700 text-lg">Кликните на карту, чтобы установить точку!</p>
          </div>

          <!-- Шаг 2: Подтверждение точки -->
          <div v-if="addStep === 2" class="text-center space-y-3">
            <p class="text-gray-700 text-lg">Вы указали точку, можете ее переместить. Подтвердить?</p>
            <button @click="confirmPoint" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">
              Да
            </button>
          </div>

          <!-- Шаг 3: Ввод информации об остановке -->
        <div v-if="addStep === 3" class="space-y-4 max-h-[450px] overflow-y-auto">
          <p class="text-gray-700 text-lg text-center font-semibold">📝 Заполните информацию об остановке:</p>

          <!-- Название остановки (обязательное) -->
          <div>
            <label class="block font-semibold">Название остановки <span class="text-red-500">*</span></label>
            <input v-model="newStop.name" type="text" 
              class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
              placeholder="Введите название остановки" />
            <p v-if="errors.name" class="text-red-500 text-sm">{{ errors.name }}</p>
          </div>

          <!-- Тип остановки -->
          <div class="flex gap-2">
            <label
              v-for="(icon, index) in icons"
              :key="index"
              class="cursor-pointer p-2 rounded-full border-2"
              :class="newStop.type === icon.value ? 'border-blue-500' : 'border-gray-300'"
            >
              <input
                type="radio"
                v-model="newStop.type"
                :value="icon.value"
                class="hidden"
              />
              <img :src="icon.src" class="w-10 h-10" />
            </label>
          </div>


          <!-- Улица (обязательное) -->
          <div>
            <label class="block font-semibold">Улица <span class="text-red-500">*</span></label>
            <input v-model="newStop.street" type="text" 
              class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
              placeholder="Введите улицу" />
            <p v-if="errors.street" class="text-red-500 text-sm">{{ errors.street }}</p>
          </div>

          <!-- Год замены (необязательное) -->
          <div>
            <label class="block font-semibold">Год замены</label>
            <input v-model="newStop.year" type="number" 
              class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
              placeholder="Введите год замены" />
          </div>

          <!-- Финансирование (необязательное) -->
          <div>
            <label class="block font-semibold">Финансирование</label>
            <input v-model="newStop.financing" type="text" 
              class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
              placeholder="Источник финансирования" />
          </div>

          <!-- Номера автобусов (необязательное) -->
          <div>
            <label class="block font-semibold">Номера автобусов</label>
            <input v-model="newStop.numbus" type="text" 
              class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
              placeholder="Введите номера автобусов (через запятую)" />
          </div>

          <!-- Номера маршрутных автобусов (необязательное) -->
          <div>
            <label class="block font-semibold">Номера маршрутных автобусов</label>
            <input v-model="newStop.numtaxi" type="text" 
              class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
              placeholder="Введите номера маршрутных автобусов (через запятую)" />
          </div>

          <!-- Комментарий (необязательное) -->
          <div>
            <label class="block font-semibold">Комментарий</label>
            <textarea v-model="newStop.comments" 
              class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
              placeholder="Введите комментарий"></textarea>
          </div>

          <!-- Кнопка отправки -->
          <button @click="submitStopInfo" 
            class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg">
            Done!
          </button>
        </div>


          <!-- Шаг 4: Завершение -->
          <div v-if="addStep === 4" class="text-center space-y-3">
            <p class="text-gray-600 text-lg font-semibold">Остановка добавлена!</p>
            <div class="flex justify-between">
              <button @click="startNewPoint" class="bg-green-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg">
                Продолжить
              </button>
              <button @click="exitAddMode" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg">
                Выйти
              </button>
            </div>
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
import { defineComponent, computed, ref, defineEmits } from 'vue';
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
import { useModeStore } from '../store/mode';

export default defineComponent({
  name: 'MapComponent',
  setup() {
    const modeStore = useModeStore(); 
    const mode = computed(() => modeStore.mode); 
    return { mode, modeStore }; 
  },
  data() {
    return {
      isPanelVisible: false,
      selectedFeature: null,
      addPoint: null,
      addLayer: null,
      addStep: 1,
      newPointText: null,
      newStop: {
        name: '',
        street: '',
        year: '',
        financing: '',
        numbus: '',
        numtaxi: '',
        comments: '',
        type: 1,
        coordinates: null,
      },
      errors: {
        name: '',
        street: '',
      },
      icons: [
        { src: "/icons/busTrue.svg", value: 1 },
        { src: "/icons/busFalse.svg", value: 2 },
        { src: "/icons/busType3.svg", value: 3 },
        { src: "/icons/busType4.svg", value: 4 },
        { src: "/icons/busType5.svg", value: 5 },
      ],
    };
  },
  mounted() {
    this.initMap();
  },
  watch: {
    mode() {
      if (this.mode === 'add') {
        this.isPanelVisible = true;
      } else {
        this.isPanelVisible = false;
      }
    },
    selectedType(newValue) {
      this.$emit("update:type", newValue);
    },
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

      
      const selectedType = ref(1);

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
        new Feature({
          geometry: new Point(coordinate),
        })
      );
      this.newStop.coordinates = coordinate;
      this.isPanelVisible = true;
      this.addStep = 2;
    },
    confirmPoint() {
      this.addStep = 3;
    },
    submitStopInfo() {
      this.errors.name = this.newStop.name.trim() ? '' : 'Название остановвки обязательно';
      this.errors.street = this.newStop.street.trim() ? '' : 'Улица обязательна';
      if (this.errors.name || this.errors.street) return;
      console.log("Данные новой остановки:", this.newStop);
      this.addStep = 4;
    },
    exitAddMode() {
      this.modeStore.setMode('default'); 
      this.addStep = 1;
      this.newPointText = '';
      this.addPoint = null;
      this.addLayer.getSource().clear();
      this.isPanelVisible = false;
    },
    startNewPoint() {
      this.addStep = 1;
      this.newPointText = '';
      this.addPoint = null;
      this.addLayer.getSource().clear();
    },
    getIcon(feature) {
      const type = feature.get('type');
      switch (type) {
        case 2: return '/icons/busFalse.svg';
        case 3: return '/icons/busType3.svg';
        case 4: return '/icons/busType4.svg';
        case 5: return '/icons/busType5.svg';
        default: return '/icons/busTrue.svg';
      }
    },
    closePanel() {
      this.isPanelVisible = false;
      this.modeStore.setMode('default'); 
      this.addStep = 1;
      this.newPointText = '';
      this.addPoint = null;
      this.addLayer.getSource().clear();
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
});
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