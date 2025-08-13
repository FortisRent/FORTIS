<template>
  <component :is="isMobile ? UserCreateMobile : UserCreateDesktop" />
</template>

<script>
import { defineComponent, computed } from 'vue';
import { useQuasar } from 'quasar';
import UserCreateMobile from 'src/pages/onboard/user/UserCreateMobile.vue';
import UserCreateDesktop from 'src/pages/onboard/user/UserCreateDesktop.vue';

export default defineComponent({
  components: {
    UserCreateMobile,
    UserCreateDesktop,
  },
  setup() {
    const $q = useQuasar();
    const isMobile = computed(() => $q.screen.width < 600);

    return { isMobile, UserCreateMobile, UserCreateDesktop };
  },
});
</script>