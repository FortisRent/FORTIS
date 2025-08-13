<template>
	<q-layout class="bg-white">
	  <div class="q-pa-md flex column items-center">
      <q-img
        src="../../../assets/fortis4.png"
        style="min-height: 350px;"
      />
		
      <div>
        <p class="text-primary text-bold text-h6" style="margin-top: -50px;">
        Bem vindo a Fortis
        </p>
      </div>
		
		<q-form @submit="onSubmit" @reset="onReset" class="q-gutter-md">
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
				outlined
				:type="isPwd ? 'password' : 'text'"
				v-model="password"
				label="Senha"
				color="secondary"
				lazy-rules
				:rules="[val => val && val.length > 0 || 'Por favor, insira a sua senha.']" 
				no-error-icon
			>
			<template v-slot:append>
			  <q-icon
				:name="isPwd ? 'visibility_off' : 'visibility'"
				class="cursor-pointer"
				@click="isPwd = !isPwd"
			  />
			</template>
		  </q-input>
		  <div class="flex justify-center q-mb-md">
			<q-btn
			  label="Entrar com a Fortis"
			  type="submit"
			  color="black"
			  style="width: 260px;"
			/>
			<!-- <q-btn
			  class="q-mt-sm text-primary"
			  color="white"
			  style="width: 260px;"
			>
			  <span>
				Entrar com o
				<span style="color: #4285F4;">G</span>
				<span style="color: #EA4335;">o</span>
				<span style="color: #FBBC05;">o</span>
				<span style="color: #4285F4;">g</span>
				<span style="color: #34A853;">l</span>
				<span style="color: #EA4335;">e</span>
			  </span>
			</q-btn> -->
  
			<q-btn
			  label="Criar uma conta" 
			  to="/register" 
			  color="black" 
			  class="q-mt-sm"
			  style="width: 260px;"
			/>
		  </div>
		  <a
			class="text-primary"  
			style="text-decoration: underline;"
			href="/password/email"
		  >
			Esqueceu a senha? 
		  </a>
		</q-form>
	  </div>
	  <div
		class="bg-secondary q-mt-md"
		style="border-radius: 10px; margin-left: 160px;"
	  >
	  </div>
	</q-layout>
  </template>
  
  <script>
  import { useQuasar } from "quasar";
  import { useRouter } from "vue-router";
  import { ref } from "vue";
  
  export default {
	name: "LoginView",
	setup() {
	  const $q = useQuasar();
	  const router = useRouter();
  
	  const phone = ref(null);
	  const password = ref(null);
	  const isPwd = ref(true); // Controle da exibição da senha
	  const accept = ref(false);
  
	  const onSubmit = async () => {
		try {
		  const response = await fetch("http://localhost:5510/v1/login", {
			method: "POST",
			headers: {
			  "Content-Type": "application/json",
			  Authorization: "Basic " + btoa(`${phone.value}:${password.value}`), 
			},
		  });
  
		  const data = await response.json(); 
  
		  if (response.ok) {
			localStorage.setItem("access_token", data.access_token);
  
			let jwt = data.access_token;
  
			const parts = jwt.split(".");
			const payload = JSON.parse(atob(parts[1]));
  
			localStorage.setItem("user_phone", payload.phone);
			localStorage.setItem("user_name", payload.name);
			localStorage.setItem("is_admin", payload.is_admin);
  
			if (payload.is_admin) {
			  router.push("/admin/dash");
			} else {
			  router.push("/user/service");
			}
		  } else {
			$q.notify({
			  color: "red-4",
			  textColor: "white",
			  icon: "cloud_done",
			  message: data.message.toString(), 
			});
		  }
		} catch (error) {
		  $q.notify({
			color: "red-4",
			textColor: "white",
			icon: "cloud_done",
			message: error.message || "Erro ao fazer login.",
		  });
		}
	  };
  
	  const onReset = () => {
		phone.value = null;
		password.value = null;
		accept.value = false;
	  };
  
	  return {
		phone,
		password,
		isPwd,
		accept,
		onSubmit,
		onReset,
	  };
	},
  };
</script>
  
<style>
	.rounded {
		border-radius: 40px;
		background-color: white;
	}
</style>
  