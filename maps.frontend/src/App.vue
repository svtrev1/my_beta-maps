<template>
  <div id="app">
    <Map />
  </div>
</template>

<script>
import Map from './components/Map.vue';

export default {
  components: {
    Map,
  },
};
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  text-align: center;
  color: #2c3e50;
  /* Убираем фиксированные размеры */
  width: 100%;
  height: 100%;
  padding: 0;
  margin: 0;
}

html, body {
    height: 100%;
    width: 100%;
    margin: 0;
    padding: 0;
}
</style>