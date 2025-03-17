<template>
    <header class="fixed top-0 left-0 w-full h-10 bg-[#4D84BC] text-white flex items-center justify-between px-4 z-50">
      <h1 class="text-sm">Остановки общественного транспорта</h1>
      <h2 class="text-sm font-semibold">Интерактивная карта г. Сургута</h2>
  
      <div class="relative flex items-center space-x-4">
        <!-- Админское меню (показывается только если авторизован) -->
        <div v-if="isAuthenticated" class="relative">
            <button @click="toggleMenu" class="p-2 bg-transparent border-none">
          <img src="/public/icons/menu.svg" alt="Меню" class="w-6 h-6" />
        </button>
          <div v-if="menuOpen" class="absolute right-0 mt-2 w-48 bg-white text-black rounded-md shadow-lg z-50">
            <button @click="setMode('delete')" class="block w-full px-4 py-2 text-left hover:bg-gray-200">Удалить остановку</button>
            <button @click="setMode('edit')" class="block w-full px-4 py-2 text-left hover:bg-gray-200">Редактировать остановку</button>
            <button @click="setMode('add')" class="block w-full px-4 py-2 text-left hover:bg-gray-200">Добавить остановку</button>
          </div>
        </div>
  
        <!-- Кнопка Вход/Выход -->
        <button @click="handleAuth" class="p-2">
          <img v-if="!isAuthenticated" src="/public/icons/login.svg" alt="Вход" class="w-6 h-6">
          <img v-else src="/public/icons/logout.svg" alt="Выход" class="w-6 h-6">
        </button>
      </div>
    </header>
  </template>
  
  <script>
  export default {
    data() {
      return {
        isAuthenticated: false, // Статус авторизации
        menuOpen: false, // Состояние меню
      };
    },
    created() {
      this.checkAuthStatus();
    },
    methods: {
      checkAuthStatus() {
        this.isAuthenticated = localStorage.getItem('auth') === 'true';
      },
      handleAuth() {
        if (this.isAuthenticated) {
          this.logout();
        } else {
          this.$router.push('/login');
        }
      },
      logout() {
        localStorage.removeItem('auth');
        this.isAuthenticated = false;
        this.menuOpen = false; // Закрываем меню при выходе
        this.$router.push('/');
      },
      toggleMenu() {
        this.menuOpen = !this.menuOpen;
      },
      setMode(mode) {
      
      this.menuOpen = false;
      this.$router.push({ path: "/", query: { mode } });
    }
    },
    watch: {
      '$route'() {
        this.checkAuthStatus();
      }
    }
  };
  </script>
  
  <style scoped>
  header {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 40px;
    background-color: #4D84BC;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 20px;
    z-index: 50;
  }

  /* Для кнопки меню, чтобы она не была круглой */
button {
  padding: 8px; /* Размер кнопки */
  background: none;
  border: none;
  cursor: pointer;
}

/* Картинка меню */
button img {
  width: 24px;
  height: 24px;
}
  
  .relative {
    position: relative;
  }
  </style>
