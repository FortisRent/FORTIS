<template>
  <q-page :class="['bg-white', { 'overflow-hidden': loading }]">
    <div class="row justify-around items-center q-pa-md q-mt-lg">
      <q-btn
        flat
        round
        icon="chevron_left"
        class="text-primary"
        color="secondary"
        size="18px"
        style="position: absolute; left: 10px"
        @click="$router.go(-1)"
      />
      <div class="text-h6 text-primary text-bold q-ml-md">
        INSERIR COLABORADOR A UMA MÁQUINA
      </div>
    </div>
    <q-separator class="bg-secondary" />

    <div class="q-pa-md">
      <!-- Listagem das Máquinas -->
      <div v-for="machine in budget_machine" :key="machine.budget_machine_uuid" class="q-mb-xl">
        <div class="text-h6 text-primary q-mb-sm">
          {{ machine.machine_name }} — {{ machine.license_plate }}
        </div>

        <!-- Listagem dos Cargos (employees) -->
        <q-list bordered separator>
          <q-item
            v-for="employee in machine.employees"
            :key="employee.machine_operator_uuid"
          >
            <q-item-section>
              {{ employee.role_name }}:
              <span v-if="employee.operator_name"> {{ employee.operator_name }} </span>
              <span v-else class="text-grey"> (sem colaborador) </span>
            </q-item-section>

            <q-item-section side>
              <q-select
                dense
                filled
                v-model="employee.selectedOperator"
                :options="company_operators"
                option-label="employee_name"
                option-value="uuid"
                label="Selecionar"
                emit-value
                map-options
                style="width: 200px"
              />
            </q-item-section>

            <q-item-section side>
              <q-btn
                icon="add"
                color="primary"
                dense
                round
                @click="associateOperator(machine, employee)"
              />
            </q-item-section>
            <q-item-section side>
              <q-btn
                icon="delete"
                color="red"
                dense
                round
                @click="removeOperator(machine, employee)"
              />
            </q-item-section>

          </q-item>
        </q-list>
      </div>
    </div>
  </q-page>
</template>

<script>
import { ref } from 'vue';

export default {
  name: 'ProjectBudget',
  data() {
    return {
      budget_machine: [],
      company_uuid: '',
      company_operators: [],
      loading: false,
    };
  },
  async mounted() {
    this.company_uuid = localStorage.getItem('company_uuid');
    await this.get_operator_by_company();
    await this.get_budget_by_project_uuid();
  },
  methods: {
    async get_operator_by_company() {
      await fetch(`https://fortis-api.55technology.com/v1/operator/company/${this.company_uuid}`, {
        headers: { token: localStorage.getItem('access_token') },
      })
        .then((res) => res.json())
        .then((data) => {
          this.company_operators = data.company_operators || [];
        })
        .catch((err) => console.error('Erro ao buscar operadores:', err));
    },

    async get_budget_by_project_uuid() {
      await fetch(
        `https://fortis-api.55technology.com/v1/budget/${this.$route.params.budget_uuid}`,
        {
          headers: { token: localStorage.getItem('access_token') },
        }
      )
        .then((res) => res.json())
        .then((data) => {
          this.budget_machine = data.budget_machine.map((machine) => {
            machine.employees = machine.employees.map((e) => ({
              ...e,
              selectedOperator: null,
            }));
            return machine;
          });
        })
        .catch((err) => console.error('Erro ao buscar budget:', err));
    },

    async associateOperator(machine, employee) {
      if (!employee.selectedOperator) {
        this.$q.notify({
          color: 'red-4',
          textColor: 'white',
          icon: 'error',
          message: 'Selecione um colaborador antes de associar.',
        });
        return;
      }

      try {
        const response = await fetch(
          `https://fortis-api.55technology.com/v1/budget/machine/operator/${employee.machine_operator_uuid}`,
          {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
              token: localStorage.getItem('access_token'),
            },
            body: JSON.stringify({
              budget_machine_uuid: machine.budget_machine_uuid,
              employee_uuid: employee.selectedOperator,
            }),
          }
        );

        if (!response.ok) {
          const res = await response.json();
          throw new Error(res.error || 'Falha ao associar colaborador.');
        }

        this.$q.notify({
          color: 'green-4',
          textColor: 'white',
          icon: 'cloud_done',
          message: 'Colaborador associado com sucesso.',
        });

        // Atualiza o nome do operador associado na tela
        const operadorSelecionado = this.company_operators.find(
          (o) => o.uuid === employee.selectedOperator
        );
        employee.operator_name = operadorSelecionado.employee_name;

        // Limpa o select
        employee.selectedOperator = null;
      } catch (err) {
        this.$q.notify({
          color: 'red-5',
          textColor: 'white',
          icon: 'error',
          message: err.message,
        });
      }
    },
    async removeOperator(machine, employee) {
      try {
        const response = await fetch(
          `https://fortis-api.55technology.com/v1/budget/machine/operator/${employee.machine_operator_uuid}`,
          {
            method: 'DELETE',
            headers: {
              token: localStorage.getItem('access_token'),
            },
          }
        );

        if (!response.ok) {
          const res = await response.json();
          throw new Error(res.error || 'Falha ao remover operador.');
        }

        this.$q.notify({
          color: 'green-4',
          textColor: 'white',
          icon: 'cloud_done',
          message: 'Cargo removido da máquina com sucesso.',
        });

        // Remove o operador da lista local da máquina
        machine.employees = machine.employees.filter(
          (e) => e.machine_operator_uuid !== employee.machine_operator_uuid
        );

      } catch (err) {
        this.$q.notify({
          color: 'red-5',
          textColor: 'white',
          icon: 'error',
          message: err.message,
        });
      }
    },

  },
};
</script>
