<template>
  <q-page class="bg-white">
    <div class="row justify-around items-center q-pa-md q-mt-lg">
      <q-btn flat round icon="chevron_left" class="text-primary" color="secondary" size="18px"
        style="position: absolute; left: 10px;" @click="$router.go(-1)" />
      <div class="text-h6 text-primary text-bold q-ml-md">
        Meus Convites
      </div>
    </div>

    <q-separator class="bg-grey" />

    <div class="q-pa-md">
      <template v-if="loading">
        <q-card v-for="n in 3" :key="n" class="card-style q-pa-sm q-mb-md">
          <div class="row items-center no-wrap">
            <q-icon name="front_loader" size="22px" color="secondary" class="q-mr-sm" />
            <q-skeleton type="text" width="60%" />
          </div>
          <q-skeleton type="text" width="80%" class="q-mt-sm" />
          <q-skeleton type="text" width="50%" class="q-mt-sm" />
          <q-skeleton type="text" width="30%" class="q-mt-sm" />
          <div class="row justify-between q-mt-md">
            <q-skeleton type="QBtn" width="45%" height="36px" />
            <q-skeleton type="QBtn" width="45%" height="36px" />
          </div>
        </q-card>
      </template>

      <template v-else>
        <q-card v-for="company in company_list" :key="company.id" class="card-style q-pa-sm q-mb-md">
          <q-btn no-caps flat class="full-width">
            <div class="col items-left">
              <div class="row items-center no-wrap">
                <q-icon name="front_loader" size="22px" color="secondary" class="q-mr-sm" />
                <p class="text-bold text-primary text-subtitle1 no-margin text-truncate">
                  {{ company.company_name }}
                </p>
              </div>

              <div class="text-left q-pa-sm text-caption">
                <p class="text-primary no-margin">Nome: {{ company.user_name }} </p>
                <p class="text-primary no-margin">Empresa: {{ company.company_name }} </p>
                <p class="text-primary no-margin">Função: {{ company.role_name }} </p>
              </div>
            </div>
          </q-btn>

          <div v-if="company.is_invite_accepted === 0" class="q-mt-md row justify-between">
            <q-btn label="Aceitar" color="green" no-caps @click="acceptInvite(company.uuid)" />
            <q-btn label="Não Aceitar" color="red" no-caps @click="declineInvite(company.uuid)" />
          </div>
        </q-card>
      </template>
    </div>
  </q-page>
</template>


<script>
export default {
	name: 'UserInvite',
	data() {
		return {
			loading: true,
			company_list: []
		};
	},
	mounted() {
		this.get_invite_by_logged_user();
	},
	methods: {
		async get_invite_by_logged_user() {
			fetch('https://fortis-api.55technology.com/v1/employee/invite/', {
				headers: { 'token': localStorage.getItem('access_token') }
			})
			.then(response => {
				if (!response.ok) throw new Error('Erro ao buscar convites.');
				return response.json();
			})
			.then(data => {
				this.company_list = data.invites;
				this.loading = false;
			})
			.catch(error => {
				console.error('Erro:', error);
				this.$q.notify({
					color: 'red-5',
					textColor: 'white',
					icon: 'error',
					message: 'Nenhuma convite cadastrado.'
				});
				this.loading = false;
			});
		},

		
		async acceptInvite(company_uuid) {
			fetch(`https://fortis-api.55technology.com/v1/employee/invite/accept/${company_uuid}`, {
				method: 'PUT',
				headers: {
					'Content-Type': 'application/json',
					'token': localStorage.getItem('access_token')
				},
				body: JSON.stringify({ is_invite_accepted: 1 })
			})
			.then(response => {
				if (!response.ok) throw new Error('Erro ao aceitar convite.');
				this.$q.notify({
					color: 'green-5',
					textColor: 'white',
					icon: 'check',
					message: 'Convite aceito com sucesso!'
				});
				this.get_invite_by_logged_user();
			})
			.catch(error => {
				this.$q.notify({
					color: 'red-4',
					textColor: 'white',
					icon: 'error',
					message: error.message,
				});
			});
		},

		
		async declineInvite(company_uuid) {
			fetch(`https://fortis-api.55technology.com/v1/employee/invite/decline/${company_uuid}`, {
				method: 'DELETE',
				headers: {
					'Content-Type': 'application/json',
					'token': localStorage.getItem('access_token')
				}
			})
			.then(response => {
				if (!response.ok) throw new Error('Erro ao recusar convite.');
				this.$q.notify({
					color: 'red-5',
					textColor: 'white',
					icon: 'delete',
					message: 'Convite recusado com sucesso!'
				});
				this.get_invite_by_logged_user();
			})
			.catch(error => {
				this.$q.notify({
					color: 'red-4',
					textColor: 'white',
					icon: 'error',
					message: error.message,
				});
			});
		}
	}
};
</script>

<style scoped>
.text-truncate {
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
	display: block;
	max-width: 80%;
}
.card-style {
	border-radius: 10px;
	max-width: 350px;
}
</style>
