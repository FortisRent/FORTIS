<template>
	<q-layout class="bg-white">
		<div class="q-pa-md flex column items-center">
			<p class="text-weight text-h6 text-primary q-pa-sm" style="margin-right: 100px;">Cadastrar novo usuário</p>
			<q-form @submit="on_submit" class="q-gutter-md">
				<q-input
					filled
					color="secondary"
					v-model="full_name"
					label="Nome completo"
					lazy-rules
					no-error-icon
					:rules="[ val => val && val.length > 0 || 'Por favor, insira o seu nome.']"
					class="q-mt-lg"
				/>

				<q-input
					filled
					color="secondary"
					v-model="phone"
					label="Celular *"
					mask="(##) # ####-####"
					lazy-rules
					no-error-icon
					:rules="[ val => val && val.length > 0 || 'Por favor, insira o seu número.']"
				/>

			  	<q-input
					filled
					color="secondary"
					:type="passwordVisible ? 'text' : 'password'"
					v-model="password"
					label="Senha *"
					hint="Deve conter um número, uma letra, um caractere especial e no mínimo 8 caracteres."
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
  	</q-layout>
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
				  await axios.post('http://localhost:5510/v1/user/', {
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
			  passwordVisible,
			  confirmPasswordVisible,
			  on_submit,
			  openTerms
		  };
	  },
  };
</script>
