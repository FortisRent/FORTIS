<template>
  <q-layout class="q-pa-md bg-grey-2">
    <q-card class="q-pa-lg" style="max-width: 1000px; margin: auto;">
      <q-card-section>
        <div class="text-h5 text-bold text-primary">Cadastro de Novo Cliente</div>
      </q-card-section>

      <q-separator />

      <q-form @submit.prevent="createClient" class="q-mt-lg">
        <div class="row q-col-gutter-md">
          <!-- COLUNA CLIENTE -->
          <div class="col-12 col-md-6">
            <q-input filled v-model="name" label="Nome *" hint="Nome e sobrenome"
              :rules="[val => val && val.length > 0 || 'Por favor, insira o nome.']" />

            <q-input filled v-model="cpf" label="CPF *" mask="###.###.###-##"
              :rules="[val => val && val.length > 0 || 'Por favor, insira o CPF.']" />

            <q-input filled v-model="birthdate" type="date" label="Nascimento *"
              :rules="[val => val && val.length > 0 || 'Por favor, insira a data de nascimento.']" />

            <q-input filled v-model="email" type="email" label="Email *"
              :rules="[val => val && val.length > 0 || 'Por favor, insira o e-mail.']" />

            <q-input filled v-model="phone" label="Celular *" mask="(##) # ####-####"
              :rules="[val => val && val.length > 0 || 'Por favor, insira o número.']" />

            <div class="row q-mt-md ">
              <q-btn label="Limpar" type="reset" color="primary" flat class="q-ml-sm" @click="clearForm" />
              <q-btn label="Cadastrar Cliente" type="submit" color="primary" class="q-ml-sm" />
            </div>
          </div>

          <!-- COLUNA ENDEREÇO -->
          <!-- <div class="col-12 col-md-6">
            <q-input filled v-model="street" label="Logradouro *"
              :rules="[val => val && val.length > 0 || 'Por favor, insira o logradouro.']" />

            <q-input filled v-model="neighborhood" label="Bairro *"
              :rules="[val => val && val.length > 0 || 'Por favor, insira o bairro.']" />

            <q-input filled v-model="city_name" label="Localidade *"
              :rules="[val => val && val.length > 0 || 'Por favor, insira a localidade.']" />

            <q-input filled v-model="state_name" label="UF *"
              :rules="[val => val && val.length > 0 || 'Por favor, insira a UF.']" />


            <q-input filled v-model="zip_code" label="Codigo postal *"
              :rules="[val => val && val.length > 0 || 'Por favor, insira a Codigo Postal.']" />

            <q-input filled v-model="complement" label="Complemento" />
          </div> -->
        </div>
      </q-form>
    </q-card>
  </q-layout>
</template>

<script>
export default {
  name: 'NovoCliente',

  data() {
    return {
      name: '',
      cpf: '',
      birthdate: '',
      email: '',
      phone: '',
      gender: '',
      password: '',
      street: '',
      neighborhood: '',
      city_name: '',
      state_name: '',
      complement: '',
      zip_code: '',
    };
  },

  methods: {
    async createClient() {
      try {
        const response = await fetch(
          `http://localhost:5510/v1/client/`,
          {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              token: localStorage.getItem('access_token'),
            },
            body: JSON.stringify({
              name: this.name,
              cpf: this.cpf,
              birthdate: this.birthdate,
              email: this.email,
              phone: this.phone,

              // street: this.street,
              // neighborhood: this.neighborhood,
              // city_name: this.city_name,
              // state_name: this.state_name,
              // complement: this.complement,
              // zip_code: this.zip_code,

              company_uuid: this.$route.params.company_uuid,
            }),
          }
        );

        if (!response.ok) {
          throw new Error('Erro ao cadastrar cliente. Verifique os dados.');
        }

        this.$q.notify({
          type: 'positive',
          message: 'Cliente cadastrado com sucesso!',
        });

        this.clearForm();
        this.$router.go(-1);

      } catch (error) {
        console.error('Erro no cadastro:', error);
        this.$q.notify({
          type: 'negative',
          message: error.message || 'Erro ao cadastrar cliente.',
        });
      }
    },

    clearForm() {
      this.name = '';
      this.cpf = '';
      this.birthdate = '';
      this.email = '';
      this.phone = '';
      this.street = '';
      this.neighborhood = '';
      this.city_name = '';
      this.state_name = '';
      this.complement = '';
      this.zip_code = '';
    },
  },
};
</script>

<style scoped>
.q-card {
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
  border-radius: 12px;
}
</style>
