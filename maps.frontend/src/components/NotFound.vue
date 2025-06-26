<template>
  <div class="not-found">
    <div class="container">
      <h1>Ошибка 404</h1>
      <p>Запрашиваемая страница не найдена. Приносим извинения за неудобства.</p>
      <p>
        Вы будете автоматически перенаправлены на главную страницу через
        <span class="countdown">{{ countdown }}</span> сек.
      </p>
      <h2>
        <router-link to="/">Перейти на главную страницу</router-link>
      </h2>
    </div>
  </div>
</template>

<script>
export default {
  name: 'NotFound',
  data() {
    return {
      countdown: 5
    }
  },
  mounted() {
    this.startCountdown();
  },
  methods: {
    startCountdown() {
      const timer = setInterval(() => {
        this.countdown--;
        
        if (this.countdown <= 0) {
          clearInterval(timer);
          this.$router.push('/');
        }
      }, 1000);
    }
  }
}
</script>

<style scoped>
.not-found {
  background-color: #5e90c2;
  color: white;
  font-family: 'Raleway', Arial, sans-serif;
  margin: 0;
  padding: 0;
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
}

.container {
  text-align: center;
  max-width: 600px;
  padding: 20px;
}

h1 {
  font-size: 62px;
  font-weight: 800;
  margin: 0 0 20px;
  text-transform: uppercase;
}

p {
  font-size: 18px;
  font-weight: 500;
  margin: 0 0 15px;
  line-height: 1.6;
}

.countdown {
  font-weight: 700;
  font-size: 20px;
}

a {
  color: #d9d9d9;
  text-decoration: none;
  transition: color 0.3s;
  font-weight: 600;
}

a:hover {
  color: #ffffff;
  text-decoration: underline;
}
</style>