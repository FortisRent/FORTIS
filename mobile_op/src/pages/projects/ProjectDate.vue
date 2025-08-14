<template>
  <q-page>
    <div class="row justify-around items-center q-pa-md q-mt-lg">
      <q-btn
        flat
        round
        icon="chevron_left"
        class="text-primary"
        color="secondary"
        size="18px"
        style="position: absolute; left: 10px;"
        @click="$router.go(-1)"
      />
      <div class="text-h6 text-primary text-bold q-ml-md">
        Agendamento
      </div>
    </div>

    <q-separator class="bg-grey" />

    <div>
      <p class="text-primary text-bold text-h6 text-center q-pa-md">
        Escolha o dia e o horário para visualizar a disponibilidade
      </p>

      <div class="q-pa-md">
        <div class="q-mb-lg flex justify-center">
          <q-date v-model="calendar_date" color="secondary" mask="YYYY-MM-DD" />
        </div>
        <div class="q-mb-lg flex justify-center">
          <q-time v-model="hour_date" color="secondary" class="text-primary" mask="HH:mm" />
        </div>

        <!-- Exibe a data e o horário escolhidos -->
        <div v-if="calendar_date && hour_date" class="text-center q-mb-md">
          <q-badge color="teal" class="q-pa-md">
            Agendamento: {{ formattedDateTime }}
          </q-badge>
        </div>

        <!-- Lista de máquinas disponíveis -->
        <div v-if="availableMachines.length > 0" class="q-mt-md">
          <p class="text-primary text-bold text-center">Máquinas disponíveis:</p>
          <q-list bordered separator>
            <q-item v-for="machine in availableMachines" :key="machine.machine_id">
              <q-item-section>
                <div class="text-primary">
                  <strong>Empresa:</strong> {{ machine.company_name }} <br>
                  <strong>Categoria:</strong> {{ machine.category_name }} <br>
                  <strong>Máquina:</strong> {{ machine.machine_name }} <br>
                  <strong>Status:</strong> {{ machine.status }}
                  <q-btn class="" label="CONFIRMAR AGENDAMENTO" @click="confirmBooking" />
                </div>
              </q-item-section>
            </q-item>
          </q-list>
        </div>

        <div class="flex justify-between q-mt-sm">
          <q-btn @click="$router.go(-1)" label="Cancelar" class="text-primary" />
          <q-btn label="Consultar Disponibilidade" color="secondary" class="text-primary" @click="on_submit" />
        </div>
      </div>
    </div>
  </q-page>
</template>

<script>
export default {
  data() {
    return {
      calendar_date: null,
      hour_date: null,
      availableMachines: [],
      expected_date:'',
      budget_proposal_uuid: this.$route.params.budget_proposal_uuid,
    };
  },
  computed: {
    formattedDateTime() {
      return this.calendar_date && this.hour_date
        ? `${this.calendar_date} ${this.hour_date}`
        : "";
    }
  },
  methods: {
    async checkAvailability() {
      // Fetch available machines (POST)
      try {
        const response = await fetch('https://fortis-api.55technology.com/v1/budget/available/', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'token': localStorage.getItem('access_token')
          },
          body: JSON.stringify({
            budget_proposal_uuid: this.budget_proposal_uuid,
            calendar_date: this.calendar_date,
            hour_date: this.hour_date,
          }),
        });

        if (!response.ok) {
          throw new Error('Por favor, preencha todos os campos.');
        }
        const data = await response.json();
        this.availableMachines = data.available_machines;
        this.$q.notify({
          color: 'green-4',
          textColor: 'white',
          icon: 'cloud_done',
          message: 'Máquinas Disponíveis',
        });
      } catch (error) {
        this.$q.notify({
          color: 'red-4',
          textColor: 'white',
          icon: 'cloud_done',
          message: error.message,
        });
      }
    },
    async confirmBooking() {
      const expected_date = `${this.calendar_date} ${this.hour_date}`;

      try {
        const response = await fetch(`https://fortis-api.55technology.com/v1/budget/proposal/accept/${this.budget_proposal_uuid}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'token': localStorage.getItem('access_token')
          },
          body: JSON.stringify({
            budget_proposal_uuid: this.budget_proposal_uuid,
            expected_date: expected_date,
          }),
        });

        if (!response.ok) {
          throw new Error('Erro ao confirmar o agendamento.');
        }
        this.$q.notify({
          color: 'green-4',
          textColor: 'white',
          icon: 'cloud_done',
          message: 'Agendamento Concluído.',
        });

        this.$router.push('/user/timeline/list');

      } catch (error) {
        this.$q.notify({
          color: 'red-4',
          textColor: 'white',
          icon: 'cloud_done',
          message: error.message,
        });
      }
    },
    on_submit() {
      this.checkAvailability();
    }
  }
};
</script>

<style scoped>
.machine-style {
  min-width: 340px;
  height: 50px;
  background-color: white;
  border-radius: 3px;
}
.border {
  border-color: secondary;
}
</style>
