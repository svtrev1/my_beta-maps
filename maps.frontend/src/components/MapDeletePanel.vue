<template>
    <div id="info-panel" :class="{ 'visible': isPanelVisible }">
      <div class="info-content">
        <button @click="closePanel" class="close-btn">×</button>
        
        <div class="bg-white p-6 rounded-lg max-w-md mx-auto space-y-4">
          <h2 class="text-xl font-bold text-gray-800 text-center">Удаление остановки</h2>
  
          <!-- Шаг 1: Выбор остановки -->
          <div v-if="step === 1" class="text-center">
            <p class="text-gray-700 text-lg">Кликните на остановку, чтобы удалить ее!</p>
          </div>
  
          <!-- Шаг 2: Подтверждение удаления -->
          <div v-if="step === 2" class="text-center space-y-3">
            <p class="text-gray-700 text-lg">
              Вы выбрали остановку <strong>{{ selectedFeatureName }}</strong>, 
              можете выбрать другую. Удалить?
            </p>
            <button @click="submitDelete" 
                    class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg">
              Удалить
            </button>
          </div>

          <div v-if="step === 3" class="text-center space-y-3">
                <p class="text-gray-600 text-lg font-semibold">Остановка удалена!</p>
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
      selectedFeature: Object
    },
    
    computed: {
      selectedFeatureName() {
        return this.selectedFeature?.get('name') || '';
      }
    },
    
    methods: {
      startNew() {
        this.$emit('start-new');
      },
      submitDelete() {
        this.$emit('submit-delete');
      },
      closePanel() {
        this.$emit('close-panel');
      }
    }
  }
  </script>
  