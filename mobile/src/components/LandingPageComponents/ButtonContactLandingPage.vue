<template>
  <q-btn
    href="https://api.whatsapp.com/send?phone=5548991582727&text=Ol%C3%A1,%20pode%20me%20ajudar?%20Cliquei%20no%20site%20da%20Fortis"
    target="_blank"
    class="btn-contact-lp"
  >
    <q-icon name="headset_mic" size="30px"/>
    {{ label }}
  </q-btn>
</template>

<script>
  export default {
    props: {
      label: {
        type: String,
        default: 'Iniciar conversa'
      }
    }
  }
</script>

<style scoped lang="scss">
  .btn-contact-lp {
    color: #303940;
    background-color: #fb0;
    width: 200px;
    border-radius: 5px;
    padding: 0.5rem 0;
  }
</style>