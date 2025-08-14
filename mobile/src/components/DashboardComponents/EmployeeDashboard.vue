<template>
  <q-layout class="bg-white">
    <div class="q-pa-md row">
      <!-- Coluna lateral de funcionários -->
      <div class="col-6 flex column items-center" style="min-height: 96vh">
        <p class="text-h3 text-primary text-bold">Lista dos colaboradores</p>

        <div class="q-pa-md full-width">
          <q-btn
            :to="`/dashboard/company/collaborators/${this.$route.params.company_uuid}/create`"
            icon="group_add"
            label="Adicionar colaborador"
            text-color="black"
            class="q-mb-md"
          />

          <div
            v-for="employees in employees"
            :key="employees.id"
            class="q-pa-sm q-mb-sm flex items-center cursor-pointer"
            style="border: 1px solid #ccc; border-radius: 12px;"
            @click="selectEmployee(employees)"
          >
            <q-icon :name="employees.icon" class="q-mr-sm" />
            <div class="column">
              <span class="text-subtitle1">{{ employees.nome }}</span>
              <span class="text-caption text-grey">{{ employees.funcao }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Coluna de detalhes do colaborador -->
      <div
        class="col-6 flex column justify-start items-center bg-grey-4"
        style="border-radius: 10px; min-height: 96vh; padding-top: 20px"
      >
        <div v-if="selectedEmployee" class="full-width flex column items-center">
          <div class="flex justify-center q-mb-md">
            <q-icon :name="selectedEmployee.icon" color="black" size="64px" />
          </div>

          <div class="text-center q-mb-md">
            <p class="text-bold text-primary text-h4">{{ selectedEmployee.nome }}</p>
            <p class="text-primary q-mt-sm  ">Função: {{ selectedEmployee.funcao }}</p>
            <p class="text-primary">Carteira de trabalho: {{ selectedEmployee.ctps_number }}</p>
            <p class="text-primary">Telefone: {{ selectedEmployee.phone }}</p>
            <template v-if="selectedEmployee.funcao === 'Operador'">
              <p class="text-primary">Valor hora: {{ selectedEmployee.hourly_price }}</p>
<!--              <p class="text-primary">Valor mínimo: {{ selectedEmployee.minimum_rental_period }}</p>-->
<!--              <p class="text-primary">Valor distância: {{ selectedEmployee.distance_amount }}</p>-->
            </template>

            <template v-else-if="selectedEmployee.funcao === 'Consultor Técnico'">
              <p class="text-primary">Salário: {{ selectedEmployee.salary }}</p>
            </template>
          </div>

          <!-- Ícones de ação -->
          <div class="q-mb-md">
            <q-btn dense icon="edit" color="primary" @click="editMode = true" class="q-mr-sm" />
            <q-btn dense icon="delete" color="negative" @click="delete_employee(selectedEmployee)" />
          </div>

          <!-- Formulário de edição -->
          <div v-if="editMode" class="q-pa-md bg-white" style="border-radius: 12px; max-width: 400px;">
            <q-input v-model="editedEmployee.nome" label="Nome" class="q-mb-sm" />
            <q-input  v-model="editedEmployee.funcao" readonly label="Função" class="q-mb-sm"  />
            <q-input v-model="editedEmployee.ctps_number" label="Carteira de trabalho" class="q-mb-sm" />


            <q-btn label="Salvar alterações" color="primary" class="q-mt-md full-width" @click="update_employee()" />
          </div>
        </div>

        <div v-else>
          <div class="text-center">
            <p class="text-bold text-white text-h4">Selecione algum colaborador</p>
          </div>
        </div>
      </div>
    </div>
  </q-layout>
</template>

<script>
import { useRoute } from 'vue-router';

export default {
  name: 'ListaColaboradores',

  data() {
    return {
      employees: [],
      selectedEmployee: null,
      editedEmployee: {},
      editMode: false,
      route: useRoute(),
    };
  },

  methods: {
    async get_list_operator() {
      try {
        const response = await fetch(
          `https://fortis-api.55technology.com/v1/employee/company/${this.$route.params.company_uuid}`,
          {
            method: 'GET',
            headers: { token: localStorage.getItem('access_token') },
          }
        );

        if (!response.ok) {
          throw new Error('Erro ao buscar colaboradores.');
        }

        const data = await response.json();

        this.employees = data.employees.map((op, index) => ({
          id: index,
          uuid: op.employee_uuid,
          nome: op.employee_name,
          funcao: op.role_name,
          ctps_number: op.ctps_number,
          hourly_price: op.hourly_price,
          icon: 'person',
          phone: op.phone,
          salary: op.salary,
          minimum_rental_period: op.minimum_rental_period,
          distance_amount: op.distance_amount,
        }));
      } catch (error) {
        console.error('Erro ao buscar colaboradores:', error);
      }
    },

    selectEmployee(employees) {
      this.selectedEmployee = employees;
      this.editMode = false;
      this.editedEmployee = { ...employees };
    },

    async update_employee() {
      try {
        const response = await fetch(
          `https://fortis-api.55technology.com/v1/employee/${this.selectedEmployee.uuid}`,
          {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
              token: localStorage.getItem('access_token'),
            },
            body: JSON.stringify({
              employee_name: this.editedEmployee.nome,
              role_name: this.editedEmployee.funcao,
              ctps_number: this.editedEmployee.ctps_number,
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
        this.get_list_operator();
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

    async delete_employee(employees) {
      try {
        await this.$q.dialog({
          title: 'Confirmar exclusão',
          message: `Deseja realmente excluir o colaborador "${employees.nome}"?`,
          cancel: true,
          persistent: true,
        }).onOk(async () => {
          const response = await fetch(
            `https://fortis-api.55technology.com/v1/employees/${employees.uuid}`,
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

          this.get_list_operator();
          this.selectedEmployee = null;
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
    this.get_list_operator();
  },
};
</script>

<style scoped>
.video-container {
  width: 100%;
}
</style>
