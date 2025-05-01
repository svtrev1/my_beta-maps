// src/store/auth.js
import { defineStore } from 'pinia'
import { ref } from 'vue'
import http from '@/http'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('token') || null)
  const isAuthenticated = ref(!!token.value)

  // Если при старте есть токен — сразу ставим его в заголовок
  if (token.value) {
    http.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
  }

  // Сохраняем токен и данные пользователя
  function login(newToken, userData) {
    localStorage.setItem('token', newToken)
    token.value = newToken
    user.value = userData
    isAuthenticated.value = true

    http.defaults.headers.common['Authorization'] = `Bearer ${newToken}`
  }

  // Разлогиниваемся: POST /api/logout, затем чистим состояние
  async function logout() {
    try {
      await http.post('/logout')
    } catch (e) {
      console.warn('Ошибка при logout:', e)
    } finally {
      localStorage.removeItem('token')
      token.value = null
      user.value = null
      isAuthenticated.value = false
      delete http.defaults.headers.common['Authorization']
    }
  }

  // Получаем текущего пользователя: GET /api/me
  async function fetchUser() {
    try {
      const response = await http.get('/me')
      user.value = response.data
      return user.value
    } catch (error) {
      // если неудача — выкидываем пользователя
      await logout()
      throw error
    }
  }

  return {
    user,
    token,
    isAuthenticated,
    login,
    logout,
    fetchUser
  }
})
