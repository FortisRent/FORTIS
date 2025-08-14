<template>
	<q-page class="bg-white">
		<div class="row justify-around items-center q-pa-md q-mt-lg">
			<q-btn
				flat
				round
				icon="chevron_left"
				class="text-secondary"
				color="secondary"
				size="18px"
				style="position: absolute; left: 10px;"
				@click="$router.go(-1)"
			/>
			<div class="text-h6 text-primary text-bold q-ml-md">
				Cadastrar Taxas
			</div>
		</div>

		<q-separator class="bg-grey" />

		<div class="q-pa-md">
			<q-form @submit="on_submit" @reset="on_reset" class="q-gutter-md q-pa-md">
				<q-input
					rounded
					color="secondary"
					v-model="name"
					label="Tipo de taxa"
					lazy-rules
					:rules="[ val => val && val.length > 0 || 'Por favor, insira o nome da empresa.']"
					no-error-icon
				/>

				<q-input
					rounded
					color="secondary"
					v-model="amount"
					label="Valor da taxa"
					mask="R$ ###.###,##"
					reverse-fill-mask
					unmasked-value
					lazy-rules
					:rules="[ val => val && val.length > 0 || 'Por favor, insira o Valor']"
					no-error-icon
				/> 
	
				<div class="flex column">
					<q-btn label="Limpar" type="reset" color="secondary" flat  />
					<q-btn label="Cadastrar"  @click="insert_company" color="secondary" />
				</div>
			</q-form>
		</div>
	</q-page>
</template>
<script>
	export default {
		data() {
			return {
				name:'',
				amount:'',

			};
		},
		mounted() {
			this.check_login_status();
		},
		methods: {
			check_login_status() {
				if (!localStorage.getItem('access_token')) {
					alert('Not logged in');
					this.$router.push('/login');
				}
			},
			async insert_company(){
				fetch(`https://fortis-api.55technology.com/v1/company/`, {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						'token': localStorage.getItem('access_token')
					},
					body: JSON.stringify({
						name: this.name,
						cnpj: this.cnpj,

					}),
				})
				.then(response => {
					if (!response.ok) {
						throw new Error('Por favor, preencha todos os campos.');
					}

					this.$q.notify({
					  color: 'green-4',
					  textColor: 'white',
					  icon: 'cloud_done',
					  message: 'Nova empresa cadastrada.',
					});

					this.$router.go(-1)
				})
				.catch(error => {
					this.$q.notify({
					  color: 'red-4',
					  textColor: 'white',
					  icon: 'cloud_done',
					  message: error.message,
					});
				});
			},	

		}
	};
</script>