<template>
  <q-layout class='bg-white'>
    <div class='flex row align-center justify-center'>
      <q-img class="q-mt-xl" src='../../assets/fortis4.png' />
    </div>
  </q-layout>
</template>

<script>
export default {
  name: 'SplashScreen',
  methods: {
    get_login() {
      let jwt = localStorage.getItem('access_token');

      if (jwt) {
        const parts = jwt.split('.');
        const payload = JSON.parse(atob(parts[1]));

        localStorage.setItem('user_email', payload.email);
        localStorage.setItem('user_name', payload.name);
        localStorage.setItem('is_admin', payload.is_admin);

        this.$router.push('/user/service');

        // Limpa o intervalo após a verificação de login
        clearInterval(this.timer);
      } else {
        // Se não houver token, redireciona para a página de login
        this.$router.push('/login');

        // Limpa o intervalo após o redirecionamento para o login
        clearInterval(this.timer);
      }
    },
  },
  mounted() {
    // Verifica o login assim que o componente for montado
    this.timer = setInterval(() => {
      console.log('Carregando.');
      this.get_login();
    }, 2000);
  },
  beforeUnmount() {
    // Certifique-se de limpar o intervalo antes do componente ser destruído
    clearInterval(this.timer);
  },
};
</script>

<style>
  .rounded {
    border-radius: 40px;
    background-color: white;
  }

  .background-video {
    max-width: 35vw;
    margin-top: 250px;
  }
</style>
