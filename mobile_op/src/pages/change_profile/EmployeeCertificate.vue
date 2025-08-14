<template>
	<q-page class="bg-white">
		<div class="row justify-around items-center q-pa-md q-mt-lg">
			<q-btn flat round icon="chevron_left" class="text-primary" color="secondary" size="18px"
				style="position: absolute; left: 10px;" @click="$router.go(-1)" />
			<div class="text-h6 text-primary text-bold q-ml-md">
				Certificações
			</div>
		</div>

		<q-separator class="bg-grey" />

		<div class="q-pa-md">
			<template v-if="loading">
				<div class="row justify-center q-gutter-md">
					<q-card v-for="n in 3" :key="n" class="card-style q-pa-md q-mb-md" style="width: 300px;">
						<div class="column items-center q-pa-sm text-caption">
							<q-icon name="picture_as_pdf" size="22px" color="secondary" class="q-mr-sm" />
							<q-skeleton type="text" width="80%" height="1rem" />
						</div>
					</q-card>
				</div>
			</template>


			<template v-else>
				<q-card v-for="employees in employees_list" :key="employees.id" class="card-style q-pa-sm q-mb-md">
					<q-btn no-caps flat class="full-width" @click="openImage(employees.file_url)">
						<q-icon name="picture_as_pdf" size="22px" color="secondary" class="q-mr-sm" />

						<div class="text-left q-pa-sm text-caption">
							<p class="text-primary no-margin">Nome: {{ employees.certification_name }} </p>
							<p class="text-primary no-margin">Detalhes: {{ employees.details }} </p>
						</div>
					</q-btn>
				</q-card>
			</template>

			<q-card class="card-style q-pa-sm q-mb-md">
				<q-btn no-caps flat :to='`/user/manage/employee/certificate/create/${uuid}`' class="full-width no-padding">
					<div class="row justify-center items-center full-width">
						<q-icon name="add_circle" size="25px" color="secondary" />
						<p class="text-subtitle2 text-primary no-margin q-ml-sm">
							Cadastrar Certificado
						</p>
					</div>
				</q-btn>
			</q-card>
			<!-- Dialog to show the image -->
			<q-dialog v-model="imageDialog">
				<q-card class="q-pa-md">
					<q-card-section class="q-pt-none">
						<img :src="imageSrc" alt="Imagem do Certificado" class="full-width" />
					</q-card-section>
					<q-btn flat label="Fechar" @click="imageDialog = false" color="primary" />
				</q-card>
			</q-dialog>

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
			imageDialog: false,
			imageSrc: '',
		};
	},
	mounted() {
		this.get_employees_list();
		this.get_employee_by_logged();
	},
	methods: {
		async get_employees_list() {
			fetch(`https://fortis-api.55technology.com/v1/employee/certification/${this.$route.params.employee_uuid}`, {
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
		openImage(fileUrl) {
			this.imageSrc = `https://fortis-api.55technology.com${fileUrl}`;
			this.imageDialog = true;  // Open the dialog
		},
		async get_employee_by_logged() {
			try {
				const response = await fetch(`https://fortis-api.55technology.com/v1/employee/logged/`, {
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