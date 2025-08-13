<template>
  <component :is="isMobile ? LoginMobile : LoginDesktop" />
</template>

<script>
import { defineComponent, computed } from 'vue';
import { useQuasar } from 'quasar';
import LoginMobile from 'src/pages/onboard/login/LoginMobile.vue';
import LoginDesktop from 'src/pages/onboard/login/LoginDesktop.vue';

export default defineComponent({
  components: {
    LoginMobile,
    LoginDesktop,
  },
  setup() {
    const $q = useQuasar();
    const isMobile = computed(() => $q.screen.width < 600);

    return { isMobile, LoginMobile, LoginDesktop };
  },
});
</script>
