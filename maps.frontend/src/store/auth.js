import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useAuthStore = defineStore('auth', () => {
  const auth = ref(localStorage.getItem('auth') || null)
  const isAuthenticated = ref(!!auth.value)

  function login(newAuth) {
    localStorage.setItem('auth', newAuth) // 👈 здесь должна быть именно строка 'true'
    auth.value = newAuth
    isAuthenticated.value = true
  }

  function logout() {
    localStorage.removeItem('auth')
    auth.value = null
    isAuthenticated.value = false
  }

  return {
    auth,
    isAuthenticated,
    login,
    logout,
  }
})
