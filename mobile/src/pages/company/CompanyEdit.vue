<template>
  <q-page class="bg-white">
    <div class="row justify-around items-center q-pa-md q-mt-lg">
      <q-btn flat round icon="chevron_left" class="text-secondary" color="secondary" size="18px"
        style="position: absolute; left: 10px;" @click="$router.go(-1)" />
      <div class="text-h6 text-primary text-bold q-ml-md">
        Editar Dados da Empresa
      </div>
    </div>

    <q-separator class="bg-grey" />

    <div class="q-pa-md">
      <q-form @submit="on_submit" @reset="on_reset" class="q-gutter-md q-pa-md">
        <q-input rounded color="secondary" v-model="name" label="Nome da Empresa" lazy-rules
          :rules="[val => val && val.length > 0 || 'Por favor, insira o País']" no-error-icon />
        <q-input rounded color="secondary" v-model="cnpj" mask="##.###.###/####-##" label="CNPJ" lazy-rules
          :rules="[val => val && val.length > 0 || 'Por favor, insira o CNPJ']" no-error-icon />
        <q-input color="secondary" class="q-mt-sm" v-model="zip_code" label="CEP" @blur="get_address_by_cep" lazy-rules
          :rules="[val => val && val.length > 0 || 'Por favor, insira o Código Postal']" no-error-icon />

        <q-input color="secondary" class="q-mt-sm" v-model="street" label="Rua" lazy-rules
          :rules="[val => val && val.length > 0 || 'Por favor, insira a Rua']" no-error-icon />

        <q-input color="secondary" class="q-mt-sm" v-model="number_street" label="Número" lazy-rules
          :rules="[val => val && val.length > 0 || 'Por favor, insira o Número']" no-error-icon />
        <q-input color="secondary" class="q-mt-sm" v-model="complement" label="Complemento" lazy-rules
          :rules="[val => val && val.length > 0 || 'Por favor, insira o Complemento']" no-error-icon />

        <q-input color="secondary" class="q-mt-sm" v-model="neighborhood" label="Bairro" lazy-rules
          :rules="[val => val && val.length > 0 || 'Por favor, insira o Bairro']" no-error-icon />

        <q-input color="secondary" class="q-mt-sm" v-model="city_name" label="Cidade" lazy-rules
          :rules="[val => val && val.length > 0 || 'Por favor, insira a Cidade']" no-error-icon />

        <q-input color="secondary" class="q-mt-sm" v-model="state_name" label="UF" lazy-rules
          :rules="[val => val && val.length > 0 || 'Por favor, insira o Estado']" no-error-icon />
        <div class="flex column">
          <q-btn label="Limpar" type="reset" color="secondary" flat />
          <q-btn label="Atualizar" @click="update_company" color="secondary" />
        </div>
      </q-form>
    </div>
  </q-page>
</template>
<script>
export default {
  data() {
    return {
      cnpj: '',
      name: '',
      street: '',
      number_street: '',
      complement: '',
      neighborhood: '',
      city_name: '',
      state_name: '',
    };
  },
  mounted() {
    this.check_login_status();
    this.get_company_by_uuid();

  },
  methods: {
    check_login_status() {
      if (!localStorage.getItem('access_token')) {
        alert('Not logged in');
        this.$router.push('/login');
      }
    },
    async update_company() {

      fetch(`http://localhost:5510/v1/company/${this.$route.params.company_uuid}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'token': localStorage.getItem('access_token')
        },
        body: JSON.stringify({
          name: this.name,
          cnpj: this.cnpj,
          zip_code: this.zip_code,
          street: this.street,
          number_street: this.number_street,
          complement: this.complement,
          neighborhood: this.neighborhood,
          city_name: this.city_name,
          state_name: this.state_name,

        }),
      })
        .then(response => {
          if (!response.ok) {
            throw new Error('Por favor, preencha todos os campos.');
          }

          this.showConfirmation = true;
          this.$router.push(`/user/company`);
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
    async get_company_by_uuid() {
      fetch(`http://localhost:5510/v1/company/${this.$route.params.company_uuid}`, {
        method: 'GET',
        headers: { 'token': localStorage.getItem('access_token') }
      })
        .then(response => {
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          return response.json();
        })
        .then(data => {
          console.table(data)
          this.name = data.company.name;
          this.cnpj = data.company.cnpj;
          this.zip_code = data.company.zip_code;
          this.street = data.company.street;
          this.number_street = data.company.number_street;
          this.complement = data.company.complement;
          this.neighborhood = data.company.neighborhood;
          this.city_name = data.company.city_name;
          this.state_name = data.company.state_name;

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
    async get_address_by_cep() {
      if (!this.zip_code || this.zip_code.length !== 8) {
        this.$q.notify({
          color: 'red-5',
          textColor: 'white',
          icon: 'warning',
          message: 'Por favor, insira um CEP válido com 8 dígitos.'
        });
        return;
      }

      try {
        const response = await fetch(`https://viacep.com.br/ws/${this.zip_code}/json/`);
        if (!response.ok) throw new Error('Erro ao buscar CEP');

        const data = await response.json();

        if (data.erro) {
          this.$q.notify({
            color: 'red-5',
            textColor: 'white',
            icon: 'error',
            message: 'CEP não encontrado.'
          });
          return;
        }

        // Preenchendo os campos com os dados retornados
        this.street = data.logradouro || '';
        this.neighborhood = data.bairro || '';
        this.city_name = data.localidade || '';
        this.state_name = data.uf || '';
        this.number_street = data.complemento || '';

        this.$q.notify({
          color: 'green-4',
          textColor: 'white',
          icon: 'check',
          message: 'Endereço preenchido automaticamente!'
        });

      } catch (error) {
        console.error('Erro ao buscar endereço:', error);
        this.$q.notify({
          color: 'red-5',
          textColor: 'white',
          icon: 'error',
          message: 'Erro ao buscar o endereço. Tente novamente.'
        });
      }
    },
  }
};
</script>
<style></style>