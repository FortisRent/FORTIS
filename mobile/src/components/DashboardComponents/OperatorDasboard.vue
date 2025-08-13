<template>
  <q-layout class="bg-white">
    <div class="q-pa-md row">
      <div class="col-6 flex column items-center" style="min-height: 96vh">

        <div class="q-pa-md full-width">
          <p class="text-h4 text-primary text-bold">Lista dos operadores</p>
          <div v-for="employee in employees" :key="employee.id" class="q-pa-sm q-mb-sm flex items-center cursor-pointer"
            style="border: 1px solid #ccc; border-radius: 12px;" @click="selectEmployee(employee)">
            <q-icon :name="employee.icon" class="q-mr-sm" />
            <span class="text-subtitle1">{{ employee.nome }}</span>
          </div>
        </div>
      </div>

      <div class="col-6 flex column justify-start items-center bg-grey-4"
        style="border-radius: 10px; min-height: 96vh; padding-top: 20px">
        <div v-if="selectedEmployee" class="full-width flex column items-center">
          <div class="flex justify-center q-mb-md">
            <q-icon :name="selectedEmployee.icon" color="black" size="64px" />
          </div>
          <div class="text-center q-mb-md">
            <p class="text-bold text-primary text-h4">
              {{ selectedEmployee.nome }}
            </p>
            <q-input label="Preço hora" mask="R$ ###.###,##" readonly
               reverse-fill-mask
               v-model="selectedEmployee.hourly_price"
            />


            <p class="text-primary">
              Cargo: <span class="text-bold text-secondary"> {{ selectedEmployee.role_name }} </span>
            </p>

            <div class="row q-mt-md">
              <q-btn label="Ver check-in" color="green" @click="showCheckins = !showCheckins" class="q-mr-sm" />
              <q-btn label="Ver check-out" color="red" @click="showCheckouts = !showCheckouts" />
            </div>
          </div>

          <div class="row q-mt-md q-col-gutter-md">
            <div v-if="showCheckins" class="q-pa-md bg-white"
              style="border-radius: 12px; max-width: 400px; max-height: 350px; overflow-y: auto;">
              <p class="text-h5 text-primary text-bold q-mb-md">{{ selectedEmployee.nome }} - Check-ins</p>

              <div v-for="(check, index) in checkins" :key="index" class="q-pa-sm q-mb-sm"
                style="border: 1px solid #ccc; border-radius: 8px;">
                <p class="text-subtitle2 text-grey-8 q-mb-xs">Data: {{ check.checkin_date }} | Hora: {{
                  check.checkin_hour }}</p>
                <p class="text-body1">📋 {{ check.checkin_description }}</p>
              </div>
            </div>

            <div v-if="showCheckouts" class="q-pa-md bg-white q-ml-sm"
              style="border-radius: 12px; max-width: 400px; max-height: 350px; overflow-y: auto;">
              <p class="text-h5 text-primary text-bold q-mb-md">{{ selectedEmployee.nome }} - Check-outs</p>

              <div v-for="(check, index) in checkouts" :key="index" class="q-pa-sm q-mb-sm"
                style="border: 1px solid #ccc; border-radius: 8px;">
                <p class="text-subtitle2 text-grey-8 q-mb-xs">Data: {{ check.checkout_date }} | Hora: {{
                  check.checkout_hour }}</p>
                <p class="text-body1">📋 {{ check.checkout_description }}</p>
              </div>
            </div>
          </div>
        </div>

        <div v-else>
          <div class="text-center">
            <p class="text-bold text-primary text-h4">Selecione algum operador</p>
          </div>
        </div>
      </div>
    </div>
  </q-layout>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';

export default {
  name: 'ListaColaboradores',

  data() {
    return {
      employees: [],
      selectedEmployee: null,
      showCheckins: false,
      showCheckouts: false,
      checkins: [],
      checkouts: [],
      route: useRoute(),
    };
  },

  methods: {
    async get_list_operator() {
      try {
        const response = await fetch(
          `http://localhost:5510/v1/operator/company/${this.route.params.company_uuid}`,
          {
            method: 'GET',
            headers: { token: localStorage.getItem('access_token') },
          }
        );

        if (!response.ok) {
          throw new Error('Erro ao buscar operadores.');
        }

        const data = await response.json();
        console.log(data);

        this.employees = data.company_operators.map((op, index) => ({
          id: index,
          nome: op.employee_name,
          icon: 'person',
          email: 'Não informado',
          telefone: 'Não informado',
          role_name: op.role_name,
          hourly_price: op.hourly_price,
          uuid: op.uuid,
        }));
      } catch (error) {
        console.error('Erro ao buscar operadores:', error);
      }
    },

    async get_list_checks(uuid) {
      try {
        const response = await fetch(
          `http://localhost:5510/v1/operator/checks/${uuid}`,
          {
            method: 'GET',
            headers: { token: localStorage.getItem('access_token') },
          }
        );

        if (!response.ok) {
          throw new Error('Erro ao buscar check-ins/check-outs.');
        }

        const data = await response.json();
        console.log(data);

        // Aqui separa o operator_checks em checkins e checkouts
        this.checkins = data.operator_checks.map(check => ({
          checkin_date: check.checkin_date,
          checkin_description: check.checkin_description,
          checkin_hour: check.checkin_hour,
        }));

        this.checkouts = data.operator_checks.map(check => ({
          checkout_date: check.checkout_date,
          checkout_description: check.checkout_description,
          checkout_hour: check.checkout_hour,
        }));

      } catch (error) {
        console.error('Erro ao buscar check-ins/check-outs:', error);
      }
    },

    selectEmployee(employee) {
      this.selectedEmployee = employee;
      this.showCheckins = false;
      this.showCheckouts = false;
      this.get_list_checks(employee.uuid);
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
