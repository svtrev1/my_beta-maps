<template>
  <header class="fixed top-0 left-0 w-full h-14 bg-[#4D84BC] text-white flex items-center px-6 z-50">
    <h1 class="text-lg">Остановки общественного транспорта</h1>

    <h2 class="absolute left-1/2 transform -translate-x-1/2 text-lg hidden lg:block font-bold">
      Интерактивная карта г. Сургута
    </h2>

    <div class="ml-auto flex items-center space-x-4">
      <div v-if="isAuthenticated" class="relative">
        <button @click="toggleMenu" class="p-2">
          <img src="/icons/menu.svg" alt="Меню" class="w-6 h-6" />
        </button>
        <div v-if="menuOpen" class="absolute right-0 mt-2 w-48 bg-white text-black rounded-md shadow-lg z-50">
          <button @click="setMode('delete')" class="block w-full px-4 py-2 text-left hover:bg-gray-200">Удалить остановку</button>
          <button @click="setMode('edit')" class="block w-full px-4 py-2 text-left hover:bg-gray-200">Редактировать остановку</button>
          <button @click="setMode('add')" class="block w-full px-4 py-2 text-left hover:bg-gray-200">Добавить остановку</button>
        </div>
      </div>

      <button @click="handleAuth" class="p-2">
        <img v-if="!isAuthenticated" src="/icons/login.svg" alt="Вход" class="w-6 h-6">
        <img v-else src="/icons/logout.svg" alt="Выход" class="w-6 h-6">
      </button>
    </div>
  </header>
</template>

<script>
import { ref } from "vue";
import { useModeStore } from "@/store/mode"; 
import { storeToRefs } from "pinia";
import { useRouter } from "vue-router";
import { useAuthStore } from '@/store/auth';

export default {
  setup() {
    const modeStore = useModeStore(); 
    const authStore = useAuthStore();
    const { mode } = storeToRefs(modeStore); 
    const { isAuthenticated } = storeToRefs(authStore);

    const menuOpen = ref(false);
    const router = useRouter();

    const toggleMenu = () => {
      menuOpen.value = !menuOpen.value;
    };

    const handleAuth = () => {
      if (isAuthenticated.value) {
        logout();
      } else {
        router.push("/login");
      }
    };

    const logout = () => {
      authStore.logout();
      menuOpen.value = false;
      router.push("/");
    };

    const setMode = (newMode) => {
      menuOpen.value = false;
      modeStore.setMode(newMode); 
    };

    return {
      mode, 
      isAuthenticated,
      menuOpen,
      toggleMenu,
      handleAuth,
      logout,
      setMode,
    };
  },
};
</script>

<style scoped>
header {
  height: 56px;
}
</style>
