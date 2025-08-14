<template>
  <q-layout class="bg-white">
    <q-btn
      flat
      round
      icon="arrow_back"
      class="q-mr-sm q-mt-md text-primary"
      color="black"
      style="position: absolute; left: 10px;"
      @click="$router.go(-1)"
    />

    <div class="q-pa-md flex column items-center">
      <q-img
        src="../../../assets/fortis4.png"
        style="min-height: 350px;"
      />
      <p class="text-primary text-h6 text-center" style="margin-top: -50px;"  >Nova Senha</p>
        <q-form @submit="onSubmit" @reset="onReset" class="q-gutter-md">
          <q-input
            outlined
            lazy-rules
            :rules="[
              val => !!val || 'A senha deve ter no mínimo 6 caracteres.',
              val => val.length >= 6 || 'A senha deve ter no mínimo 6 caracteres.',
              val => /[a-zA-Z]/.test(val) || 'A senha deve conter pelo menos uma letra.',
              val => /\d/.test(val) || 'A senha deve conter pelo menos um número.'
            ]"
            color="secondary"
            v-model="password"
            :type="isPwd ? 'password' : 'text'"
            clear-icon
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

          <q-input
            outlined
            lazy-rules
            v-model="password2"
            :rules="[val => val && val.length > 0 || 'Por favor, repita a sua senha.']"
            color="secondary"
            :type="isPwd2 ? 'password' : 'text'"
            clear-icon
            no-error-icon
          >
            <template v-slot:append>
              <q-icon
                :name="isPwd2 ? 'visibility_off' : 'visibility'"
                class="cursor-pointer"
                @click="isPwd2 = !isPwd2"
              />
            </template>
          </q-input>

          <div class="flex justify-center">
            <q-btn
              label="Alterar a Senha"
              type="submit"
              color="black"
              style="width: 260px;"
            />
          </div>
        </q-form>
      </div>
  </q-layout>
</template>

<script>
export default {
  name: "LoginView",
  data() {
    return {
      code: null,
      password: null,
      password2: null,
      isPwd: true,
      isPwd2: true,
    };
  },
  methods: {
    async onSubmit() {
      try {
        const response = await fetch("https://fortis-api.55technology.com/v1/password/update", {
          method: "PUT",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            email: localStorage.getItem("update_email"),
            password: this.password,
          }),
        });

        const data = await response.json();

        if (response.status === 200) {
          // Notificação de sucesso
          this.$q.notify({
            color: "green-4",
            textColor: "white",
            icon: "check_circle",
            message: data.message,
          });

          this.$router.push({ path: "/login" });
        } else {
          this.$q.notify({
            color: "red-4",
            textColor: "white",
            icon: "error",
            message: data.message || "Erro ao atualizar a senha.",
          });
        }
      } catch (error) {
        this.$q.notify({
          color: "red-4",
          textColor: "white",
          icon: "error",
          message: error.message || "Erro ao conectar ao servidor.",
        });
      }
    },
  },
};
</script>

<style>
.rounded {
  border-radius: 40px;
  background-color: white;
}
</style>
