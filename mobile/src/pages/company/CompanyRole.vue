<template>
  <q-layout class="bg-white">
    <div class="q-pa-md row">
      <!-- Coluna lateral de cargos-->
      <div class="col-6 flex column items-center" style="min-height: 96vh">
        <p class="text-h3 text-primary text-bold">Lista dos Cargos</p>

        <div class="q-pa-md full-width">
<!--          <q-btn-->
<!--            :to="`/dashboard/role/${this.$route.params.company_uuid}/create`"-->
<!--            icon="group_add"-->
<!--            label="Adicionar cargo"-->
<!--            text-color="black"-->
<!--            class="q-mb-md" agora vai!-->
<!--          />-->

          <div
            v-for="roles in roles"
            :key="roles.id"
            class="q-pa-sm q-mb-sm flex items-center cursor-pointer"
            style="border: 1px solid #ccc; border-radius: 12px;"
            @click="selectRoles(roles)"
          >
            <q-icon :name="roles.icon" class="q-mr-sm" />
            <div class="column">
              <span class="text-subtitle1 text-black">{{ roles.name }}</span>
              <span class="text-caption text-grey">Salário: {{ roles.salary }}</span>
              <span class="text-caption text-grey">Valor hora: {{ roles.hourly_price }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Coluna de detalhes do colaborador -->
      <div
        class="col-6 flex column justify-start items-center bg-grey-4"
        style="border-radius: 10px; min-height: 96vh; padding-top: 20px"
      >
        <div v-if="selectedRole" class="full-width flex column items-center">
          <div class="flex justify-center q-mb-md">
            <q-icon :name="selectedRole.icon" color="black" size="64px" />
          </div>

          <div class="text-center q-mb-md">
            <p class="text-bold text-primary text-h4">{{ selectedRole.name }}</p>

            <template v-if="selectedRole.name === 'Operador'">
              <p class="text-primary" >Valor hora: {{ selectedRole.hourly_price }} </p>
            </template>

            <template v-else-if="selectedRole.name === 'Consultor Técnico'">
              <p class="text-primary">Salário: {{ selectedRole.salary }}</p>
            </template>

            <template v-else-if="selectedRole.name === 'Lider de Pátio'">
              <p class="text-primary">Valor hora: {{ selectedRole.hourly_price }}</p>
            </template>

            <template v-else-if="selectedRole.name === 'Financeiro'">
              <p class="text-primary">Valor hora: {{ selectedRole.salary }}</p>
            </template>
          </div>

          <!-- Ícones de ação -->
          <div class="q-mb-md">
            <q-btn dense icon="edit" color="primary" @click="editMode = true" class="q-mr-sm" />
<!--            <q-btn dense icon="delete" color="negative" @click="delete_role(selectedRole)" />-->
          </div>

          <!-- Formulário de edição -->
          <div v-if="editMode" class="q-pa-md bg-white" style="border-radius: 12px; max-width: 400px;">

            <template v-if="editedRole.name === 'Operador'">
              <q-input v-model="editedRole.name" label="Valor hora" class="q-mb-sm" />
              <q-input v-model="editedRole.hourly_price" label="Valor hora" class="q-mb-sm" />
            </template>

            <template v-else-if="editedRole.name === 'Consultor Técnico'">
              <q-input v-model="editedRole.salary" label="Salário" class="q-mb-sm" />
            </template>

            <template v-else-if="editedRole.name === 'Financeiro'">
              <q-input v-model="editedRole.salary" label="Salário" class="q-mb-sm" />
            </template>

            <template v-else-if="editedRole.name === 'Lider de Pátio'">
              <q-input v-model="editedRole.hourly_price" label="Valor hora" class="q-mb-sm" />
            </template>


            <q-btn label="Salvar alterações" color="primary" class="q-mt-md full-width" @click="update_role()" />
          </div>
        </div>

        <div v-else>
          <div class="text-center">
            <p class="text-bold text-black text-h4">Selecione alguma categoria para alterar o valor</p>
          </div>
        </div>
      </div>
    </div>
  </q-layout>
</template>

<script>
import { useRoute } from 'vue-router';

export default {
  name: 'ListaCategorias',

  data() {
    return {
      roles: [],
      selectedRole: null,
      editedRole: {},
      editMode: false,
      route: useRoute(),
    };
  },

  methods: {

    async get_list_role() {
      try {
        const response = await fetch(
          `https://fortis-api.55technology.com/v1/role/company/${this.$route.params.company_uuid}`,
          {
            method: 'GET',
            headers: { token: localStorage.getItem('access_token') },
          }
        );

        if (!response.ok) {
          throw new Error('Erro ao buscar colaboradores.');
        }

        const data = await response.json();

        this.roles = data.roles.map((op, index) => ({
          id: index,
          uuid: op.uuid,
          name: op.name,
          hourly_price: op.hourly_price,
          icon: 'person',
          salary: op.salary,
        }));
      } catch (error) {
        console.error('Erro ao buscar colaboradores:', error);
      }
    },

    selectRoles(roles) {
      this.selectedRole = roles;
      this.editMode = false;
      this.editedRole = { ...roles };
    },

    async update_role() {
      try {
        const response = await fetch(
          `https://fortis-api.55technology.com/v1/role/${this.selectedRole.uuid}`,
          {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
              token: localStorage.getItem('access_token'),
            },
            body: JSON.stringify({
              hourly_price: this.editedRole.hourly_price,
              salary: this.editedRole.salary,
            }),
          }
        );

        if (!response.ok) {
          throw new Error('Erro ao atualizar colaborador.');
        }

        this.$q.notify({
          color: 'green-4',
          textColor: 'white',
          icon: 'check',
          message: 'Colaborador atualizado com sucesso.',
        });

        this.editMode = false;
        this.get_list_role();
      } catch (error) {
        console.error('Erro ao atualizar colaborador:', error);
        this.$q.notify({
          color: 'red-4',
          textColor: 'white',
          icon: 'error',
          message: error.message,
        });
      }
    },

    async delete_role(roles) {
      try {
        await this.$q.dialog({
          title: 'Confirmar exclusão',
          message: `Deseja realmente excluir o colaborador "${roles.name}"?`,
          cancel: true,
          persistent: true,
        }).onOk(async () => {
          const response = await fetch(
            `https://fortis-api.55technology.com/v1/roles/${roles.uuid}`,
            {
              method: 'DELETE',
              headers: {
                'Content-Type': 'application/json',
                token: localStorage.getItem('access_token'),
              },
            }
          );

          if (!response.ok) {
            throw new Error('Erro ao excluir colaborador.');
          }

          this.$q.notify({
            color: 'green-4',
            textColor: 'white',
            icon: 'delete',
            message: 'Colaborador excluído com sucesso.',
          });

          this.get_list_role();
          this.selectedRole = null;
        });
      } catch (error) {
        console.error('Erro ao excluir colaborador:', error);
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
    this.get_list_role();
  },
};
</script>

<style scoped>
.video-container {
  width: 100%;
}
</style>
