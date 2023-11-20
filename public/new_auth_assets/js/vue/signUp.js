
  createApp({
    setup() {
      const message = ref('Hello Vue! James')
      return {
        message
      }
    }
  }).mount('#app')