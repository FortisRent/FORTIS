<template>
  <div>
    <h1>User Profile</h1>
    <p>User ID: {{ user.id }}</p>
    <p>Name: {{ user.name }}</p>
    <p>CPF: {{ user.cpf }}</p>
  </div>
</template>

<script>
import { useQuasar } from 'quasar';

export default {
  data() {
    return {
      userId: null,
      user: {},
    };
  },
  mounted() {
    const $q = useQuasar();
    this.userId = this.$route.params.id;

    $q.notify({
      color: 'green-4',
      textColor: 'white',
      icon: 'cloud_done',
      message: this.userId,
    });

    fetch('https://api.omnifitness.com.br/v1/user/' + this.userId)
      .then(response => response.json())
      .then(data => {
        this.user = data.users;
      })
      .catch(error => {
        console.error('Error fetching data:', error);
      });
  },
};
</script>
