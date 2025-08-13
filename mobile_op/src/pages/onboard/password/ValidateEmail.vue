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
      <p class="text-primary text-h6 text-center">
        Qual seu email para a recuperação da senha?
      </p>
      <div
        class="q-pa-md"
      >
        <q-form @submit="onSubmit"  class="q-gutter-md">
          <q-input
            outlined
            type="email"
            v-model="email"
            label="Email"
            color="secondary"
            lazy-rules
            :rules="[val => val && val.length > 0 || 'Por favor, insira o seu e-mail.']"
            no-error-icon
          />
          <div class="flex justify-center">
            <q-btn
              label="Verificar E-mail"
              type="submit"
              color="black"
              style="width: 260px;"
            />
          </div>
        </q-form>
      </div>
    </div>
  </q-layout>
</template>

<script>
export default {
  name: "LoginView",
  data() {
    return {
      email: null,
    };
  },
  methods: {
    async onSubmit() {
      try {
        const response = await fetch("http://localhost:5510/v1/password/email", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({ email: this.email }),
        });

        const data = await response.json();

        if (response.status === 200) {
          this.$q.notify({
            color: "green-4",
            textColor: "white",
            icon: "check_circle",
            message: data.message,
          });

          localStorage.setItem("update_email", this.email);

          this.$router.push({ path: "/password/code" });
        } else {
          this.$q.notify({
            color: "red-4",
            textColor: "white",
            icon: "error",
            message: data.message || "Erro ao verificar email.",
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
