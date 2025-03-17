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
html, body {
  height: 100%;
  width: 100%;
  margin: 0;
  padding: 0;
}

#app {
  height: 100%;
  width: 100%;
  margin: 0;
  padding: 0;
}
</style>