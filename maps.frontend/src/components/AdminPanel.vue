<template>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
      <div class="bg-white p-6 rounded shadow-lg w-96">
        <h3 class="text-lg font-semibold mb-4">Админ Панель</h3>
        <button @click="closePanel" class="bg-red-500 text-white px-4 py-2 rounded">Закрыть</button>
        <!-- Здесь будут функции админ-панели -->
      </div>
    </div>
  </template>
  
  <script>
  export default {
    methods: {
      closePanel() {
        this.$router.push('/'); // Закрытие модального окна (возвращаем на главную)
      }
    }
  };
  </script>
  