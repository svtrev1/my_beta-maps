<template>
    <div id="map-container">
      <div id="map" style="height: 100%; width: 100%"></div>
      
      
      <div id="info-panel" :class="{ 'visible': isPanelVisible }">
        <div class="info-content">
          <button @click="closePanel" class="close-btn">×</button>
  
  
  
          <!-- Добавление остановки -->
  
          <div v-if="mode === 'add'" class="bg-white p-6 rounded-lg max-w-md mx-auto space-y-4">
    
            <!-- Заголовок -->
            <h2 class="text-xl font-bold text-gray-800 text-center">Добавление новой остановки</h2>
  
            <!-- Шаг 1: Выбор точки -->
            <div v-if="step === 1" class="text-center">
              <p class="text-gray-700 text-lg">Кликните на карту, чтобы установить точку!</p>
            </div>
  
            <!-- Шаг 2: Подтверждение точки -->
            <div v-if="step === 2" class="text-center space-y-3">
              <p class="text-gray-700 text-lg">Вы указали точку, можете ее переместить. Подтвердить?</p>
              <button @click="confirmPoint" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">
                Да
              </button>
            </div>
  
            <!-- Шаг 3: Ввод информации об остановке -->
          <div v-if="step === 3" class="space-y-4 max-h-[450px] overflow-y-auto">
            <p class="text-gray-700 text-lg text-center font-semibold">Заполните информацию об остановке:</p>
  
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
              Отправить!
            </button>
          </div>
  
            <!-- Шаг 4: Завершение -->
            <div v-if="step === 4" class="text-center space-y-3">
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
  
  
  
          <!-- Удаление остановки -->
          <div v-else-if="mode === 'delete'">
            <!-- Заголовок -->
            <h2 class="text-xl font-bold text-gray-800 text-center">Удаление остановки</h2>
  
            <!-- Шаг 1: Выбор остановки -->
            <div v-if="step === 1" class="text-center">
              <p class="text-gray-700 text-lg">Кликните на остановку, чтобы удалить ее!</p>
            </div>
  
            <!-- Шаг 2: Подтверждение точки -->
            <div v-if="step === 2" class="text-center space-y-3">
              <p class="text-gray-700 text-lg">Вы выбрали остановку <strong>{{selectedFeature.get('name') }}</strong>, можете ее поменять. Подтвердить?</p>
              <button @click="confirmDelete" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">
                Да
              </button>
            </div>
  
          </div>
  
  
  
          <!-- Редактирование остановки -->
          <div v-if="mode === 'edit'" class="bg-white p-6 rounded-lg max-w-md mx-auto space-y-4">
  
            <!-- Заголовок -->
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
                <button @click="() => { editFields.name = true; newStop.name = selectedFeature.get('name');}" class="p-2 flex items-center justify-center">
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
                    v-model="newStop.name"
                    type="text"
                    class="flex-1 p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 min-w-0"
                    placeholder="Введите название остановки"
                  />
                </div>
                <button
                  :disabled="!newStop.name.trim()"
                  @click="() => { selectedFeature.set('name', newStop.name); editFields.name = false; }"
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
                        'border-green-500': newStop.type === icon.value && newStop.type !== selectedFeature.get('type'),
                        'border-blue-500': icon.value === selectedFeature.get('type'),
                        'border-gray-300': newStop.type !== icon.value
                      }">
                      <input
                        type="radio"
                        v-model="newStop.type"
                        :value="icon.value"
                        class="hidden"/>
                      <img :src="icon.src" class="w-10 h-10" />
                    </label>
                  </div>
                  <button
                    @click="() => { selectedFeature.set('type', newStop.type); editFields.type = false }"
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
                <button @click="() => { editFields.numbus = true; newStop.numbus = selectedFeature.get('numbus'); }"
                        class="p-2 flex items-center justify-center">
                  <img src="/icons/edit.svg" alt="Изменить" class="w-6 h-6" />
                </button>
              </div>
  
              <!-- Режим редактирования -->
              <div v-else-if="editFields.numbus" class="flex items-center justify-between gap-4 flex-wrap">
                <label class="font-semibold whitespace-nowrap shrink-0">Номера автобусов:</label>
                <input v-model="newStop.numbus" type="text"
                      class="flex-1 min-w-0 p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                      placeholder="Введите номера автобусов (через запятую)" />
                <button
                  @click="() => { selectedFeature.set('numbus', newStop.numbus); editFields.numbus = false; }"
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
                <button @click="() => { editFields.numtaxi = true; newStop.numtaxi = selectedFeature.get('numtaxi'); }"
                        class="p-2 flex items-center justify-center">
                  <img src="/icons/edit.svg" alt="Изменить" class="w-6 h-6" />
                </button>
              </div>
  
              <!-- Режим редактирования -->
              <div v-else-if="editFields.numtaxi" class="flex items-center justify-between gap-4 flex-wrap">
                <label class="font-semibold whitespace-nowrap shrink-0">Номера марш. автобусов:</label>
                <input v-model="newStop.numtaxi" type="text"
                      class="flex-1 min-w-0 p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                      placeholder="Введите номера маршрутных автобусов (через запятую)" />
                <button
                  @click="() => { selectedFeature.set('numtaxi', newStop.numtaxi); editFields.numtaxi = false; }"
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
                <button @click="() => { editFields.year = true; newStop.year = selectedFeature.get('year'); }"
                        class="p-2 flex items-center justify-center">
                  <img src="/icons/edit.svg" alt="Изменить" class="w-6 h-6" />
                </button>
              </div>
  
              <!-- Режим редактирования -->
              <div v-else-if="editFields.year" class="flex items-center justify-between gap-4 flex-wrap">
                <label class="font-semibold whitespace-nowrap shrink-0">Год замены:</label>
                <input v-model="newStop.year" type="number"
                min="1"
                      class="flex-1 min-w-0 p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                      placeholder="Введите год замены" />
                <button
                  @click="() => { selectedFeature.set('year', newStop.year); editFields.year = false; }"
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
                <button @click="() => { editFields.financing = true; newStop.financing = selectedFeature.get('financing'); }"
                        class="p-2 flex items-center justify-center">
                  <img src="/icons/edit.svg" alt="Изменить" class="w-6 h-6" />
                </button>
              </div>
  
              <!-- Режим редактирования -->
              <div v-else-if="editFields.financing" class="flex items-center justify-between gap-4 flex-wrap">
                <label class="font-semibold whitespace-nowrap shrink-0">Источник финансирования:</label>
                <input v-model="newStop.financing" type="text"
                      class="flex-1 min-w-0 p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                      placeholder="Введите источник финансирования" />
                <button
                  @click="() => { selectedFeature.set('financing', newStop.financing); editFields.financing = false; }"
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
                <button @click="() => { editFields.comments = true; newStop.comments = selectedFeature.get('comments'); }"
                        class="p-2 flex items-center justify-center">
                  <img src="/icons/edit.svg" alt="Изменить" class="w-6 h-6" />
                </button>
              </div>
  
              <!-- Режим редактирования -->
              <div v-else-if="editFields.comments" class="flex items-center justify-between gap-4 flex-wrap">
                <label class="font-semibold whitespace-nowrap shrink-0">Коммментарии:</label>
                <input v-model="newStop.comments" type="text"
                      class="flex-1 min-w-0 p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                      placeholder="Введите комментарий" />
                <button
                  @click="() => { selectedFeature.set('comments', newStop.comments); editFields.comments = false; }"
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
                <button @click="exitAddMode" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg">
                  Выйти
              </button>
              </div>
            </div>  
          </div>
  
  
  
  
          <div v-else-if="selectedFeature && mode === 'default'" class="info-grid">
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