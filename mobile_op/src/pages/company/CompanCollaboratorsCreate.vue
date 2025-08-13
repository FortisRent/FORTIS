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
          Adicionar Funcionário
        </div>
      </div>
  
      <q-separator class="bg-grey" />
  
      <div class="q-pa-md">
        <q-form @submit.prevent="create_employee" class="q-gutter-md q-pa-md">
          <q-input
            rounded
            color="secondary"
            v-model="cpf"
            label="CPF"
            mask="###.###.###-##"
            lazy-rules
            :rules="[val => val && val.length > 0 || 'Por favor, insira o CPF']"
            no-error-icon
          />
          <q-input
							rounded
							color="secondary"
							v-model="hourly_price"
							label="Valor por Hora Trabalhada"
							lazy-rules
							mask="R$ ###.###,##"
							reverse-fill-mask
							unmasked-value
							:rules="[val => val && val.length > 0 || 'Por favor, insira o Preço por Distância']"
							no-error-icon
						/>
            <q-input
							rounded
							color="secondary"
							v-model="ctps_number"
							label="Número Carteira de Trabalho"
							lazy-rules
							:rules="[val => val && val.length > 0 || 'Por favor, insira o Número Carteira de Trabalho']"
							no-error-icon
						/>
  
          <q-select
            filled
            v-model="selected_role"
            :options="role_options"
            option-value="uuid"
            option-label="name"
            label="Cargo*"
          />
  
          <div class="flex column">
            <q-btn label="Adicionar" type="submit" color="secondary" />
          </div>
        </q-form>
      </div>
    </q-page>
  </template>
  
  <script>
  export default {
    data() {
      return {
        cpf: '',
        selected_role: null,
        role_options: [],
        hourly_price:'',
        ctps_number:'',
        company_uuid: this.$route.params.company_uuid // Obtendo o UUID da rota
      };
    },
    mounted() {
      this.check_login_status();
      this.get_roles();
    },
    methods: {
      check_login_status() {
        if (!localStorage.getItem('access_token')) {
          alert('Não está logado');
          this.$router.push('/login');
        }
      },
      async get_roles() {
        fetch('http://localhost:5510/v1/role/', {
          method: 'GET',
          headers: { 'token': localStorage.getItem('access_token') }
        })
        .then(response => {
          if (!response.ok) throw new Error('Erro ao buscar cargos');
          return response.json();
        })
        .then(data => {
          this.role_options = data.roles; // Define as opções no select
        })
        .catch(error => {
          console.error('Erro ao buscar dados:', error);
          this.$q.notify({
            color: 'red-5',
            textColor: 'white',
            icon: 'error',
            message: 'Erro ao carregar os cargos'
          });
        });
      },
      async create_employee() {
        if (!this.cpf || !this.selected_role) {
          this.$q.notify({
            color: 'red-5',
            textColor: 'white',
            icon: 'error',
            message: 'Preencha todos os campos!'
          });
          return;
        }
  
        fetch('http://localhost:5510/v1/employee/', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'token': localStorage.getItem('access_token')
          },
          body: JSON.stringify({
            company_uuid: this.company_uuid,
            role_name: this.selected_role.name, // Pegando o nome do cargo
            cpf: this.cpf,
            ctps_number: this.ctps_number,
            hourly_price: this.hourly_price,
          })
        })
        .then(response => {
          if (!response.ok) throw new Error('Erro ao adicionar funcionário');
          this.$router.push('/user/manage');
          this.$q.notify({
            color: 'green-5',
            textColor: 'white',
            icon: 'check',
            message: 'Funcionário adicionado com sucesso!'
          });
        })
        .catch(error => {
          console.error('Erro ao cadastrar funcionário:', error);
          this.$q.notify({
            color: 'red-5',
            textColor: 'white',
            icon: 'error',
            message: 'CPF não cadastrado, por favor cadastre o funcionário no sistema.'
          });
        });
      }
    }
  };
  </script>
  
  <style scoped>
  .text-truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
    max-width: 100%;
  }
  .card-style {
    border-radius: 10px;
    max-width: 350px;
  }
  </style>
  