<template>
  <q-layout class="bg-white">
    <div class="q-pa-md flex column items-center">
      <q-img
        src="../../../assets/fortis4.png"
        style="min-height: 350px;"
      />
      <p class="text-primary text-h6 text-center">Qual seu Token de Verificação</p>
      <div
        class="q-pa-md"
      >
        <q-form @submit="onSubmit" @reset="onReset" class="q-gutter-md">
          <q-input
            outlined
            v-model="code"
            label="Token"
            color="secondary"
            lazy-rules
            :rules="[val => val && val.length > 0 || 'Por favor, insira o seu token.']" 
            no-error-icon
          />

          <div class="flex justify-center q-mb-md">
            <q-btn
              label="Validar"
              type="submit"
              color="black"
              style="width: 260px;"
            />
          </div>
          <a href="/login" class="text-primary">
            Voltar para o inicio
          </a>
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
      code: null,
    };
  },
  methods: {
    async onSubmit() {
      try {
        const response = await fetch("http://localhost:5510/v1/password/code", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({ code: this.code }),
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

          this.$router.push({ path: '/password/update' });
        } else {
          this.$q.notify({
            color: "red-4",
            textColor: "white",
            icon: "error",
            message: data.message || "Erro ao verificar token.",
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
