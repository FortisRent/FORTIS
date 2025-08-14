<template>
	  <div class="container-login-desktop reset-global-styles">
      <div class="container-form">
        <q-img
          src="../../../assets/fortis4.png"
          fit="cover"
          width="100%"
          height="150%"
        />

        <q-form @submit="onSubmit" class="form-login-desktop">
          <h2 class="text-primary text-bold text-h5 text-center" style="margin-top: -30px;">
            Bem vindo a Fortis
          </h2>

          <q-input
            style="width: 100%;"
            outlined
            color="secondary"
            bg-color="white"
            v-model="phone"
            label="Número Celular *"
            mask="(##) # ####-####"
            lazy-rules
            no-error-icon
            :rules="[ val => val && val.length > 0 || 'Por favor, insira o seu Número de Celular.']"
          />

          <q-input
            style="width: 100%;"
            outlined
            bg-color="white"
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

          <q-btn
            label="Entrar"
            type="submit"
            color="black"
            style="
              width: 100%;
              padding: 0.5rem;
              margin-bottom: 0.5rem;
            "
          />

          <a
            class="text-primary"  
            style="text-decoration: underline; align-self: flex-start;"
            href="/password/phone"
            >
            Esqueceu a senha?
          </a>

          <div class="divider-login-desktop">
            <div></div>
            <span>ou</span>
            <div></div>
          </div>

          
          <div class="text-primary text-h7">
            Novo na Fortis? 
            <span>
              <a 
                href="/register" class="text-secondary text-bold"  
                style="text-decoration: none; align-self: flex-start;"
              >
                Cadastrar
              </a>
            </span>
          </div>

        </q-form>
      </div>

      <div class="container-login-desktop-img"></div>
    </div>
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
    const response = await fetch("https://fortis-api.55technology.com/v1/login", {
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
  
<style scoped lang="scss">
  .reset-global-styles{
    padding: 0;
    margin: 0;
    box-sizing: border-box;
  }

  .container-login-desktop {
    background: linear-gradient(to right, #303940, #11161a);
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    grid-template-rows: 100vh;
    align-items: center;
    overflow-x: hidden;
    
    .container-login-desktop-img { 
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
      min-width: 500px;
      padding: 0 2rem 2rem 2rem;
      gap: 1rem;
      box-shadow: 0 3px 10px 0 rgba(0, 0, 0, .14);
      background-color: #fff;
      border-radius: 5px;
      justify-self: center;

      grid-template-columns: 1fr;
      align-items: center;
      justify-content: center;

      .form-login-desktop {
        display: flex;
        flex-direction: column;
        gap: 1rem;

        justify-content: center;
        align-items: center;

        .divider-login-desktop {
          display: flex;
          align-items: center;
          justify-content: center;
          gap: 20px;
          width: 100%;
          font-size: 14px;
          color: #dbdbdb;
          font-weight: 400;
          text-transform: uppercase;

          div {
            flex-grow: 1;
            height: 2px;
            background-color: #dbdbdb;
          }
        }

        .buttons-login-desktop{
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
    .container-login-desktop{
      grid-template-columns: 1fr;
    }
  }
</style>
  