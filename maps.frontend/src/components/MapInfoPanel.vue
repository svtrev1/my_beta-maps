<!-- <template>
    <div id="info-panel" :class="{ 'visible': isPanelVisible }">
      <div class="info-content">
        <button @click="closePanel" class="close-btn">×</button>
        
        <div v-if="selectedFeature" class="info-grid">
          <template v-if="selectedFeature.get('name')">
            <div class="info-label">Наименование:</div>
            <div class="info-value">{{ selectedFeature.get('name') }}</div>
          </template>
          <template v-if="selectedFeature.get('street')">
            <div class="info-label">Местоположение:</div>
            <div class="info-value">{{ selectedFeature.get('street') }}</div>
          </template>
          <template v-if="selectedFeature.get('numbus')">
            <div class="info-label">Номера автобусов:</div>
            <div class="info-value">{{ selectedFeature.get('numbus') }}</div>
          </template>
          <template v-if="selectedFeature.get('numtaxi')">
            <div class="info-label">Номера маршрутных автобусов:</div>
            <div class="info-value">{{ selectedFeature.get('numtaxi') }}</div>
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
  </template>
  
  <script>
  export default {
    props: {
      isPanelVisible: {
        type: Boolean,
        required: true
      },
      selectedFeature: {
        type: Object,
        default: null
      }
    },
    methods: {
      closePanel() {
        this.$emit('close-panel');
      },
      getInformationType(type) {
        switch (type) {
          case 1: return 'Электронное табло';
          case 2: return 'Жидкокристаллический экран';
          default: return 'Нет данных';
        }
      },
      getComfortType(type) {
        switch (type) {
          case 1: return 'Теплая умная';
          case 2: return 'Обычная';
          default: return 'Нет данных';
        }
      }
    }
  };
  </script> -->
  <template>
    <div id="info-panel" :class="{ 'visible': isPanelVisible }">
      <div class="info-content">
        <button @click="closePanel" class="close-btn">×</button>
        
        <!-- Таблица с данными -->
        <table class="table table-hover" v-if="selectedFeature">
          <tbody>
            <!-- Заголовок -->
            <tr>
              <th colspan="2">
                <div class="panel-header">
                  <span class="label label-warning">Автобусные остановки</span>
                </div>
              </th>
            </tr>

            <!-- Наименование -->
            <tr v-if="selectedFeature.get('name')">
              <th>Наименование остановки</th>
              <td>{{ selectedFeature.get('name') }}</td>
            </tr>

            <!-- Местоположение -->
            <tr v-if="selectedFeature.get('street')">
              <th>Местоположение остановки</th>
              <td>{{ selectedFeature.get('street') }}</td>
            </tr>

            <!-- Номера автобусов -->
            <tr v-if="selectedFeature.get('numbus')">
              <th>Номера автобусов</th>
              <td>{{ selectedFeature.get('numbus') }}</td>
            </tr>

            <!-- Номера маршруток -->
            <tr v-if="selectedFeature.get('numtaxi')">
              <th>Номера маршрутных автобусов</th>
              <td>{{ selectedFeature.get('numtaxi') }}</td>
            </tr>

            <!-- Дополнительные поля -->
            <tr v-if="selectedFeature.get('year')">
              <th>Год замены</th>
              <td>{{ selectedFeature.get('year') }}</td>
            </tr>

            <tr v-if="selectedFeature.get('financing')">
              <th>Финансирование</th>
              <td>{{ selectedFeature.get('financing') }}</td>
            </tr>

            <!-- <tr v-if="selectedFeature.get('comments')">
              <th>Комментарии</th>
              <td>{{ selectedFeature.get('comments') }}</td>
            </tr> -->
          </tbody>
        </table>
      </div>
    </div>
</template>

<script>
export default {
  props: {
    isPanelVisible: {
      type: Boolean,
      required: true
    },
    selectedFeature: {
      type: Object,
      default: null
    }
  },
  methods: {
    closePanel() {
      this.$emit('close-panel');
    }
  }
};
</script>

<style scoped>
/* Стили таблицы */
.table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 10px;
  font-size: 0.9em;
}

.table th, .table td {
  padding: 8px 12px;
  border-bottom: 1px solid #ddd;
  text-align: left;
}

.table th {
  background-color: #f8f9fa;
  width: 40%;
}

/* Заголовок панели */
.panel-header {
  padding: 5px;
  text-align: center;
}

.label-warning {
  background-color: #ffc107;
  color: #000;
  padding: 4px 8px;
  border-radius: 3px;
  font-size: 0.9em;
}

/* Адаптивность */
@media (max-width: 768px) {
  .table {
    font-size: 0.8em;
  }
  
  .table th, .table td {
    padding: 6px 8px;
  }
}
</style>