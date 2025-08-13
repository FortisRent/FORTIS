<template>
	<q-layout class="bg-white">
		<div class="q-pa-md flex column items-center">
			<div class="flex justify-around items-center">
				<q-btn
					flat
					round
					icon="arrow_back"
					class="q-mr-sm text-primary"
					color="black"
					style="position: absolute; left: 10px;"
					@click="$router.go(-1)"
				/>
				<p class="text-weight text-h4 text-primary q-mt-md">Editar Dados</p>
			</div>

			<div class="q-pa-md q-mt-md" style="border: 1px solid black; border-radius: 15px;  min-width: 300px; background-color: whitesmoke; ">
				<q-form @submit="on_submit" @reset="on_reset" class="q-gutter-md ">
					<q-input
						filled
                        type="name"
						color="primary"
						v-model="doctor_name"
						label="Nome Completo"
						lazy-rules
						:rules="[ val => val && val.length > 0 || 'Por favor, insira o Nome Completo.']"
						no-error-icon
					/>

					<q-select
						filled
						color="primary"
						v-model="state_crm"
						label="Estado de Emissão CRM"
                        :options="geo_state_list"
                        option-label="label"
                        option-value="value"
                        map-options
						lazy-rules
						:rules="[ val => val && val.length > 0 || 'Por favor, insira o Estado de Emissão do CRM.']"
						no-error-icon
					/>

                    <q-input
						filled
                        mask="######"
						color="primary"
						v-model="crm"
						label="N° CRM"
						lazy-rules
						:rules="[ val => val && val.length > 0 || 'Por favor, insira o n° do CRM.']"
						no-error-icon
					/>

                    <q-input
						filled
                        mask="###.###.###-##"
						color="primary"
						v-model="cpf"
						label="CPF"
						lazy-rules
						:rules="[ val => val && val.length > 0 || 'Por favor, insira o CPF.']"
						no-error-icon
					/>

					<div class="flex column">
						<q-btn label="Limpar" type="reset" color="primary" flat  />
						<q-btn label="Concluir Edição" type="submit" color="primary" />
					</div>
				</q-form>
			</div>
			<q-btn class="q-mt-md" style="margin-left: 200px;" label="voltar" to="/doctor/validation/" color="primary" />
		</div>
	</q-layout>
</template>

<script>
export default {
	name: 'DoctorValidateEdit',

	data() {
		return {
			doctor_name: '',
			state_crm: '',
            crm: '',
            cpf: '',
            geo_state_list: [
                'Acre',
                'Alagoas',
                'Amazonas',
                'Amapá',
                'Bahia',
                'Ceará',
                'Distrito Federal',
                'Espírito Santo',
                'Goiás',
                'Maranhão',
                'Minas Gerais',
                'Mato Grosso do Sul',
                'Mato Grosso',
                'Pará',
                'Paraíba',
                'Pernambuco',
                'Piauí',
                'Paraná',
                'Rio de Janeiro',
                'Rio Grande do Norte',
                'Rondônia',
                'Roraima',
                'Rio Grande do Sul',
                'Santa Catarina',
                'Sergipe',
                'São Paulo',
                'Tocantins',
                'Exterior']
        };
	},

	methods: {
		async on_submit() {

			if (!this.$route.params.doctor_uuid) {
					this.$router.push('/doctor/validation')
			}

			try {
				const response = await fetch(`http://localhost:5510/v1/doctor/validate/data/${this.$route.params.doctor_uuid}`, {
					method: 'PUT',
					headers: {
							'Content-Type': 'application/json',
							'token': localStorage.getItem('access_token')
					},
					body: JSON.stringify({
						doctor_name: this.doctor_name,
						state_crm: this.state_crm,
                        crm: this.crm,
                        cpf: this.cpf
					})
				});

				// Check if request was successful
				if (response.ok) {
					this.$router.push(`/doctor/validation/${this.$route.params.doctor_uuid}`);
				} else {
					alert('Registration failed. Please try again.');
				}
			} catch (error) {
				console.error('Error:', error);
				alert('An error occurred. Please try again later.');
			}
		},

		check_login_status() {
			if (!localStorage.getItem('access_token')) {
				alert('Not logged in');
				this.$router.push('/login');
			}
		},

		async get_health_data() {
			fetch(`http://localhost:5510/v1/health/logged/`, {
				method:'GET',
				headers:{'token': localStorage.getItem('access_token')}})
				.then(response => {
					if (!response.ok) {
						throw new Error('Network response was not ok');
					}
					return response.json();
				})
				.then(data => {
					this.weight=data.health_infos[0].weight;
					this.height=data.health_infos[0].height;
				})
				.catch(error => {
					console.error('Error fetching data:', error);

					this.$q.notify({
						color: 'red-5',
						textColor: 'white',
						icon: 'cloud_done',
						message: 'Ops! Falha ao carregar dados.'
					});
				});
		}

	},
	mounted(){
		this.get_health_data();
		this.check_login_status();
	}
};
</script>