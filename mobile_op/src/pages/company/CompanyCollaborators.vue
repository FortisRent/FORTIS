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
			<div class="text-h6 text-primary text-bold q-ml-md">
				Meus Colaboradores
			</div>
		</div>

		<q-separator class="bg-grey" />

		<div class="q-pa-md">
			<q-card class="card-style q-pa-sm q-mb-md">
				<q-btn no-caps flat :to="`/user/company/collaborators/${this.$route.params.company_uuid}/create`" class="full-width no-padding">
					<div class="row justify-center items-center full-width">
						<q-icon name="add_circle" size="25px" color="secondary" />
						<p class="text-subtitle2 text-primary no-margin q-ml-sm">
							Adicionar Colaborador
						</p>
					</div>
				</q-btn>
			</q-card>

			<!-- Lista de colaboradores -->
			<q-card v-for="employee in employee_list" :key="employee.uuid" class="card-style q-pa-sm q-mb-md">
				<div class="row flex justify-between q-mb-none">
					<div class="row items-center no-wrap">
						<q-icon name="person" size="22px" color="secondary" class="q-mr-sm" />
						<p class="text-bold text-primary text-subtitle1 no-margin">
							{{ employee.employee_name }}
						</p>
					</div>
					<q-btn icon="delete" rounded size="10px" flat color="black" @click="confirmDelete(employee.uuid)" />
				</div>

				<!-- Status -->
				<div class="text-left q-pa-sm text-caption">
					<p class="no-margin text-truncate">
						Status: 
						<span :class="employee.is_invite_accepted ? 'text-green' : 'text-red'">
							{{ employee.is_invite_accepted ? 'ACEITO' : 'PENDENTE' }}
						</span>
					</p>
				</div>

				<!-- Cargo -->
				<div class="text-left q-pa-sm text-caption">
					<p class="text-primary no-margin text-truncate">
						Cargo: {{ employee.role_name }}
					</p>
				</div>

				<!-- Empresa -->
				<div class="text-left q-pa-sm text-caption">
					<p class="text-primary no-margin text-truncate">
						Empresa: {{ employee.company_name }}
					</p>
				</div>
			</q-card>
		</div>

		<!-- Diálogo de Confirmação -->
		<q-dialog v-model="confirmDialog">
			<q-card>
				<q-card-section class="row items-center">
					<q-icon name="warning" color="red" size="24px" class="q-mr-sm" />
					<span class="text-h6">Confirmar exclusão</span>
				</q-card-section>

				<q-card-section>
					Você tem certeza de que deseja excluir este colaborador? Esta ação não pode ser desfeita.
				</q-card-section>

				<q-card-actions align="right">
					<q-btn flat label="Cancelar" color="grey" v-close-popup />
					<q-btn flat label="Excluir" color="red" @click="deleteEmployee" />
				</q-card-actions>
			</q-card>
		</q-dialog>
	</q-page>
</template>

<script>
export default {
	name: 'MachineList',
	data() {
		return {
			loading: true,
			employee_list: [],
			confirmDialog: false,
			employeeToDelete: null
		};
	},
	mounted() {
		this.get_employee_by_company_uuid();
	},
	methods: {
		async get_employee_by_company_uuid() {
			fetch(`https://fortis-api.55technology.com/v1/employee/company/${this.$route.params.company_uuid}`, {
				headers: { 'token': localStorage.getItem('access_token') }
			})
			.then(response => {
				if (!response.ok) {
					throw new Error('Erro ao buscar colaboradores.');
				}
				return response.json();
			})
			.then(data => {
				this.employee_list = data.employees;
				this.loading = false;
			})
			.catch(error => {
				console.error('Erro:', error);
				this.$q.notify({
					color: 'red-5',
					textColor: 'white',
					icon: 'error',
					message: 'Nenhum funcionário cadastrado.'
				});
				this.loading = false;
			});
		},

		
		confirmDelete(employee_uuid) {
			this.employeeToDelete = employee_uuid;
			this.confirmDialog = true;
		},

		
		async deleteEmployee() {
			if (!this.employeeToDelete) return;

			fetch(`https://fortis-api.55technology.com/v1/employee/${this.employeeToDelete}`, {
				method: 'DELETE',
				headers: {
					'Content-Type': 'application/json',
					'token': localStorage.getItem('access_token')
				}
			})
			.then(response => {
				if (!response.ok) throw new Error('Erro ao excluir colaborador.');
				this.$q.notify({
					color: 'red-5',
					textColor: 'white',
					icon: 'delete',
					message: 'Colaborador excluído com sucesso!'
				});
				this.confirmDialog = false;
				this.get_employee_by_company_uuid();
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
	max-width: 100%;
}
.card-style {
	border-radius: 10px;
	max-width: 350px;
}
.text-green {
	color: green;
	font-weight: bold;
}
.text-red {
	color: red;
	font-weight: bold;
}
</style>
