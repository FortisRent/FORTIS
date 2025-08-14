<template>
  <q-layout class="bg-white">
    <div class="q-pa-md row">
      <!-- Coluna da lista de clientes -->
      <div class="col-6 flex column items-center" style="min-height: 96vh">
        <p class="text-h3 text-primary text-bold">Lista de clientes</p>

        <div class="q-pa-md full-width">
          <q-btn :to="`/dashboard/create/customers/${$route.params.company_uuid}`" icon="group_add" label="Novo cliente"
            text-color="black" class="q-mb-md" />
          <div v-for="cliente in clientes" :key="cliente.id" class="q-pa-sm q-mb-sm flex items-center cursor-pointer"
            style="border: 1px solid #ccc; border-radius: 12px;" @click="selecionarCliente(cliente)">
            <q-icon :name="cliente.icon" class="q-mr-sm" />
            <span class="text-subtitle1">{{ cliente.name }}</span>
          </div>
        </div>
      </div>

      <!-- Coluna do perfil -->
      <div class="col-6 flex column justify-center items-center bg-grey-4"
        style="border-radius: 10px; min-height: 96vh">
        <div v-if="clienteSelecionado">
          <div class="flex justify-center q-mb-md">
            <q-icon name="person" size="80px" color="black" />
          </div>

          <div class="text-center text-primary">
            <div class="flex items-center justify-center q-gutter-sm">
              <p class="text-bold text-h4 q-mb-none">
                {{ clienteSelecionado.name }}
              </p>
              <q-btn flat round dense icon="edit" color="black" @click="editandoPerfil = !editandoPerfil" />
              <q-btn flat round dense icon="delete" color="negative" @click="delete_client(clienteSelecionado)" />
            </div>

            <div v-if="editandoPerfil" class="q-mt-md">
              <q-input filled v-model="clienteSelecionado.name" label="Nome" class="q-mb-sm" />
              <q-input filled v-model="clienteSelecionado.email" label="Email" class="q-mb-sm" />
              <q-input filled v-model="clienteSelecionado.phone" mask="(##) # ####-####" label="Telefone" class="q-mb-sm" />
              <q-input filled v-model="clienteSelecionado.cpf" mask="###.###.###-##" label="CPF" class="q-mb-sm" />
              <q-btn color="positive" label="Salvar" class="q-mt-md" @click="update_client" />
            </div>

            <div v-else class="q-mt-md">
              <p>Email: {{ clienteSelecionado.email }}</p>
              <p>Telefone: {{ clienteSelecionado.phone }}</p>
              <p>CPF: {{ clienteSelecionado.cpf }}</p>
            </div>
          </div>
        </div>

        <div v-else>
          <div class="text-center">
            <p class="text-bold text-primary text-h4">Selecione um cliente</p>
          </div>
        </div>
      </div>
    </div>
  </q-layout>
</template>

<script>
import { useRoute } from 'vue-router';

export default {
  data() {
    return {
      clientes: [],
      clienteSelecionado: null,
      editandoPerfil: false,
      showConfirmation: false,
    };
  },
  methods: {
    selecionarCliente(cliente) {
      this.clienteSelecionado = cliente;
      this.editandoPerfil = false;
    },

    async get_list_operator() {
      try {
        const response = await fetch(
          `https://fortis-api.55technology.com/v1/client/company/${this.$route.params.company_uuid}`,
          {
            method: 'GET',
            headers: { token: localStorage.getItem('access_token') },
          }
        );

        if (!response.ok) {
          throw new Error('Erro ao buscar clientes.');
        }

        const data = await response.json();
        console.log(data);

        this.clientes = data.clients.map((item) => ({
          id: item.id,
          name: item.name,
          icon: 'person',
          email: item.email,
          phone: item.phone,
          cpf: item.cpf,
          user_uuid: item.uuid,
        }));
      } catch (error) {
        console.error('Erro ao buscar clientes:', error);
      }
    },

    async update_client() {
      try {
        const response = await fetch(
          `https://fortis-api.55technology.com/v1/client/${this.clienteSelecionado.user_uuid}`,
          {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
              token: localStorage.getItem('access_token'),
            },
            body: JSON.stringify({
              name: this.clienteSelecionado.name,
              email: this.clienteSelecionado.email,
              phone: this.clienteSelecionado.phone,
              cpf: this.clienteSelecionado.cpf,
            }),
          }
        );

        if (!response.ok) {
          throw new Error('Por favor, preencha todos os campos.');
        }

        this.showConfirmation = true;
        this.editandoPerfil = false;

      } catch (error) {
        console.error('Erro ao atualizar cliente:', error);
      }
    },

    async delete_client(cliente) {
      try {
        await this.$q.dialog({
          title: 'Confirmar exclusão',
          message: `Deseja realmente excluir o cliente "${cliente.name}"?`,
          cancel: true,
          persistent: true
        }).onOk(async () => {
          const response = await fetch(
            `https://fortis-api.55technology.com/v1/client/${cliente.user_uuid}`,
            {
              method: 'DELETE',
              headers: {
                'Content-Type': 'application/json',
                token: localStorage.getItem('access_token'),
              },
            }
          );

          if (!response.ok) {
            throw new Error('Erro ao excluir cliente.');
          }

          this.$q.notify({
            color: 'green-4',
            textColor: 'white',
            icon: 'delete',
            message: 'Cliente excluído com sucesso.',
          });

          this.get_list_operator();
          this.clienteSelecionado = null;
        });

      } catch (error) {
        console.error('Erro ao excluir cliente:', error);
        this.$q.notify({
          color: 'red-4',
          textColor: 'white',
          icon: 'error',
          message: error.message,
        });
      }
    },
  },
  mounted() {
    this.get_list_operator();
  },
};
</script>

<style scoped>
.video-container {
  width: 100%;
}
</style>
