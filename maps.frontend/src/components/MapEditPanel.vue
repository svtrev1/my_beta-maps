<template>
    <div id="info-panel" :class="{ 'visible': isPanelVisible }">
      <div class="info-content">
        <button @click="closePanel" class="close-btn">×</button>
        
        <div class="bg-white p-6 rounded-lg max-w-md mx-auto space-y-4">
          <h2 class="text-xl font-bold text-gray-800 text-center">Редактирование остановки</h2>
            
            <!-- Шаг 1: Выбор точки -->
            <div v-if="step === 1" class="text-center">
                <p class="text-gray-700 text-lg">Кликните на остановку, чтобы выбрать точку!</p>
            </div>

            <!-- Шаг 2: Редактирование информации о остановке -->
            <div v-if="step === 2" class="space-y-4 max-h-[450px] overflow-y-autos">
                <p class="text-gray-700 text-lg">Вы выбрали остановку <strong>{{selectedFeature.get('name') }}</strong></p>
                
                <!-- Режим просмотра -->
                <div v-if="!editFields.name" class="flex items-center justify-between gap-4 flex-wrap">
                <div class="flex-1 flex items-center gap-2 min-w-0">
                    <span class="font-semibold whitespace-nowrap shrink-0">
                    Наименование: <span class="text-red-500">*</span>
                    </span>
                    <span class="truncate block text-ellipsis overflow-hidden min-w-0">
                    {{ selectedFeature.get('name')}}
                    </span>
                </div>
                <button @click="() => { editFields.name = true; tempStop.name = selectedFeature.get('name');}" class="p-2 flex items-center justify-center">
                    <img src="/icons/edit.svg" alt="Изменить" class="w-6 h-6" />
                </button>
                </div>


                <!-- Режим редактирования -->
                <div v-else-if="editFields.name" class="flex items-center gap-4 flex-wrap">
                <div class="flex items-center gap-2 flex-1 min-w-0">
                    <span class="font-semibold whitespace-nowrap shrink-0">
                    Наименование:<span class="text-red-500">*</span>
                    </span>
                    <input
                    v-model="tempStop.name"
                    type="text"
                    class="flex-1 p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 min-w-0"
                    placeholder="Введите название остановки"
                    />
                </div>
                <button
                    :disabled="!tempStop.name.trim()"
                    @click="() => { selectedFeature.set('name', tempStop.name); editFields.name = false; }"
                    class="p-2 flex items-center justify-center">
                    <img src="/icons/save.svg" alt="Сохранить" class="w-6 h-6" />
                </button>
                </div>

                <!-- Тип остановки -->
                <!-- Режим просмотра -->
                <div v-if="!editFields.type" class="flex flex-col gap-2">
                <label class="font-semibold">Тип: <span class="text-red-500">*</span></label>
                <div class="flex justify-between items-center flex-wrap gap-3">
                    <div class="flex gap-3 flex-wrap flex-1">
                    <div
                        v-for="(icon, index) in icons"
                        :key="'readonly-' + index"
                        class="p-2 rounded-full border-2"
                        :class="selectedFeature.get('type') === icon.value ? 'border-blue-500' : 'border-gray-300'">
                        <img :src="icon.src" class="w-10 h-10" />
                    </div>
                    </div>
                    <button @click="() => {editFields.type = true;}" class="p-2">
                    <img src="/icons/edit.svg" alt="Редактировать" class="w-6 h-6" />
                    </button>
                </div>
                </div>

                <!-- Режим редактирования -->
                <div v-else-if="editFields.type" class="flex flex-col gap-2">
                <label class="font-semibold">Тип: <span class="text-red-500">*</span></label>
                <div class="flex justify-between items-center flex-wrap gap-3">
                    <div class="flex gap-3 flex-wrap flex-1">
                    <label
                        v-for="(icon, index) in icons"
                        :key="'edit-' + index"
                        class="cursor-pointer p-2 rounded-full border-2 transition-all"
                        :class="{
                        'border-green-500': tempStop.type === icon.value && tempStop.type !== selectedFeature.get('type'),
                        'border-blue-500': icon.value === selectedFeature.get('type'),
                        'border-gray-300': tempStop.type !== icon.value
                        }">
                        <input
                        type="radio"
                        v-model="tempStop.type"
                        :value="icon.value"
                        class="hidden"/>
                        <img :src="icon.src" class="w-10 h-10" />
                    </label>
                    </div>
                    <button
                    @click="() => { selectedFeature.set('type', tempStop.type); editFields.type = false }"
                    class="p-2 text-green-500 hover:bg-green-100">
                    <img src="/icons/save.svg" alt="Сохранить" class="w-6 h-6" />
                    </button>
                </div>
                </div>

                <!-- Номера автобусов -->
                <!-- Режим просмотра -->
                <div v-if="!editFields.numbus" class="flex items-center justify-between gap-4 flex-wrap">
                <div class="flex-1 flex items-center gap-2 min-w-0">
                    <span class="font-semibold whitespace-nowrap shrink-0">
                    Номера автобусов:
                    </span>
                    <span class="truncate block text-ellipsis overflow-hidden min-w-0">
                    {{ selectedFeature.get('numbus') }}
                    </span>
                </div>
                <button @click="() => { editFields.numbus = true; tempStop.numbus = selectedFeature.get('numbus'); }"
                        class="p-2 flex items-center justify-center">
                    <img src="/icons/edit.svg" alt="Изменить" class="w-6 h-6" />
                </button>
                </div>

                <!-- Режим редактирования -->
                <div v-else-if="editFields.numbus" class="flex items-center justify-between gap-4 flex-wrap">
                <label class="font-semibold whitespace-nowrap shrink-0">Номера автобусов:</label>
                <input v-model="tempStop.numbus" type="text"
                        class="flex-1 min-w-0 p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Введите номера автобусов (через запятую)" />
                <button
                    @click="() => { selectedFeature.set('numbus', tempStop.numbus); editFields.numbus = false; }"
                    class="p-2 flex items-center justify-center">
                    <img src="/icons/save.svg" alt="Сохранить" class="w-6 h-6" />
                </button>
                </div>

                <!-- Номера маршрутных автобусов -->
                <!-- Режим просмотра -->
                <div v-if="!editFields.numtaxi" class="flex items-center justify-between gap-4 flex-wrap">
                <div class="flex-1 flex items-center gap-2 min-w-0">
                    <span class="font-semibold whitespace-nowrap shrink-0">
                    Номера марш. автобусов:
                    </span>
                    <span class="truncate block text-ellipsis overflow-hidden min-w-0">
                    {{ selectedFeature.get('numtaxi') }}
                    </span>
                </div>
                <button @click="() => { editFields.numtaxi = true; tempStop.numtaxi = selectedFeature.get('numtaxi'); }"
                        class="p-2 flex items-center justify-center">
                    <img src="/icons/edit.svg" alt="Изменить" class="w-6 h-6" />
                </button>
                </div>

                <!-- Режим редактирования -->
                <div v-else-if="editFields.numtaxi" class="flex items-center justify-between gap-4 flex-wrap">
                <label class="font-semibold whitespace-nowrap shrink-0">Номера марш. автобусов:</label>
                <input v-model="tempStop.numtaxi" type="text"
                        class="flex-1 min-w-0 p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Введите номера маршрутных автобусов (через запятую)" />
                <button
                    @click="() => { selectedFeature.set('numtaxi', tempStop.numtaxi); editFields.numtaxi = false; }"
                    class="p-2 flex items-center justify-center">
                    <img src="/icons/save.svg" alt="Сохранить" class="w-6 h-6" />
                </button>
                </div>

                <!-- Год замены -->
                <!-- Режим просмотра -->
                <div v-if="!editFields.year" class="flex items-center justify-between gap-4 flex-wrap">
                <div class="flex-1 flex items-center gap-2 min-w-0">
                    <span class="font-semibold whitespace-nowrap shrink-0">
                    Год замены:
                    </span>
                    <span class="truncate block text-ellipsis overflow-hidden min-w-0">
                    {{ selectedFeature.get('year') }}
                    </span>
                </div>
                <button @click="() => { editFields.year = true; tempStop.year = selectedFeature.get('year'); }"
                        class="p-2 flex items-center justify-center">
                    <img src="/icons/edit.svg" alt="Изменить" class="w-6 h-6" />
                </button>
                </div>

                <!-- Режим редактирования -->
                <div v-else-if="editFields.year" class="flex items-center justify-between gap-4 flex-wrap">
                <label class="font-semibold whitespace-nowrap shrink-0">Год замены:</label>
                <input v-model="tempStop.year" type="number"
                min="1"
                        class="flex-1 min-w-0 p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Введите год замены" />
                <button
                    @click="() => { selectedFeature.set('year', tempStop.year); editFields.year = false; }"
                    class="p-2 flex items-center justify-center">
                    <img src="/icons/save.svg" alt="Сохранить" class="w-6 h-6" />
                </button>
                </div>

                <!-- Финансирование -->
                <!-- Режим просмотра -->
                <div v-if="!editFields.financing" class="flex items-center justify-between gap-4 flex-wrap">
                <div class="flex-1 flex items-center gap-2 min-w-0">
                    <span class="font-semibold whitespace-nowrap shrink-0">
                    Источник финансирования:
                    </span>
                    <span class="truncate block text-ellipsis overflow-hidden min-w-0">
                    {{ selectedFeature.get('financing') }}
                    </span>
                </div>
                <button @click="() => { editFields.financing = true; tempStop.financing = selectedFeature.get('financing'); }"
                        class="p-2 flex items-center justify-center">
                    <img src="/icons/edit.svg" alt="Изменить" class="w-6 h-6" />
                </button>
                </div>

                <!-- Режим редактирования -->
                <div v-else-if="editFields.financing" class="flex items-center justify-between gap-4 flex-wrap">
                <label class="font-semibold whitespace-nowrap shrink-0">Источник финансирования:</label>
                <input v-model="tempStop.financing" type="text"
                        class="flex-1 min-w-0 p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Введите источник финансирования" />
                <button
                    @click="() => { selectedFeature.set('financing', tempStop.financing); editFields.financing = false; }"
                    class="p-2 flex items-center justify-center">
                    <img src="/icons/save.svg" alt="Сохранить" class="w-6 h-6" />
                </button>
                </div>

                <!-- Комментарий -->
                <!-- Режим просмотра -->
                <div v-if="!editFields.comments" class="flex items-center justify-between gap-4 flex-wrap">
                <div class="flex-1 flex items-center gap-2 min-w-0">
                    <span class="font-semibold whitespace-nowrap shrink-0">
                    Комментарии:
                    </span>
                    <span class="truncate block text-ellipsis overflow-hidden min-w-0">
                    {{ selectedFeature.get('comments') }}
                    </span>
                </div>
                <button @click="() => { editFields.comments = true; tempStop.comments = selectedFeature.get('comments'); }"
                        class="p-2 flex items-center justify-center">
                    <img src="/icons/edit.svg" alt="Изменить" class="w-6 h-6" />
                </button>
                </div>

                <!-- Режим редактирования -->
                <div v-else-if="editFields.comments" class="flex items-center justify-between gap-4 flex-wrap">
                <label class="font-semibold whitespace-nowrap shrink-0">Коммментарии:</label>
                <input v-model="tempStop.comments" type="text"
                        class="flex-1 min-w-0 p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Введите комментарий" />
                <button
                    @click="() => { selectedFeature.set('comments', tempStop.comments); editFields.comments = false; }"
                    class="p-2 flex items-center justify-center">
                    <img src="/icons/save.svg" alt="Сохранить" class="w-6 h-6" />
                </button>
                </div>

                <button @click="confirmPoint" 
                class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg">
                Отправить! 
                </button>
            </div>

            
            <!-- Шаг 3: Подтверждение -->
            <div v-if="step === 3" class="text-center space-y-3">
                <p class="text-gray-700 text-lg">Вы изменили остановку <strong>{{selectedFeature.get('name') }}</strong>. Подтвердить?</p>
                <button @click="submitEdit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">
                Отправить
                </button>
            </div>

            <!-- Шаг 4: Завершение-->
            <div v-if="step === 4" class="text-center space-y-3">
                <p class="text-gray-600 text-lg font-semibold">Остановка изменена!</p>
                <div class="flex justify-between">
                <button @click="closePanel" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg">
                    Выйти
                </button>
                </div>
            </div>  
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      isPanelVisible: Boolean,
      step: Number,
      selectedFeature: Object,
      editFields: Object,
      tempStop: Object,
      icons: Array
    },
    
    methods: {
      closePanel() {
        this.$emit('close-panel');
      },
      submitEdit() {
        this.$emit('submit-edit');
      },
      confirmPoint() {
        this.$emit('confirm-point');
      },
      updateField(field, value) {
        this.$emit('update-field', { field, value });
      }
    }
  }
  </script>
  
  