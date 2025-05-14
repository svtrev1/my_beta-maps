<template>
  <header class="fixed top-0 left-0 w-full h-14 bg-[#4D84BC] text-white flex items-center px-6 z-50">
    <h1 class="text-lg">Остановки общественного транспорта</h1>

    <h2 class="absolute left-1/2 transform -translate-x-1/2 text-lg hidden lg:block font-bold">
      Интерактивная карта г. Сургута
    </h2>

    <div class="ml-auto flex items-center space-x-4">
      <button v-if="isAuthenticated" @click="setMode('add')" class="p-2" style="font-size: large;">
        +
      </button>

      <button @click="handleAuth" class="p-2">
        <img v-if="!isAuthenticated" src="/icons/login.svg" alt="Вход" class="w-6 h-6">
        <img v-else src="/icons/logout.svg" alt="Выход" class="w-6 h-6">
      </button>
    </div>
  </header>
</template>

<script>
import { useModeStore } from "@/store/mode"; 
import { storeToRefs } from "pinia";
import { useRouter } from "vue-router";
import { useAuthStore } from '@/store/auth';

export default {
  setup() {
    const modeStore = useModeStore(); 
    const authStore = useAuthStore();
    const { isAuthenticated } = storeToRefs(authStore);
    const router = useRouter();

    const handleAuth = () => {
      if (isAuthenticated.value) {
        authStore.logout();
        modeStore.setMode('default');
        router.push("/");
      } else {
        router.push("/login");
      }
    };

    const setMode = (newMode) => {
      modeStore.setMode(newMode); 
    };

    return {
      isAuthenticated,
      handleAuth,
      setMode
    };
  },
};
</script>

<style scoped>
header {
  height: 56px;
}
</style>
