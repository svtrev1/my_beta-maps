<template>
  <div id="map-container">
    <div id="map" style="height: 100%; width: 100%"></div>
    
    <MapInfoPanel 
      v-if="mode === 'default' && selectedFeature"
      :is-panel-visible="isPanelVisible" 
      :selected-feature="selectedFeature"
      @close-panel="closePanel"
    />
      
    <MapAddPanel
      v-if="mode === 'add'"
      :is-panel-visible="isPanelVisible"
      :step="step"
      :temp-stop="tempStop"
      :errors="errors"
      :icons="icons"
      @close-panel="closePanel"
      @confirm-point="confirmPoint"
      @submit-add="submitAdd"
      @start-new="startNew"
    />

    <MapEditDeletePanel
      v-if="mode === 'edit'"
      :is-panel-visible="isPanelVisible"
      :step="step"
      :selected-feature="selectedFeature"
      :edit-fields="editFields"
      :temp-stop="tempStop"
      :icons="icons"
      @close-panel="closePanel"
      @submit-edit="submitEdit"
      @update-field="updateField"
      @confirm-point="confirmPoint"
      @submit-delete="submitDelete"
      @confirm-delete="confirmDelete"
      @cancel-delete="cancelDelete"
    />

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
import MapInfoPanel from './MapInfoPanel.vue';
import MapAddPanel from './MapAddPanel.vue';
import MapEditDeletePanel from './MapEditDeletePanel.vue';
import { useAuthStore } from '@/store/auth';

export default defineComponent({
  name: 'MapComponent',
  setup() {
    const modeStore = useModeStore(); 
    const mode = computed(() => modeStore.mode); 
    return { mode, modeStore }; 
  },
  components: {
    MapInfoPanel,
    MapAddPanel,
    MapEditDeletePanel
  },
  data() {
    return {
      isPanelVisible: false,
      selectedFeature: null,
      addLayer: null,
      step: 1,
      tempStop: {
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
      editFields: {
        name: false,
        street: false,
        year: false,
        financing: false,
        numbus: false,
        numtaxi: false,
        comments: false,
        type: false,
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
      if (this.mode === 'add')
        this.isPanelVisible = true;
    },
    selectedType(newValue) {
      this.$emit("update:type", newValue);
    },
  },
  computed: {
    isAuthenticated() {
      const authStore = useAuthStore();
      return authStore.isAuthenticated;
    }
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
        if (this.mode === 'add' && (this.step === 1 || this.step === 2)) {
          this.setAddPoint(event.coordinate);
        } else {
          this.handleFeatureClick(event);
        }
      });
    },
    handleFeatureClick(event) {
      const feature = this.map.forEachFeatureAtPixel(event.pixel, (feature) => {
        return feature;
      });

      if (feature) {
        if (this.mode === 'default' && !this.isAuthenticated) {
          this.selectedFeature = feature;
          this.isPanelVisible = true;
        }
        else if (this.isAuthenticated) {
          this.selectedFeature = feature;
          this.modeStore.setMode('edit');
          this.prepareEditMode(feature);
          this.isPanelVisible = true;
        }
      }

    },
    prepareEditMode(feature) {
      this.tempStop = {
        name: feature.get('name') || '',
        type: feature.get('type') || 1,
        numbus: feature.get('numbus') || '',
        numtaxi: feature.get('numtaxi') || '',
        year: feature.get('year') || '',
        financing: feature.get('financing') || '',
        comments: feature.get('comments') || '',
        coordinates: feature.getGeometry().getCoordinates()
      };
    },
    updateField({field, value}) {
      this.selectedFeature.set(field, value);
      this.tempStop[field] = value;

      // if (field === 'name') {
      //   this.validateName(value);
      // }
    },

    submitEdit() {
      console.log("Редактирование остановки:", this.selectedFeature.get('name'));

      console.log("Обновленная информация:", this.tempStop);
      this.step = 4;
      this.resetTempData();
    },

    setAddPoint(coordinate) {
      this.addLayer.getSource().clear();
      this.addLayer.getSource().addFeature(
        new Feature({
          geometry: new Point(coordinate),
        })
      );
      this.tempStop.coordinates = coordinate;
      this.isPanelVisible = true;
      this.step = 2;
    },

    confirmPoint() {
      this.step = 3;
    },

    // submitDelete() {
    //   console.log("Удаление остановки:", this.selectedFeature.get('name'));
    //   this.isPanelVisible = false;
    //   this.step = 1;
    //   this.modeStore.setMode('default');
    // },

    submitDelete() {
      console.log("Удаление остановки:", this.selectedFeature.get('name'));
      this.step = 5;
      this.resetTempData();
    },

    cancelDelete() {
      this.step = 1;
    },

    confirmDelete() {
      this.step = 4;
    },

    submitAdd() {
      this.errors.name = this.tempStop.name.trim() ? '' : 'Название остановки обязательно';
      this.errors.street = this.tempStop.street.trim() ? '' : 'Улица обязательна';
      if (this.errors.name || this.errors.street) return;
      console.log("Данные новой остановки:", this.tempStop);
      this.step = 4;
      this.resetTempData();
    },

    startNew() {
      this.step = 1;
      this.addLayer.getSource().clear();
      this.resetTempData();
    },

    prepareEditMode(feature) {
      this.resetTempData();
      this.selectedFeature = feature;
      this.tempStop = {
        name: feature.get('name') || '',
        street: feature.get('street') || '',
        year: feature.get('year') || '',
        financing: feature.get('financing') || '',
        numbus: feature.get('numbus') || '',
        numtaxi: feature.get('numtaxi') || '',
        comments: feature.get('comments') || '',
        type: feature.get('type') || 1,
        coordinates: feature.getGeometry().getCoordinates(),
      };
      this.step = 1;
      this.isPanelVisible = true;
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
      this.step = 1;
      this.addLayer.getSource().clear();
      this.selectedFeature = null;
      this.resetTempData();
    },

    resetTempData() {
      this.tempStop = {
      name: '',
      street: '',
      year: '',
      financing: '',
      numbus: '',
      numtaxi: '',
      comments: '',
      type: 1,
      coordinates: null,
    };
    this.errors = {
      name: '',
      street: '',
    };
    this.editFields = {
      name: false,
      street: false,
      year: false,
      financing: false,
      numbus: false,
      numtaxi: false,
      comments: false,
      type: false,
    };
    }
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