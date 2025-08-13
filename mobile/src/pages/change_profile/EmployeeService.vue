<template>
	<q-page class="bg-white">
		<div class="row justify-around items-center q-pa-md q-mt-lg">
			<q-btn flat round icon="chevron_left" class="text-primary" color="secondary" size="18px"
				style="position: absolute; left: 10px;" @click="$router.go(-1)" />
			<div class="text-h6 text-primary text-bold q-ml-md">
				Serviços
			</div>
		</div>

		<q-separator class="bg-grey" />

		<div class="q-pa-md">
			<q-list>
				<template v-if="loading">
					<div class="q-pa-md">
						<q-list>
							<q-card class="q-pa-sm q-mb-md" style="height: 330px; border-color: secondary;" bordered>
								<div class="row items-center col items-left justify-start">
									<div class="text-left full-width">
										<q-skeleton type="text" width="30%" />
										<q-separator color="secondary" class="q-my-sm" />
										<div class="column q-gutter-sm">
											<q-skeleton type="text" width="50%" />
											<q-skeleton type="text" width="40%" />
											<q-skeleton type="text" width="60%" />
											<q-skeleton type="text" width="55%" />
											<q-skeleton type="text" width="45%" />
										</div>
									</div>
								</div>
							</q-card>
						</q-list>
					</div>
				</template>


				<template v-else>
					<q-card class=" q-pa-sm q-mb-md" style="height: 330px; border-color: secondary;" bordered>
						<q-btn no-caps flat to="``" class="full-width">
							<div class="row items-center col items-left justify-start">
								<div class="text-left">
									<span class="text-primary"><strong class="text-primary">Cod:</strong> </span>
									<q-separator color="secondary" />
									<div class="text-left">
										<p class="q-mt-sm">
											<strong class="text-primary">Status: </strong>
											<span class="text-red text-bold"> PENDENTE</span>
										</p>
										<p class="text-primary"><strong>Data:</strong> </p>
										<p class="text-primary"><strong>Cliente:</strong> </p>
										<p class="text-primary"><strong>Endereço:</strong> </p>
										<p class="text-primary"><strong>CEP: </strong></p>
									</div>
								</div>
							</div>
						</q-btn>
						<div class="flex justify-center">


						</div>
					</q-card>
				</template>
			</q-list>
		</div>
	</q-page>
</template>

<script>
export default {
	name: 'employeesList',
	data() {
		return {
			loading: true,
			employees_list: [],
			uuid: '',
		};
	},
	mounted() {
		this.get_employees_list();
		this.get_employee_by_logged();
	},
	methods: {
		async get_employees_list() {
			fetch(`http://localhost:5510/v1/employee/certification/${this.$route.params.employee_uuid}`, {
				headers: { 'token': localStorage.getItem('access_token') }
			})
				.then(response => {
					if (!response.ok) {
						throw new Error('Network response was not ok');
					}
					return response.json();
				})
				.then(data => {
					console.table(data.employees);
					this.employees_list = data.employees;
					this.loading = false;
					this.uuid = data.employees[0].employee_uuid;
				})
				.catch(error => {
					console.error('Error fetching data:', error);
					this.$q.notify({
						color: 'red-5',
						textColor: 'white',
						icon: 'cloud_done',
						message: 'Nenhuma Certificado cadastrado.'
					});
					this.loading = false;
				});
		},

		async get_employee_by_logged() {
			try {
				const response = await fetch(`http://localhost:5510/v1/employee/logged/`, {
					method: "GET",
					headers: { token: localStorage.getItem("access_token") },
				});

				if (!response.ok) throw new Error("Network response was not ok");

				const data = await response.json();
				this.employee = data.employees[0]; // Pegando o primeiro item da lista
				this.uuid = data.employees[0].uuid;
				this.loaded = true;
			} catch (error) {
				this.$q.notify({
					color: "red-5",
					textColor: "white",
					icon: "cloud_done",
					message: "Ops! Falha ao carregar dados.",
				});
			}
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
	min-width: 350px;
}
</style>