<template>
  <q-page class="bg-white">
    <div class="row justify-around items-center q-mt-xl q-mb-sm">
      <q-btn
        flat
        round
        icon="arrow_back"
        class="q-mr-sm text-primary"
        color="black"
        style="position: absolute; left: 10px;"
        @click="$router.go(-1)"
      />
      <div class="text-bold text-primary text-h5" style="margin-left: -50px;">Validação CRM</div>
    </div>

    <q-separator class="bg-info" />

    <!-- Aqui adicionamos o q-pull-to-refresh -->
    <q-pull-to-refresh @refresh="refresh" class="q-pa-none">
      <div class="row items-center reverse justify-around q-mb-md q-ml-md">
        <div class="text-left col q-mr-none">
          <h5 class="q-mb-none text-primary text-bold">
            <q-skeleton v-if="!user_name" type="text" width="150px" />
            <span v-else class="text-truncate">{{ user_name }}</span>
          </h5>
          <p class="q-mb-xs text-grey-7 text-caption">
            <q-skeleton v-if="!user_email" type="text" width="200px" />
            <span v-else class="text-truncate">{{ user_email }}</span>
          </p>
          <div class="text-bold">
            <q-skeleton v-if="status === null" type="circle" size="24px" />
            <q-badge v-else rounded :color="statusColor" />
            <q-skeleton v-if="status === null" type="text" width="100px" class="q-ml-sm" />
            <span v-else :class="statusClass" class="q-ml-sm">{{ statusLabel }}</span>
          </div>
        </div>
        <q-avatar size="80px" class="q-mr-lg q-mt-lg">
          <q-skeleton v-if="!user_name" type="rect" size="80px" />
          <q-img
							v-if="selfie_url"
							:src="`http://localhost:5510/${selfie_url}`"
							width="80px"
							height="80px"
							fit="cover"
							style="border-radius: 100px;"
						/>
        </q-avatar>
      </div>

      <div class="flex items-center justify-between text-primary q-mt-xl q-ml-md q-mr-md">
        <h5 class="text-bold q-my-none">Dados</h5>
        <q-btn
          v-if="user_name"
          class="edit"
          icon="edit"
          flat
          size="sm"
          :to="`/doctor/validation/edit/${doctor_uuid}`" 
        />
      </div>

      <q-list separator class="text-primary text-left">
        <q-item>
          <q-item-section>
            <div class="data-truncate">
              Nome Completo:
              <q-skeleton v-if="!user_name" type="text" width="150px" />
              <span v-else>{{ user_name }}</span>
            </div>
          </q-item-section>
        </q-item>

        <q-item>
          <q-item-section>
            <div class="data-truncate">
              Estado de Emissão do CRM:
              <q-skeleton v-if="!emission_state" type="text" width="150px" />
              <span v-else>{{ emission_state }}</span>
            </div>
          </q-item-section>
        </q-item>

        <q-item>
          <q-item-section>
            <div class="data-truncate">
              N° do CRM:
              <q-skeleton v-if="!crm_number" type="text" width="150px" />
              <span v-else>{{ crm_number }}</span>
            </div>
          </q-item-section>
        </q-item>

        <q-item>
          <q-item-section>
            <div class="data-truncate">
              CPF:
              <q-skeleton v-if="!user_cpf" type="text" width="150px" />
              <span v-else>{{ user_cpf }}</span>
            </div>
          </q-item-section>
        </q-item>

        <q-separator />
        <div class="flex justify-center q-mt-lg">
          <q-btn
            class="bg-info"
            label="Validar CRM"
            v-if="!status && user_name"
            v-on:click="ValidateDoctor" />
          <q-skeleton v-if="status === null" type="rect" width="100px" height="40px" />
        </div>

        <h5 class="text-bold q-ml-md q-my-none">Informações de Registro</h5>
        <q-item>
          <q-item-section>
            <div class="data-truncate">
              Status do Registro:
              <q-skeleton v-if="!registration_status" type="text" width="200px" />
              <span v-else>{{ registration_status }}</span>
            </div>
          </q-item-section>
        </q-item>
        <q-item>
          <q-item-section>
            <div class="data-truncate">
              Especialidades Registradas:
              <q-skeleton v-if="!registered_specialties" type="text" width="200px" />
              <span v-else>{{ registered_specialties }}</span>
            </div>
          </q-item-section>
        </q-item>
        <q-item>
          <q-item-section>
            <div class="data-truncate">
              Advertências ou Restrições:
              <q-skeleton v-if="!warnings" type="text" width="200px" />
              <span v-else>{{ warnings }}</span>
            </div>
          </q-item-section>
        </q-item>
      </q-list>
    </q-pull-to-refresh>
  </q-page>
</template>

<script>
export default {
  name: 'UserProfile',
  data() {
    return {
      selfie_url: '',
      user_name: '',
      user_email: '',
      emission_state: '',
      crm_number: '',
      user_cpf: '',
      registration_status: '',
      registered_specialties: '',
      warnings: '',
      doctor_uuid: '',
      status: null,
    };
  },
  mounted() {
    this.get_user_data();
  },
  computed: {
    statusColor() {
      switch (this.status) {
        case 1:
          return 'green';
        case 0:
          return 'secondary';
        default:
          return 'gray';
      }
    },
    statusClass() {
      return {
        'text-green': this.status === 1,
        'text-secondary': this.status === 0,
        'text-gray': this.status !== 'Validado' && this.status !== 'Validação Pendente',
      };
    },
    statusLabel() {
      switch (this.status) {
        case 1:
          return 'Validado';
        case 0:
          return 'Validação Pendente';
        default:
          return 'Status Desconhecido';
      }
    },
  },
  methods: {
    refresh(done) {
      // Chama a função de pegar dados novamente
      this.get_user_data();
      done(); // Chama a função done para finalizar o "pull to refresh"
    },
    async get_user_data() {
      fetch(`http://localhost:5510/v1/doctor/validate/${this.$route.params.doctor_uuid}`, {
        method:'GET',
        headers:{'token': localStorage.getItem('access_token')},
      })
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(data => {
        this.user_name = data.doctor.doctor_name;
        this.user_email = data.doctor.email;
        this.emission_state = data.doctor.state_name;
        this.crm_number = data.doctor.crm;
        this.user_cpf = data.doctor.cpf;
        this.status = data.doctor.is_validated;
        this.doctor_uuid = data.doctor.doctor_uuid;
        this.selfie_url = data.doctor.selfie_url;
      })
      .catch(error => {
        this.$q.notify({
          color: 'red-5',
          textColor: 'white',
          icon: 'cloud_done',
          message: 'Ops! Falha ao carregar dados.',
        });
      });
    },
    async ValidateDoctor() {
      this.$q.dialog({
        title: 'Confirma?',
        message: 'Você realmente quer validar este médico?',
        cancel: true,
        persistent: true,
        dark: true,
      }).onOk(() => {
        fetch(`http://localhost:5510/v1/doctor/validate/${this.$route.params.doctor_uuid}`, {
          method: 'PUT',
          headers: { 'token': localStorage.getItem('access_token') },
        })
        .then(response => {
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          this.$q.notify({
            color: 'green-5',
            textColor: 'white',
            icon: 'cloud_done',
            message: 'Médico validado com Sucesso!',
          });
          this.$router.go(0);
        })
        .catch(error => {
          this.$q.notify({
            color: 'red-5',
            textColor: 'white',
            icon: 'cloud_done',
            message: 'Ops! Falha ao validar médico, por favor verifique os dados.',
          });
        });
      });
    },
  },
};
</script>

<style>
.edit {
  width: 50px;
  margin-left: 120px;
}
.text-truncate {
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		display: block;
		max-width: 90%;
	}
.data-truncate {
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
	display: block;
	max-width: 100%;
}
</style>
