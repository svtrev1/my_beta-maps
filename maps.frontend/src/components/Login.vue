<template>
  <div class="flex justify-center items-center h-screen bg-gray-100">
    <div class="bg-white p-6 rounded shadow-lg w-96">
      <h3 class="text-lg font-semibold mb-4">Авторизация</h3>
      <form @submit.prevent="login">
        <div class="mb-4">
          <label for="username" class="block text-sm">Логин:</label>
          <input type="text" id="username" v-model="username" class="w-full px-4 py-2 border border-gray-300 rounded" required />
        </div>
        <div class="mb-4">
          <label for="password" class="block text-sm">Пароль:</label>
          <input type="password" id="password" v-model="password" class="w-full px-4 py-2 border border-gray-300 rounded" required />
        </div>
        <div class="flex justify-between">
          <button type="button" @click="$router.push('/')" class="text-gray-500">Отмена</button>
          <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Войти</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      username: '',
      password: ''
    };
  },
  methods: {
    login() {
      // Тут должна быть логика авторизации
      if (this.username === 'admin' && this.password === 'password') {
        localStorage.setItem('auth', true); // Пример авторизации
        this.$router.push('/'); // Перенаправляем на главную страницу
      } else {
        alert('Неверный логин или пароль');
      }
    }
  }
};
</script>
