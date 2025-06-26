<template>
  <div class="flex justify-center items-center h-screen bg-gray-100">
    <div class="bg-white p-6 rounded shadow-lg w-96">
      <h3 class="text-lg font-semibold mb-4">Авторизация</h3>
      <form @submit.prevent="login">
        <div class="mb-4">
          <label for="email" class="block text-sm">Email:</label>
          <input 
            :type="email" 
            id="email" 
            v-model="email" 
            class="w-full px-4 py-2 border border-gray-300 rounded"
            required />
        </div>
        <div class="mb-4 relative">
          <label for="password" class="block text-sm">Пароль:</label>
          <div class="relative">
            <input 
            :type="showPassword ? 'text' : 'password'" 
            id="password" 
            v-model="password"
            class="w-full px-4 py-2 border border-gray-300 rounded pr-10" 
            required />
            <button
              type="button"
              class="absolute inset-y-0 right-0 px-3 flex items-center"
              @click="togglePasswordVis"
            > <img
              :src="showPassword ? '/icons/eye_hide.png' : '/icons/eye_show.png'"
              class="h-5 w-5"
            </button>
          </div>
        </div>
        <div v-if="errorMessage" class="mb-4 text-red-500 text-sm">
          {{ errorMessage }}
        </div>
        <div class="flex justify-between">
          <button type="button" @click="$router.push('/')" class="text-gray-500">Отмена</button>
          <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Войти</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/store/auth'
import http from '@/http'

 const router = useRouter()
 const authStore = useAuthStore()

 const email = ref('')
 const password = ref('')
 const errorMessage = ref('')
 const showPassword =ref(false)

 async function togglePasswordVis() {
  showPassword.value = !showPassword.value
 }

 async function login() {
   errorMessage.value = ''
   
   try {
    await http.get('/sanctum/csrf-cookie')

    const response = await http.post('/login', {
       email: email.value,
       password: password.value
     })
     
     if (!response.data?.token || !response.data?.user) {
       throw new Error('Неверный формат ответа от сервера')
     }
     
     authStore.login(response.data.token, response.data.user)
     
     router.push('/')
   } catch (error) {
     console.error('Ошибка авторизации:', error)
     errorMessage.value = 'Неверный email или пароль'
     authStore.logout()
   }
 }
</script>
