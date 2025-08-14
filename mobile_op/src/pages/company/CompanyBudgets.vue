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
				Orçamentos
			</div>
		</div>

		<q-separator class="bg-grey" />

        <div class="q-pa-md">
			<!-- <q-card class="card-style q-pa-sm q-mb-md">
				<q-btn no-caps flat to="/user/manage/info/addaddress" class="full-width no-padding">
					<div class="row justify-center items-center full-width">
						<q-icon name="add_circle" size="25px" color="secondary" />
						<p class="text-subtitle2 text-primary no-margin q-ml-sm">
							Adicionar Orçamento
						</p>
					</div>
				</q-btn>
            </q-card> -->
			<q-card class="card-style q-pa-sm q-mb-md">
                <div class="row flex justify-between q-mb-none">
                    <div class="row items-center no-wrap">
						<q-icon
							name="analytics"
							size="22px"
							color="secondary"
							class="q-mr-sm"
						/>
						<p class="text-bold text-primary text-subtitle1 no-margin">
							Nome do Projeto
						</p>
					</div>
                    <q-btn
						icon="delete"
                        clickcable
						rounded
                        size="10px"
                        flat
                        color="black"
                    />
                </div>
                <div class="text-left q-pa-sm text-caption">
                    <p class="text-primary no-margin text-truncate">Data do Serviço: </p>
					<p class="text-primary no-margin text-truncate">Equipamentos: </p>
					<p class="text-primary no-margin text-truncate">Contato: </p>
					<p class="text-primary no-margin text-truncate">Valor: </p>
                </div>
            </q-card>
        </div>
	</q-page>  
</template>

<script>
	export default {
		name: 'MachineList',
		data() {
			return {
				loading: true,
				machine_list: []
			};
		},
		mounted() {
			this.get_machine_list();
		},
		methods: {
			to_edit() {
				this.$router.push('/user/manage/edit/')
			},
			async get_machine_list() {
				fetch('https://fortis-api.55technology.com/v1/machine/company/' + this.$route.params.company_uuid, {
					headers: { 'token': localStorage.getItem('access_token') }
				})
				.then(response => {
					if (!response.ok) {
						throw new Error('Network response was not ok');
					}
					return response.json();
				})
				.then(data => {
					console.table(data.machine);
					this.machine_list = data.machine;
					this.loading = false;
				})
				.catch(error => {
					console.error('Error fetching data:', error);
					this.$q.notify({
						color: 'red-5',
						textColor: 'white',
						icon: 'cloud_done',
						message: 'Nenhuma empresa cadastrada.'
					});
					this.loading = false;
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
		max-width: 100%;
	}
	.card-style {
		border-radius: 10px;
		max-width: 350px;
	}
</style>