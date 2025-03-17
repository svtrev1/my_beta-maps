<template>
    <div class="modal-overlay" v-if="isVisible" @click.self="closeModal">
      <div class="modal">
        <button class="close-btn" @click="closeModal">&times;</button>
        <h2 class="modal-title">Укажите место для остановки</h2>
        <p v-if="!selectedPoint">Кликните на карту, чтобы установить точку</p>
        <p v-else>Вы указали точку. Подтвердить?</p>
        <div class="modal-actions" v-if="selectedPoint">
          <button @click="confirmPoint" class="confirm-btn">Да</button>
          <button @click="resetPoint" class="cancel-btn">Изменить</button>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: "AddStopModal",
    props: {
      isVisible: Boolean, // Показывать окно или нет
      selectedPoint: Array, // Координаты выбранной точки
    },
    methods: {
      closeModal() {
        this.$emit("close"); // Закрытие окна
      },
      confirmPoint() {
        this.$emit("confirm", this.selectedPoint); // Подтверждение выбора
      },
      resetPoint() {
        this.$emit("reset"); // Изменение точки
      },
    },
  };
  </script>
  
  <style scoped>
  .modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: flex-start;
    z-index: 1000;
  }
  
  .modal {
    background: white;
    padding: 20px;
    width: 300px;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    transform: translateX(0);
  }
  
  .close-btn {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 18px;
    border: none;
    background: none;
    cursor: pointer;
  }
  
  .modal-title {
    margin-bottom: 10px;
  }
  
  .modal-actions {
    margin-top: 15px;
    display: flex;
    gap: 10px;
  }
  
  .confirm-btn {
    background: green;
    color: white;
    padding: 8px 12px;
    border: none;
    cursor: pointer;
  }
  
  .cancel-btn {
    background: red;
    color: white;
    padding: 8px 12px;
    border: none;
    cursor: pointer;
  }
  </style>