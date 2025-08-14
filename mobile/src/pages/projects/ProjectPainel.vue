<template>
	<q-page class="bg-white">
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
			<div class="text-truncate text-h6 text-primary text-bold q-ml-md">
				{{ name }}
			</div>
		</div>

		<q-separator class="bg-grey" />

        <div class="q-pa-md">
			<q-card v-if="status_name === null" class="card-style q-pa-sm q-mb-md">
				<q-btn
					no-caps
					flat
					:to="`/user/project/recomend/${this.$route.params.project_uuid}`"
					class="full-width"
				>
					<div class="row items-center col items-left justify-start">
						<q-icon name="screen_share" size="22px" color="secondary" class="q-mr-sm"/>
						<p class="text-bold text-primary text-subtitle1 no-margin">
							Consultar disponibilidade
						</p>
					</div>
				</q-btn>
			</q-card>
			<!-- <q-card class="card-style q-pa-sm q-mb-md">
				<q-btn no-caps flat :to='`/user/project/date/${this.$route.params.project_uuid}`' class="full-width">
					<div class="row items-center col items-left justify-start">
						<q-icon name="calendar_month" size="22px" color="secondary" class="q-mr-sm"/>
						<p class="text-bold text-primary text-subtitle1 no-margin">
							Agendamento
						</p>
					</div>
				</q-btn>
			</q-card> -->

            <q-card class="card-style q-pa-sm q-mb-md">
				<q-btn no-caps flat :to='`/user/project/details/${this.$route.params.project_uuid}`' class="full-width">
					<div class="row items-center col items-left justify-start">
						<q-icon name="analytics" size="22px" color="secondary" class="q-mr-sm"/>
						<p class="text-bold text-primary text-subtitle1 no-margin">
							Detalhes do Projeto
						</p>
					</div>
				</q-btn>
			</q-card>
			
            <q-card class="card-style q-pa-sm q-mb-md">
				<q-btn no-caps flat :to='`/user/project/${this.$route.params.project_uuid}`' class="full-width">
					<div class="row items-center col items-left justify-start">
						<q-icon name="request_page" size="22px" color="secondary" class="q-mr-sm"/>
						<p class="text-bold text-primary text-subtitle1 no-margin">
							Orçamentos 
						</p>
					</div>
				</q-btn>
			</q-card>
			
            <!-- <q-card class="card-style q-pa-sm q-mb-md">
				<q-btn no-caps flat to="``" class="full-width">
					<div class="row items-center col items-left justify-start">
						<q-icon name="history" size="22px" color="secondary" class="q-mr-sm"/>
						<p class="text-bold text-primary text-subtitle1 no-margin">
							Histórico
						</p>
					</div>
				</q-btn>
			</q-card> -->

			<q-card v-if="status_name === 'ACEITO'" class="card-style q-pa-sm q-mb-md">
				<q-btn no-caps flat :to='`/user/project/date/list/${this.$route.params.project_uuid}`' class="full-width">
					<div class="row items-center col items-left justify-start">
						<q-icon name="calendar_month" size="22px" color="secondary" class="q-mr-sm"/>
						<p class="text-bold text-primary text-subtitle1 no-margin">
							Resumo Agendamento
						</p>
					</div>
				</q-btn>
			</q-card>

        </div>
	</q-page>  
</template>

<script>
	export default {
		name: 'CompanyOptions',
		data() {
			return {
				loading: true,
				name:'',
				status_name:'',
			};
		},
		mounted() {
			this.get_company_by_uuid();
		},
		methods: {
			to_edit() {
				this.$router.push('/user/manage/edit/')
			},
			async get_company_by_uuid() {
				fetch(`https://fortis-api.55technology.com/v1/project/${this.$route.params.project_uuid}`, {
				method:'GET',
				headers:{'token': localStorage.getItem('access_token')}})
				.then(response => {
					if (!response.ok) {
					throw new Error('Network response was not ok');
					}
					return response.json();
				})
				.then(data => {
					console.table(data)
					// Sync user object with API data.
					 this.name=data.project.project_name;
					 this.status_name = data.project.status_name;
				})
				.catch(error => {
					// console.error('Error fetching data:', error);
		
					this.$q.notify({
					  color: 'red-5',
					  textColor: 'white',
					  icon: 'cloud_done',
					  message: 'Ops! Falha ao carregar dados.'
					});
				});
        	}, 
		},
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