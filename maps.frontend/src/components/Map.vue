<template>
  <div id="map-container">
    <div v-if="showLegend" id="map-legend">
      <div class="legend-header">
        <h3>Условные обозначения</h3>
        <button class="close-legend" @click="showLegend = false">×</button>
      </div>
      <ul class="legend-list">
        <li v-for="item in icons" :key="item.value">
          <img :src="item.src" class="legend-icon">
          <span class="legend-text">{{ item.text }}</span>
        </li>
      </ul>
    </div>
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
      @confirm-point="confirmPointAdd"
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
      :contracts="contracts"
      @close-panel="closePanel"
      @submit-edit="submitEdit"
      @update-field="updateField"
      @confirm-point="confirmPointEdit"
      @submit-delete="submitDelete"
      @confirm-delete="confirmDelete"
      @cancel-delete="cancelDelete"
      @upload-contract="handleUploadContract"
      @download-contract="handleDownloadContract"
      @delete-contract="handleDeleteContract"
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
import { fromLonLat, toLonLat } from 'ol/proj';
import { GeoJSON } from 'ol/format';
import Icon from 'ol/style/Icon';
import Point from 'ol/geom/Point';
import { useModeStore } from '../store/mode';
import MapInfoPanel from './MapInfoPanel.vue';
import MapAddPanel from './MapAddPanel.vue';
import MapEditDeletePanel from './MapEditDeletePanel.vue';
import { useAuthStore } from '@/store/auth';
import axios from 'axios';
import http from '@/http'

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
      showLegend: true,
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
      busSource: null,
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
        { src: "/icons/busTrue.svg", value: 1, text: "Установлен павильон" },
        { src: "/icons/busFalse.svg", value: 2, text: "Не установлен павильон" },
        { src: "/icons/busType3.svg", value: 3, text: "Установлен павильон предпринимателем" },
        { src: "/icons/busType4.svg", value: 4, text: "Установлен теплый павильон" },
        { src: "/icons/busType5.svg", value: 5, text: "Установлен навес" },
      ],
      contracts: [],
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
      this.busSource = vectorSource;

      const vectorLayer = new VectorLayer({
        source: vectorSource,
        style: (feature) => {
          return this.getStyle(feature);
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
        const oldSelected = this.selectedFeature;
        this.selectedFeature = feature;
        if (this.mode === 'default' && !this.isAuthenticated) {
          this.isPanelVisible = true;
        } else if (this.isAuthenticated) {
          this.modeStore.setMode('edit');
          this.prepareEditMode(feature);
          this.isPanelVisible = true;
        }

        if (oldSelected && oldSelected !== feature) {
          this.updateFeatureStyle(oldSelected);
        }
        this.updateFeatureStyle(feature);
      }

    },

    updateFeatureStyle(feature) {
      const style = this.getStyle(feature);
      feature.setStyle(style);
    },

    updateField({field, value}) {
      this.selectedFeature.set(field, value);
      this.tempStop[field] = value;
    },

    async submitEdit() {
      try {
        const fullId = this.selectedFeature.getId();
        const id = fullId?.split('.')[1];
        this.errors = {};

        const response = await http.put(`/busstop/${id}`, this.tempStop);
        console.log(response.data);
        this.step = 3;
        this.resetTempData();
        this.busSource.clear(true);
        this.busSource.refresh();
      }
      catch (error) {
        if (error.response && error.response.status === 422) {
          this.errors = error.response.data.errors;
        } else {
          console.error('Ошибка при отправке:', error);
        }
      }
    },

    setAddPoint(coordinate) {
      this.addLayer.getSource().clear();
      this.addLayer.getSource().addFeature(
        new Feature({
          geometry: new Point(coordinate),
        })
      );
      const lonLat = toLonLat(coordinate);
      this.tempStop.coordinates = lonLat;
      this.isPanelVisible = true;
      this.step = 2;
    },

    confirmPointEdit() {
      this.step = 2;
    },

    confirmPointAdd() {
      this.step = 3;
    },

    async submitDelete() {
      const fullId = this.selectedFeature.getId();
      const id = fullId?.split('.')[1]; 
      this.errors = {};
      
      try {
        const response = await http.delete(`/busstop/${id}`);

        console.log(response.data);
        this.step = 5;
        this.resetTempData();
        this.busSource.clear(true);
        this.busSource.refresh();
      }
      catch (error) {
        if (error.response && error.response.status === 422) {
          this.errors = error.response.data.errors;
        } else {
          console.error('Ошибка при отправке:', error);
        }
      }
      
    },

    cancelDelete() {
      this.step = 1;
    },

    confirmDelete() {
      this.step = 4;
    },

    getCookie(name) {
      const value = `; ${document.cookie}`;
      const parts = value.split(`; ${name}=`);
      if (parts.length === 2) return parts.pop().split(';').shift();
    },

    async submitAdd() {
      this.errors = {};

      try {
        const response = await http.post('/addbusstop', this.tempStop);
        console.log(response.data);
        this.step = 4; 
        this.resetTempData();
        this.busSource.clear(true);
        this.busSource.refresh();
      } catch (error) {
        if (error.response && error.response.status === 422) {
          this.errors = error.response.data.errors;
        } else {
          console.error('Ошибка при отправке:', error);
        }
      }
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
      };
      this.step = 1;
      this.isPanelVisible = true;
      const fullId = feature.getId();
      const id = fullId.split('.')[1];
      this.fetchContracts(id);
    },

    getStyle(feature) {
      const isSelected = this.selectedFeature && this.selectedFeature.getId() === feature.getId();
      const baseScale = 0.05;
      const selectedScale = 0.07;

      return new Style({
        image: new Icon( {
          src: this.getIcon(feature),
          scale: isSelected ? selectedScale : baseScale,
          opacity: isSelected ? 1 : 0.8,
        }),
      });
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
      if (this.selectedFeature) {
        const oldSelected = this.selectedFeature;
        this.selectedFeature = null;
        this.updateFeatureStyle(oldSelected);
      }
      this.isPanelVisible = false;
      this.modeStore.setMode('default'); 
      this.step = 1;
      this.addLayer.getSource().clear();
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
    },


    // Работа с файлами
    async fetchContracts(stopId) {
      try {
        const response = await http.get(`/stops/${stopId}/contracts`);
        this.contracts = response.data;
      } catch (e) {
        console.error('Не удалось загрузить список договоров', e);
      }
    },

    // загрузить файл
    async handleUploadContract(file) {
      const fullId = this.selectedFeature.getId();
      const id = fullId.split('.')[1];
      const formData = new FormData();
      formData.append('contract', file);

      try {
        await http.post(`/stops/${id}/contracts`, formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });
        await this.fetchContracts(id);
      } catch (e) {
        console.error('Ошибка загрузки договора', e);
      }
    },

    // скачать
    handleDownloadContract(contractId) {
      http({
        url: `/contracts/${contractId}/download`,
        method: 'GET',
        responseType: 'blob'
      }).then(res => {
        const url = window.URL.createObjectURL(new Blob([res.data]));
        const link = document.createElement('a');
        link.href = url;
        const contract = this.contracts.find(c => c.id === contractId);
        link.setAttribute('download', contract.original_name);
        document.body.appendChild(link);
        link.click();
        link.remove();
      }).catch(e => console.error(e));
    },

    // удалить
    async handleDeleteContract(contractId) {
      if (!confirm('Удалить файл?')) return;
      try {
        await http.delete(`/contracts/${contractId}`);
        const fullId = this.selectedFeature.getId();
        const id = fullId.split('.')[1];
        await this.fetchContracts(id);
      } catch (e) {
        console.error('Ошибка удаления договора', e);
      }
    },
  },
});
</script>

<style scoped>
#map-container {
  position: relative;
  width: 100vw;
  height: 100%; 
  margin-top: 40px;  
  z-index: 5;
}

#info-panel {
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  max-width: 500px;
  height: 100%;
  background: white;
  box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
  transition: left 0.3s ease-in-out;
  z-index: 100;
  color: black;
  overflow-y: auto;
}

#info-panel.visible {
  left: 0;
}

@media (min-width: 501px) {
  #info-panel {
    left: -500px;
  }
  #info-panel.visible {
    left: 0;
  }
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

::v-deep .ol-viewport .ol-overlaycontainer-stopevent {
  transition: all 0.3s ease;
}

::v-deep .ol-viewport {
  backface-visibility: hidden;
  perspective: 1000px;
}

#map-legend {
  position: absolute;
  top: 10px;
  right: 10px;
  background: rgba(255, 255, 255, 0.7); 
  border-radius: 8px;
  padding: 15px;
  z-index: 1000; 
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
  max-width: 300px;
}

.legend-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
  border-bottom: 1px solid #eee;
  padding-bottom: 8px;
}

.legend-header h3 {
  color: #4D84BC;
  margin: 0;
  font-size: 16px;
  font-weight: bold;
  flex-grow: 1;
  text-align: center;
}

.close-legend {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
  color: #999;
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: color 0.3s;
}

.close-legend:hover {
  color: #333;
}

.legend-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.legend-list li {
  display: flex;
  align-items: center;
  margin-bottom: 8px;
}

.legend-icon {
  width: 24px;
  height: 24px;
  margin-right: 10px;
}

.legend-text {
  font-size: 14px;
  color: #333;
}

@media (max-width: 768px) {
  #map-legend {
    display: none;
  }
}
</style>

