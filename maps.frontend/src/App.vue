<template>
  <div id="app" class="flex flex-col h-screen">
    <Header />
    <div class="flex-grow overflow-hidden">
      <router-view />
    </div>
  </div>
</template>

<script>
import Header from './components/Header.vue';

export default {
  components: {
    Header
  }
};
</script>
