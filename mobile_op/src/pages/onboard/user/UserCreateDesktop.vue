<template>
  <div class="container-register-desktop reset-global-styles">
    <div class="container-form">
      <q-img
        src="../../../assets/fortis4.png"
        fit="cover"
        width="100%"
        height="150%"
      />

      <q-form @submit="on_submit" @reset="on_reset" class="form-register-desktop">
        <h2 class="text-primary text-bold text-h5 text-center" style="margin-top: -30px;">
          Cadastrar novo usuário
        </h2>

        <q-input
          style="width: 100%;"
          class="reset-global-styles"
          filled
          color="secondary"
          bg-color="white"
          v-model="full_name"
          label="Nome Completo *"
          lazy-rules
          no-error-icon
          :rules="[ val => val && val.length > 0 || 'Por favor, insira o seu email.']"
        />

        <q-input
          style="width: 100%;"
          class="reset-global-styles"
          filled
          color="secondary"
          bg-color="white"
          v-model="phone"
          label="Celular *"
					mask="(##) # ####-####"
          lazy-rules
          no-error-icon
          :rules="[ val => val && val.length > 0 || 'Por favor, insira o seu email.']"
        />

        <q-input
					filled
          style="width: 100%;"
					color="secondary"
					:type="passwordVisible ? 'text' : 'password'"
					v-model="password"
					label="Senha *"
					hint="Deve conter um número, uma letra e no mínimo 6 caracteres."
					lazy-rules
					no-error-icon
					:rules="[
						val => !!val || 'Por favor, insira a sua senha.',
						val => val.length >= 8 || 'A senha deve ter no mínimo 8 caracteres.',
						val => /[a-zA-Z]/.test(val) || 'A senha deve conter pelo menos uma letra.',
						val => /\d/.test(val) || 'A senha deve conter pelo menos um número.',
            val => /[!@#$%^&*()\[\]{}\\|<>?/._+=-]/.test(val) || 'A senha deve conter pelo menos um caractere especial.'
					]"
        >
        <template v-slot:append>
						<q-icon 
							:name="passwordVisible ? 'visibility' : 'visibility_off'" 
							class="cursor-pointer" 
							@click="passwordVisible = !passwordVisible"
						/>
					</template>
				</q-input>
        
				<q-input
					filled
          style="width: 100%;"
					color="secondary"
					:type="confirmPasswordVisible ? 'text' : 'password'"
					v-model="confirm_password"
					label="Confirme sua senha *"
					lazy-rules
					class="q-pt-md"
					no-error-icon
					:rules="[ val => val && val.length > 0 || 'Por favor, confira a sua senha.']"
				>
					<template v-slot:append>
						<q-icon 
							:name="confirmPasswordVisible ? 'visibility' : 'visibility_off'" 
							class="cursor-pointer" 
							@click="confirmPasswordVisible = !confirmPasswordVisible"
						/>
					</template>
				</q-input>

				<q-toggle class="text-primary" color="secondary" v-model="accept" label="Eu aceito os termos de uso." />

				<q-slide-transition>
					<div v-show="accept" class="q-mt-md text-center">
						<q-btn 
							label="Ler os Termos de Uso" 
							color="secondary" 
							outline 
							@click="openTerms"
						/>
					</div>
				</q-slide-transition>

				<div class="flex column">
					<q-btn label="Cadastrar" type="submit" color="black" class="q-mt-sm" />
					<a href="/login" class="text-primary q-ma-md" style="text-decoration: underline;">Já possui uma conta? Faça o login</a>
				</div>
      </q-form>
    </div>

    <div class="container-register-desktop-img"></div>
  </div>
</template>

<script>
  import { useQuasar } from 'quasar';
  import { useRouter } from 'vue-router';
  import { ref } from 'vue';
  import axios from 'axios';

  export default {
		setup() {
		  const $q = useQuasar();
		  const router = useRouter();

		  const full_name = ref(null);
		  const phone = ref(null);
		  const password = ref(null);
		  const confirm_password = ref(null);
		  const accept = ref(false);
      const passwordVisible = ref(false);
		  const confirmPasswordVisible = ref(false);

		  const on_submit = async () => {
			  if (!accept.value) {
				  $q.notify({
					  color: 'red-5',
					  textColor: 'white',
					  icon: 'warning',
					  message: 'Você precisa aceitar os termos de uso.',
				  });

				  return;
			  } 
		  
			  if(password.value !== confirm_password.value) {
				  $q.notify({
					  color: 'red-5',
					  textColor: 'white',
					  icon: 'warning',
					  message: 'A confirmação de senha está incorreta.',
				  });

				  return;
			  }

			  try {
				  await axios.post('https://fortis-api.55technology.com/v1/user/', {
					  full_name: full_name.value,
					  phone: phone.value,
					  password: password.value,
				  });

				  $q.notify({
					  color: 'green-4',
					  textColor: 'white',
					  icon: 'cloud_done',
					  message: 'Usuário criado!',
				  });

				  router.push('/login');
			  } catch (error) {
				  $q.notify({
					  color: 'red-4',
					  textColor: 'white',
					  icon: 'cloud_done',
					  message: error.response.data.message
				  });
			  }
		  };
      const openTerms = () => {
			  window.open('/terms', '_blank');
		  };
		  return {
			  full_name,
			  phone,
			  password,
			  confirm_password,
			  accept,
			  on_submit,
        openTerms
		  };
	  },
  };
</script>

<style scoped lang="scss">
  .reset-global-styles{
    padding: 0;
    margin: 0;
    box-sizing: border-box;
  }

  .container-register-desktop {
    background: linear-gradient(to right, #303940, #11161a);
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    grid-template-rows: 100vh;
    align-items: center;
    overflow-x: hidden;
    
    .container-register-desktop-img { 
      background-image: url("../../../assets/presentation.svg");
      background-size: contain;
      background-repeat: no-repeat;
      background-position: center;

      margin-left: 1rem;
      width: 100%;
      height: 100%;
    }

    .container-form {
      display: grid;
      min-width: 450px;
      padding: 0 2rem 2rem 2rem;
      box-shadow: 0 3px 10px 0 rgba(0, 0, 0, .14);
      background-color: #fff;
      border-radius: 5px;
      justify-self: center;

      grid-template-columns: 1fr;
      align-items: center;
      justify-content: center;

      .form-register-desktop {
        display: flex;
        flex-direction: column;
        gap: 1rem;

        justify-content: center;
        align-items: center;

        .buttons-register-desktop{
          display: grid;
          grid-template-columns: 1fr 1fr 1fr;
          gap: 1rem;
          justify-content: space-between;
        }
      }
    }
  }

  /* Responsivo */

  @media screen and (max-width: 1000px) {
    .container-register-desktop{
      grid-template-columns: 1fr;
    }
  }
</style>
