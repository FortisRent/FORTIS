<template>
    <q-page class="bg-white">
      <div class="row justify-around items-center q-pa-md q-mt-lg">
              <q-btn
                  flat
                  round
                  icon="chevron_left"
                  class="text-secondary"
                  color="secondary"
                  size="18px"
                  style="position: absolute; left: 10px;"
                  @click="$router.go(-1)"
              />
              <div class="text-h6 text-primary text-bold q-ml-md">
                  Editar Contato Emergência
              </div>
          </div>
  
          <q-separator class="bg-grey" />
      
          <div class="q-pa-md">
        <q-form @submit="on_submit" @reset="on_reset" class="q-gutter-md q-pa-md">
          <q-input
          rounded
          color="secondary"
          v-model="full_name"
          label="Nome Completo"
          lazy-rules
          :rules="[ val => val && val.length > 0 || 'Por favor, insira o Nome Completo']"
          no-error-icon
        />
        <q-input
          rounded
          color="secondary"
          v-model="birthdate"
          label="Número de Telefone"
          lazy-rules
          :rules="[ val => val && val.length > 0 || 'Por favor, insira o Número de Telefone']"
          no-error-icon
        />
  
        <div class="flex column">
          <q-btn label="Limpar" type="reset" color="secondary" flat  />
          <q-btn label="Atualizar"  @click="update_user_data" color="secondary" />
        </div>
        </q-form>
      </div>
      </q-page>
  </template>
  <script>
      export default {
          data() {
              return {
              email:'',
              birthdate:'',
              full_name:'',
              phone: '',
              cpf: '',
              emergency_name:'',
              emergency_number:''
              };
          },
          mounted() {
          this.check_login_status();
          this.get_user_data();
  
          },
          methods: {
              check_login_status() {
        if (!localStorage.getItem('access_token')) {
          alert('Not logged in');
          this.$router.push('/login');
        }
      },
      async update_user_data(){
  
      fetch(`http://localhost:5510/v1/user/logged/`, {
          method: 'PUT',
          headers: {
          'Content-Type': 'application/json',
          'token': localStorage.getItem('access_token')
    },
    body: JSON.stringify({
      email:this.email,
      birthdate: this.birthdate,
      full_name: this.full_name,
      phone: this.phone,
      cpf: this.cpf,
      emergency_name: this.emergency_name,
      emergency_number: this.emergency_number,
  
    }),
  })
  .then(response => {
    if (!response.ok) {
      throw new Error('Por favor, preencha todos os campos.');
    }
    
    this.showConfirmation = true;
    this.$router.push(`/user/manage`);
  })
  .catch(error => {
    // this.$q.notify({
    //   color: 'red-4',
    //   textColor: 'white',
    //   icon: 'cloud_done',
    //   message: error.message,
    // });
  });
  
  
  },
  async get_user_data() {
        fetch(`http://localhost:5510/v1/user/logged/`, {
          method:'GET',
          headers:{'token': localStorage.getItem('access_token')}})
          .then(response => {
            if (!response.ok) {
              throw new Error('Network response was not ok');
            }
            return response.json();
          })
          .then(data => {
            // Sync user object with API data.
            this.phone=data.user.phone;
            this.cpf=data.user.cpf;
            this.emergency_name=data.user.emergency_name;
            this.emergency_number=data.user.emergency_number;
            this.full_name=data.user.full_name;
            this.email=data.user.email;
            this.birthdate=data.user.birthdate;
          })
          .catch(error => {
            console.error('Error fetching data:', error);
  
            // this.$q.notify({
            //   color: 'red-5',
            //   textColor: 'white',
            //   icon: 'cloud_done',
            //   message: 'Ops! Falha ao carregar dados.'
            // });
          });
      }, 
  }
  };
  </script>
  <style>
  </style>