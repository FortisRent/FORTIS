<template>
  <q-page class="bg-white">
  

      <div class="flex justify-between">
        <h4 class="q-mb-sm q-ml-md text-primary text-bold text-h5 q-pt-md ">Consultas</h4>
      </div>

      <q-separator class="bg-info" />

      <div class="bg-white">
        <q-input
          outlined
          placeholder="Pesquisar"
          v-model="search"
          class="q-pa-md"
          bg-color="white"
          rounded
        >
          <template v-slot:prepend>
            <q-icon name="search" />
          </template>
        </q-input>

        <q-pull-to-refresh @refresh="refreshList" color="primary" bg-color="white" icon="autorenew" scroll-target="body">
          <q-list>
            <template v-if="loading">
              <q-item v-for="i in 3" :key="i" style="max-width: 300px;">
                <q-item-section avatar>
                  <q-skeleton type="QAvatar" />
                </q-item-section>
                <q-item-section>
                  <q-item-label>
                    <q-skeleton type="text" />
                  </q-item-label>
                  <q-item-label caption>
                    <q-skeleton type="text" width="65%" />
                  </q-item-label>
                </q-item-section>
              </q-item>
            </template>

            <template v-else>
              <q-item
                v-for="appointment in appointments"
                :key="appointment.id"
                clickable
                class="line-item"
                :to="`/appointment/${appointment.uuid}/details`"
                v-ripple
              >
                <q-item-section avatar>
                  <q-avatar style="width: 70px; height: 70px;">
                    <q-img
                      v-if="appointment.selfie_url"
                      :src="`https://fortis-api.55technology.com/${appointment.selfie_url}`"
                      width="70px"
                      height="70px"
                      fit="cover"
                      style="border-radius: 100px;"
                    />
                 </q-avatar>
                </q-item-section>
                <q-item-section>
                  <q-item-label class="text-primary text-truncate">{{ appointment.clinic_name }}</q-item-label>
                  <q-item-label caption class="text-truncate">
                    {{ appointment.doctor_name }} - {{ appointment.specialty_name }}
                  </q-item-label>
                  <q-item-label caption class="text-truncate">
                    {{ appointment.due_date }} às {{ appointment.start_time }}
                  </q-item-label>
                  <q-item-label caption class="text-truncate">
                    R$ {{ parseFloat(appointment.appointment_amount / 100).toFixed(2) }}
                  </q-item-label>
                </q-item-section>
              </q-item>
            </template>
          </q-list>
        </q-pull-to-refresh>
      </div>
  </q-page>
</template>

<script>
export default {
  name: "AppointmentScheduling",
  data() {
    return {
      appointments: [], 
      search: "",
      loading: true, // estado de carregamento
    };
  },
  methods: {
    async get_appointment_data() {
      this.loading = true; // inicia o carregamento
      try {
        const response = await fetch(
          `https://fortis-api.55technology.com/v1/appointment/scheduling/logged/`,
          {
            method: "GET",
            headers: {
              token: localStorage.getItem("access_token"),
            },
          }
        );

        if (!response.ok) throw new Error("Erro ao buscar dados.");

        const data = await response.json();
        this.appointments = data.appointment_schedulings;
        this.loading = false;
        this.selfie_url = data.appointment_scheduling.selfie_url;

        if (this.appointments.length === 0) {
          // this.$q.notify({
          //   color: "secondary-8",
          //   textColor: "white",
          //   icon: "cloud_done",
          //   message: "Nenhuma consulta agendada.",
          // });
        }
      } catch (error) {
        console.error("Error fetching data:", error);

        // this.$q.notify({
        //   color: "red-5",
        //   textColor: "white",
        //   icon: "cloud_done",
        //   message: "Ops! Falha ao carregar dados.",
        // });

        this.loading = false; 
      }
    },
    refreshList(done) {
      this.get_appointment_data();
      done(); 
    },
  },
  mounted() {
    this.get_appointment_data();
  },
};
</script>

<style scoped>
.line-item {
  border-bottom: 0.5px solid #979797;
  padding-left: 16px;
  padding-right: 16px;
}
.text-truncate {
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		display: block;
		max-width: 100%;
	}
</style>
