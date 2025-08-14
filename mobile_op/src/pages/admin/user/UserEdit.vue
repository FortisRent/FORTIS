<template>
	<div class="q-pa-md" style="max-width: 650px">
	  <h5 class="q-mb-xl">Editar usuário</h5>
	  <q-form @submit="on_submit" @reset="on_reset" class="q-gutter-md">

		<q-input
			filled
			dark
			v-model="name"
			label="Nome"
			lazy-rules
			:rules="[ val => val && val.length > 0 || 'Por favor, insira o nome do usuário.']"
		/>

		<q-input
			filled
			dark
			v-model="cpf"
			label="Cpf"
			mask="###.###.###-##"
			lazy-rules
			:rules="[ val => val && val.length > 0 || 'Por favor, insira o cpf do usuário.']"
		/>

		<q-input
			filled
			dark
			v-model="rg"
			label="RG"
			mask="#.###-###"
			lazy-rules
			:rules="[ val => val && val.length > 0 || 'Por favor, insira o RG do usuário.']"
		/>

		<q-input
			filled
			dark
			type="date"
			v-model="birthdate"
			label="Data de Nascimento"
			lazy-rules
			:rules="[ val => val && val.length > 0 || 'Por favor, insira quando consumiu o remédio.']"
		/>

		<q-input
			filled
			dark
			v-model="phone"
			label="Celular"
			mask="(##) # ####-####"
			lazy-rules
			:rules="[ val => val && val.length > 0 || 'Por favor, insira o número celular.']"
		/>

		<q-input
			filled
			dark
			v-model="emergency_name"
			label="Nome do contato de emergência"
			lazy-rules
			:rules="[ val => val && val.length > 0 || 'Por favor, insira o nome do contato de emergência.']"
		/>

		<q-input
			filled
			dark
			v-model="emergency_number"
			label="Número de emergência"
			mask="(##) # ####-####"
			lazy-rules
			:rules="[ val => val && val.length > 0 || 'Por favor, insira o número de emergência.']"
		/>

		<div>
		  <q-btn label="Limpar" type="reset" color="primary" flat class="q-ml-sm" />
		  <q-btn label="Salvar" type="submit" color="primary" />
		</div>
	  </q-form>
	</div>
  </template>
<script>
	export default {
		data() {
			return {
				name: '',
				email: '',
				cpf: '',
				rg: '',
				birthdate: '',
				phone: '',
				emergency_name: '',
				emergency_number: '',
			};
		},
		mounted() {
			this.check_login_status();
			this.get_user_data();
		},
		methods: {
			async on_submit() {

				if (!this.$route.params.user_uuid) {
					this.$router.push('/admin/user')
				}

				const data = {
					name: 				this.name,
					email: 				this.email,
					cpf: 				this.cpf,
					rg: 				this.rg,
					birthdate: 			this.birthdate,
					phone: 				this.phone,
					emergency_number: 	this.emergency_number,
					emergency_name: 	this.emergency_name,
					user_uuid: 			this.$route.params.user_uuid
				};

				try {
					const response = await fetch(`https://fortis-api.55technology.com/v1/user/${this.$route.params.user_uuid}`, {
						method: 'PUT',
						headers: {
							'Content-Type': 'application/json',
							'token': localStorage.getItem('access_token')
						},
						body: JSON.stringify(data)
					});

					// Check if request was successful
					if (response.ok) {
						this.$router.push('/admin/user');

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
			async get_user_data() {
				fetch(`https://fortis-api.55technology.com/v1/user/${this.$route.params.user_uuid}`)
				.then(response => {
					if (!response.ok) {
						throw new Error('Network response was not ok');
					}
					return response.json();
				})
				.then(data => {
					this.name = data.user.name;
					this.cpf = data.user.cpf;
					this.rg = data.user.rg;
					this.birthdate = data.user.birthdate;
					this.phone = data.user.phone;
					this.emergency_name = data.user.emergency_name;
					this.emergency_number = data.user.emergency_number;
				})
				.catch(error => {
					console.error('Error fetching data:', error);

					$q.notify({
						color: 'red-5',
						textColor: 'white',
						icon: 'cloud_done',
						message: 'Ops! Falha ao carregar dados.'
					});

					loading.value = false;
				});
			}
		}
	};
</script>
