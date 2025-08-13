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
			<q-card class="card-style q-pa-sm q-mb-md">
				<q-btn no-caps flat :to='`/user/machine/list/${this.$route.params.company_uuid}`' class="full-width">
					<div class="row items-center col items-left justify-start">
						<q-icon name="front_loader" size="22px" color="secondary" class="q-mr-sm"/>
						<p class="text-bold text-primary text-subtitle1 no-margin">
							Meus Equipamentos
						</p>
					</div>
				</q-btn>
			</q-card>

            <q-card class="card-style q-pa-sm q-mb-md">
				<q-btn no-caps flat :to='`/user/company/projects/${this.$route.params.company_uuid}`' class="full-width">
					<div class="row items-center col items-left justify-start">
						<q-icon name="analytics" size="22px" color="secondary" class="q-mr-sm"/>
						<p class="text-bold text-primary text-subtitle1 no-margin">
							Projetos
						</p>
					</div>
				</q-btn>
			</q-card>
			
            <!-- <q-card class="card-style q-pa-sm q-mb-md">
				<q-btn no-caps flat to="/user/company/budgets" class="full-width">
					<div class="row items-center col items-left justify-start">
						<q-icon name="request_page" size="22px" color="secondary" class="q-mr-sm"/>
						<p class="text-bold text-primary text-subtitle1 no-margin">
							Orçamentos
						</p>
					</div>
				</q-btn>
			</q-card> -->
			
			<q-card class="card-style q-pa-sm q-mb-md">
				<q-btn no-caps flat :to='`/dashboard/${this.$route.params.company_uuid}`' class="full-width">
					<div class="row items-center col items-left justify-start">
						<q-icon name="request_page" size="22px" color="secondary" class="q-mr-sm"/>
						<p class="text-bold text-primary text-subtitle1 no-margin">
							Dashboard Teste
						</p>
					</div>
				</q-btn>
			</q-card>
			
            <q-card class="card-style q-pa-sm q-mb-md">
				<q-btn no-caps flat :to='`/user/company/collaborators/${this.$route.params.company_uuid}`' class="full-width">
					<div class="row items-center col items-left justify-start">
						<q-icon name="group" size="22px" color="secondary" class="q-mr-sm"/>
						<p class="text-bold text-primary text-subtitle1 no-margin">
							Meus Colaboradores
						</p>
					</div>
				</q-btn>
			</q-card>

            <q-card class="card-style q-pa-sm q-mb-md">
				<q-btn no-caps flat :to='`/user/company/edit/${this.$route.params.company_uuid}`' class="full-width">
					<div class="row items-center col items-left justify-start">
						<q-icon name="edit" size="22px" color="secondary" class="q-mr-sm"/>
						<p class="text-bold text-primary text-subtitle1 no-margin">
							Editar Dados da Empresa
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
				fetch(`http://localhost:5510/v1/company/${this.$route.params.company_uuid}`, {
				method:'GET',
				headers:{'token': localStorage.getItem('access_token')}})
				.then(response => {
					if (!response.ok) {
					throw new Error('Network response was not ok');
					}
					return response.json();
				})
				.then(data => {
					console.table(data.company)
					// Sync user object with API data.
					this.name=data.company.name;
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