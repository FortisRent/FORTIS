<template>
  <div>
    <h1>User Profile</h1>
    <p>User ID: {{ user.id }}</p>
    <p>Name: {{ user.name }}</p>
    <p>CPF: {{ user.cpf }}</p>
  </div>

  <q-uploader
    style="max-width: 300px"
    :url="`https://api.omnifitness.com.br/v1/user/profile/upload/${user.id}`"
    field-name="user_image"
    label="Restricted to images"
    accept=".jpg, image/*"
    multiple="false"
    @rejected="onRejected"
    dark
  />
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
  methods: {
    onRejected (rejectedEntries) {
      const $q = useQuasar();
      // Notify plugin needs to be installed
      // https://quasar.dev/quasar-plugins/notify#Installation
      $q.notify({
        type: 'negative',
        message: `${rejectedEntries.length} file(s) did not pass validation constraints`
      });
    }
  },
  mounted() {
    const $q = useQuasar();
    this.userId = this.$route.params.id;

    // Notify user creation
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
