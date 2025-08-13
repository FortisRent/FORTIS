<template>
  <div class="q-pa-md" style="max-width: 650px">
    <h5 class="q-mb-xl">Novo usuário</h5>
    <q-form @submit="on_submit" @reset="on_reset" class="q-gutter-md">
      <q-input
        filled
        dark
        v-model="name"
        label="Nome *"
        hint="Nome e sobrenome"
        lazy-rules
        :rules="[ val => val && val.length > 0 || 'Por favor, insira o seu nome.']"
      />

      <q-input
        filled
        dark
        v-model="cpf"
        label="CPF *"
        lazy-rules
        :rules="[ val => val && val.length > 0 || 'Por favor, insira o seu cpf.']"
      />

      <q-input
        filled
        dark
        type="date"
        v-model="birthdate"
        label="Nascimento *"
        hint="Data de nascimento"
        lazy-rules
        :rules="[ val => val && val.length > 0 || 'Por favor, insira sua data de nascimento.']"
      />

      <q-input
        filled
        dark
        type="email"
        v-model="email"
        label="Email *"
        lazy-rules
        :rules="[ val => val && val.length > 0 || 'Por favor, insira o seu e-mail.']"
      />

      <q-input
        filled
        dark
        v-model="phone"
        label="Celular *"
        lazy-rules
        :rules="[ val => val && val.length > 0 || 'Por favor, insira o seu número.']"
      />

      <city_select v-model="city_id" />

      <q-input
        filled
        dark
        type="number"
        v-model="gender_id"
        label="Gênero *"
        hint="Gênero"
        lazy-rules
        :rules="[ val => val && val.length > 0 || 'Please type something']"
      />

      <q-input
        filled
        dark
        type="password"
        v-model="password"
        label="Senha *"
        hint="Deve conter um número, uma letra e no mínimo 6 caracteres."
        lazy-rules
        :rules="[ val => val && val.length > 0 || 'Por favor, insira a sua senha.']"
      />

      <q-toggle v-model="accept" label="Eu aceito os termos de uso." />

      <div>
        <q-btn label="Limpar" type="reset" color="primary" flat class="q-ml-sm" />
        <q-btn label="Cadastrar" type="submit" color="primary" />
      </div>
    </q-form>
  </div>
</template>

<script>
import CitySelect from 'src/components/CitySelect.vue';
import { useQuasar } from 'quasar';
import { useRouter } from 'vue-router';
import { ref } from 'vue';

export default {
  components: {
    city_select: CitySelect,
  },
  setup() {
    const $q = useQuasar();
    const router = useRouter();

    const name = ref(null);
    const cpf = ref(null);
    const birthdate = ref(null);
    const email = ref(null);
    const phone = ref(null);
    const city_id = ref(null);
    const gender_id = ref(null);
    const password = ref(null);
    const accept = ref(false);

    const on_submit = async () => {
      if (!accept.value) {
        $q.notify({
          color: 'red-5',
          textColor: 'white',
          icon: 'warning',
          message: 'Você precisa aceitar os termos de uso.',
        });
      } else {
        try {
          // Perform fetch POST request
          const response = await fetch('http://localhost:5510/v1/user', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              name: name.value,
              cpf: cpf.value,
              birthdate: birthdate.value,
              email: email.value,
              phone: phone.value,
              city_id: city_id.value,
              gender_id: gender_id.value,
              password: password.value,
              is_admin: false,
            }),
          });

          if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || 'Erro ao criar usuário');
          }

          $q.notify({
            color: 'green-4',
            textColor: 'white',
            icon: 'cloud_done',
            message: 'Usuário criado!',
          });

          router.push('/user');
        } catch (error) {
          $q.notify({
            color: 'red-4',
            textColor: 'white',
            icon: 'cloud_done',
            message: error.message,
          });
        }
      }
    };

    const on_reset = () => {
      name.value = null;
      cpf.value = null;
      birthdate.value = null;
      email.value = null;
      phone.value = null;
      password.value = null;
      accept.value = false;
    };

    return {
      name,
      cpf,
      birthdate,
      email,
      phone,
      city_id,
      gender_id,
      password,
      accept,
      on_submit,
      on_reset,
    };
  },
};
</script>
