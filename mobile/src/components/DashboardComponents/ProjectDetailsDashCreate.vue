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
        ADCIONAR UMA NOVA MAQUINA
      </div>
    </div>
    <q-separator class="bg-secondary" />

    <div class="q-pa-md">
      <!-- Select de Máquina -->
      <q-select
        outlined
        v-model="selectedMachine"
        :options="machineOptions"
        option-label="label"
        label="Selecione a Máquina"
        emit-value
        map-options
        dense
      />

      <!-- Exibir dados selecionados -->
      <div v-if="selectedMachineData" class="q-mt-md text-black">
        <div><strong>Categoria:</strong> {{ selectedMachineData.category_name }}</div>
        <div><strong>Marca:</strong> {{ selectedMachineData.brand }}</div>
        <div><strong>Nome:</strong> {{ selectedMachineData.name }}</div>
        <div><strong>Parâmetros:</strong> {{ selectedMachineData.parameters }}</div>
        <div><strong>Preço por hora:</strong> {{ selectedMachineData.price_per_hour }}</div>
        <div><strong>Preço horario especial:</strong> {{ selectedMachineData.special_hour_fee }}</div>
      </div>

      <q-btn
        color="primary"
        label="Adcionar Máquina"
        class="q-mt-lg"
        @click="on_submit"
      />
    </div>
  </q-page>
</template>

<script>
import { ref } from 'vue';

export default {
  name: 'ProjectBudget',
  data() {
    return {
      machineOptions: [],
      selectedMachine: null,
      selectedMachineData: null,
      company_uuid: '',
      uuid: '',
    };
  },
  async mounted() {
    this.company_uuid = localStorage.getItem('company_uuid');
    this.get_machine_company();
  },
  methods: {
    async get_machine_company() {
      const company_uuid = localStorage.getItem('company_uuid');

      fetch(`http://localhost:5510/v1/machine/company/${company_uuid}`, {
        headers: { token: localStorage.getItem('access_token') },
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          return response.json();
        })
        .then((data) => {
          // Monta opções para o q-select
          this.machineOptions = data.machine.map((item) => ({
            label: `${item.category_name} - ${item.brand}`,
            value: item.uuid,
            data: item, // guardar info completa para exibir depois
          }));
        })
        .catch((error) => {
          console.error('Erro ao buscar máquinas:', error);
          this.$q.notify({
            color: 'red-5',
            textColor: 'white',
            icon: 'error',
            message: 'Erro ao buscar máquinas!',
          });
        });
    },

    on_submit() {
      if (!this.selectedMachine) {
        this.$q.notify({
          color: 'red-5',
          textColor: 'white',
          icon: 'error',
          message: 'Selecione uma máquina antes de enviar!',
        });
        return;
      }

      fetch('http://localhost:5510/v1/budget/machine/', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          token: localStorage.getItem('access_token'),
        },
        body: JSON.stringify({
          budget_uuid: this.$route.params.budget_uuid,
          machine_uuid: this.selectedMachine, // uuid da máquina selecionada
        }),
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error('Erro ao cadastrar Máquina.');
          }
          this.$q.notify({
            color: 'green-4',
            textColor: 'white',
            icon: 'cloud_done',
            message: 'Máquina adcionada com sucesso!',
          });

          const company_uuid = localStorage.getItem('company_uuid');
          this.$router.go(-1)

        })
        .catch((error) => {
          this.$q.notify({
            color: 'red-4',
            textColor: 'white',
            icon: 'error',
            message: error.message,
          });
        });
    },
  },
  watch: {
    selectedMachine(newVal) {
      if (newVal) {
        const selected = this.machineOptions.find((item) => item.value === newVal);
        this.selectedMachineData = selected ? selected.data : null;
      } else {
        this.selectedMachineData = null;
      }
    },
  },
};
</script>
