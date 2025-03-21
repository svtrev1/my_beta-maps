import { defineStore } from 'pinia';

export const useModeStore = defineStore('mode', {
  state: () => ({
    mode: "default" // режим (add, edit, delete)
  }),
  actions: {
    setMode(newMode) {
      this.mode = newMode;
    }
  }
});
