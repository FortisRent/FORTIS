<template>
  <q-layout class="q-pa-md bg-white no-margin">
    <q-img src="../../assets/meetmedlogo.png" width="80px" height="80px" style="margin-bottom: -15px;" />
    <div class="column items-center q-gutter-md">
      <q-btn
          flat
          round
          icon="arrow_back"
          class="q-mr-sm text-primary"
          color="black"
          style="position: absolute; top: 5px; left: -15px; z-index: 10;"
          @click="$router.go(-1)"
        />
      <div class="text-center relative">
        <q-btn
          icon="calendar_today"
          style="border-radius: 20px; width: 80px; margin-bottom: -60px; z-index: 1"
          color="info"
          class="q-pb-md"
          @click="show"
        />
      </div>
      <div class="q-pa-md text-primary">
        <q-date
          v-model="selectedDate"
          today-btn
          class="bg-info rounded"
          color="info"
          style="position: relative; z-index: 2;"
          locale="pt-BR"
          @input="onDateSelect"
        />
        <div class="flex justify-center">
          <q-btn
            v-if="selectedDate"
            label="SELECIONE HORÁRIO"
            color="info"
            @click="getAvailableSchedulingHours"
            class="rounded q-mt-md flex"
          />
        </div>
        <div v-if="selectedTime" class="q-mt-md text-center">
          <p>Data: {{ formattedDate }}</p>
          <p>Horário: {{ selectedTime }}</p>
        </div>

        <div class="flex justify-center">
          <q-btn
            v-if="selectedDate && selectedTime"
            label="AGENDAR"
            color="info"
            class="rounded q-mt-md"
            :to="`/doctor/${this.$route.params.doctor_uuid}/scheduling/payment`"
          />
        </div>
      </div>
    </div>
  </q-layout>
</template>

<script>
import { LocalStorage, useQuasar } from 'quasar';

export default {
  data() {
    return {
      selectedDate: null,
      selectedTime: null,
      times: [],
    };
  },
  computed: {
    formattedDate() {
      if (!this.selectedDate) return 'Data não selecionada';
      const date = new Date(this.selectedDate);
      const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', locale: 'pt-BR' };
      return date.toLocaleDateString('pt-BR', options);
    },
  },
  methods: {
    show() {
      this.$q.bottomSheet({
        style: 'background-color:#65B8A6; color:black;',
        message: `Data escolhida: ${this.formattedDate}`,
        actions: this.times.map(time => ({
          calendar_uuid: time.calendar_uuid,
          label: time.start_time,
          icon: 'schedule',
          color: 'black'
        })),
      })
      .onOk(action => {
        this.selectedTime = action.label;
        
        localStorage.setItem('calendar_uuid', action.calendar_uuid);
        localStorage.setItem('due_date', this.selectedDate);
      })
      .onCancel(() => {
        // Ação ao cancelar
      })
      .onDismiss(() => {
        // Ação ao fechar
      });
    },
    onDateSelect(newDate) {
      console.log("Data selecionada:", newDate);
    },
    getAvailableSchedulingHours() {
      fetch('http://localhost:5510/v1/doctor/calendar/available/', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						'token': localStorage.getItem('access_token')
					},
					body: JSON.stringify({
						calendar_date: this.selectedDate,
            doctor_uuid: this.$route.params.doctor_uuid,
					}),
				})
				.then(response => {
					if (!response.ok) {
						throw new Error('Erro ao encontrar agenda do médico.');
					}

          return response.json();
				})
        .then(data => {
          this.times = [];
          this.times = data.doctor_calendar;

          // Só abre o modal apóscarregar a lista
          if (this.times.length === 0) {
            this.$q.notify({
              color: 'red-5',
              textColor: 'white',
              icon: 'cloud_done',
              message: 'Nenhum horário disponível na data selecionada.'
            });
          } else {
            this.show();
          }
        });

    },
  },
  mounted() {
    this.$q = useQuasar();
  },
};
</script>

<style scoped>
.rounded {
  border-radius: 30px;
}
</style>
