<template>
    <div id="info-panel" :class="{ 'visible': isPanelVisible }">
      <div class="info-content">
        <button @click="$emit('close-panel')" class="close-btn">×</button>
        
        <!-- Весь контент панели добавления -->
        <div class="bg-white p-6 rounded-lg max-w-md mx-auto space-y-4">
            <h2 class="text-xl font-bold text-gray-800 text-center">Добавление новой остановки</h2>

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
            <div v-if="step === 3" class="space-y-4 ">
                <p class="text-gray-700 text-lg text-center font-semibold">Заполните информацию об остановке:</p>
  
                <!-- Название остановки (обязательное) -->
                <div>
                    <label class="block font-semibold">Название остановки <span class="text-red-500">*</span></label>
                    <input v-model="tempStop.name" type="text" 
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
                    :class="tempStop.type === icon.value ? 'border-blue-500' : 'border-gray-300'"
                >
                    <input
                    type="radio"
                    v-model="tempStop.type"
                    :value="icon.value"
                    class="hidden"
                    />
                    <img :src="icon.src" class="w-10 h-10" />
                </label>
                </div>
  
                <!-- Улица (обязательное) -->
                <div>
                <label class="block font-semibold">Улица <span class="text-red-500">*</span></label>
                <input v-model="tempStop.street" type="text" 
                    class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                    placeholder="Введите улицу" />
                <p v-if="errors.street" class="text-red-500 text-sm">{{ errors.street }}</p>
                </div>
    
                <!-- Год замены (необязательное) -->
                <div>
                <label class="block font-semibold">Год замены</label>
                <input v-model="tempStop.year" type="number" 
                    class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                    placeholder="Введите год замены" />
                </div>
    
                <!-- Финансирование (необязательное) -->
                <div>
                <label class="block font-semibold">Финансирование</label>
                <input v-model="tempStop.financing" type="text" 
                    class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                    placeholder="Источник финансирования" />
                </div>
  
                <!-- Номера автобусов (необязательное) -->
                <div>
                <label class="block font-semibold">Номера автобусов</label>
                <input v-model="tempStop.numbus" type="text" 
                    class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                    placeholder="Введите номера автобусов (через запятую)" />
                </div>
    
                <!-- Номера маршрутных автобусов (необязательное) -->
                <div>
                <label class="block font-semibold">Номера маршрутных автобусов</label>
                <input v-model="tempStop.numtaxi" type="text" 
                    class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                    placeholder="Введите номера маршрутных автобусов (через запятую)" />
                </div>
  
                <!-- Комментарий (необязательное) -->
                <div>
                <label class="block font-semibold">Комментарий</label>
                <textarea v-model="tempStop.comments" 
                    class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                    placeholder="Введите комментарий"></textarea>
                </div>
  
                <!-- Кнопка отправки -->
                <button @click="submitAdd" 
                class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg">
                Отправить!
                </button>
            </div>
  
            <!-- Шаг 4: Завершение -->
            <div v-if="step === 4" class="text-center space-y-3">
                <p class="text-gray-600 text-lg font-semibold">Остановка добавлена!</p>
                <div class="flex justify-between">
                    <button @click="startNew" class="bg-green-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg">
                    Продолжить
                    </button>
                    <button @click="closePanel" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg">
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
      tempStop: Object,
      errors: Object,
      icons: Array
    },

    methods: {
    confirmPoint() {
      this.$emit('confirm-point');
    }, 
    closePanel() {
      this.$emit('close-panel');
    },
    submitAdd () {
      this.$emit('submit-add');
    },
    startNew() {
      this.$emit('start-new');
    },
  }
  }
  </script>
  