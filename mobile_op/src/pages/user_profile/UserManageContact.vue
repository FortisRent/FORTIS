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
				Informações de Contato
			</div>
		</div>

		<q-separator class="bg-grey" />

        <div class="q-pa-md">
			<q-card class="card-style q-pa-sm q-mb-md">
                <div class="row flex justify-between q-mb-none">
                    <div class="row items-center no-wrap">
						<q-icon
							name="call"
							size="22px"
							color="secondary"
							class="q-mr-sm"
						/>
						<p class="text-bold text-primary text-subtitle1 no-margin">
							Telefone Pessoal
						</p>
					</div>
                    <q-btn
						icon="edit"
                        clickcable
						rounded
                        size="10px"
                        flat
                        color="black"
						to="/user/manage/edit/phone"
                    />
                </div>
                <div class="text-left q-pa-sm text-caption text-truncate">
                    <p class="text-primary no-margin">Número de Telefone: </p>
                    <p class="text-primary no-margin">Outro Telefone<span class="text-grey">(opcional)</span>:</p>
                </div>
            </q-card>

            <q-card class="card-style q-pa-sm q-mb-md">
                <div class="row flex justify-between q-mb-none">
                    <div class="row items-center no-wrap">
						<q-icon
							name="call"
							size="22px"
							color="secondary"
							class="q-mr-sm"
						/>
						<p class="text-bold text-primary text-subtitle1 no-margin">
							Contato de Emergência
						</p>
					</div>
                    <q-btn
						icon="edit"
                        clickcable
						rounded
                        size="10px"
                        flat
                        color="black"
						to="/user/manage/edit/emergency"
                    />
                </div>
                <div class="text-left q-pa-sm text-caption text-truncate">
                    <p class="text-primary no-margin">Nome: </p>
                    <p class="text-primary no-margin">Número de Telefone: </p>
                </div>
            </q-card>

			<q-card class="card-style q-pa-sm q-mb-md">
				<div class="row justify-center items-center">
					<q-icon
						name="add_circle"
						size="25px"
						color="secondary"
						class="no-margin"
					/>
					<p class="text-subtitle2 text-primary no-margin q-ml-sm">
						Adicionar Contato de Emergência
					</p>
				</div>
            </q-card>
        </div>
	</q-page>  
</template>

<script>
export default {
	name: "UserProfile",
	data() {
		return {
			loaded: false,
			selfie_url: null,
			user_name: "",
			user_email: "",
			user_phone: "",
			user_cpf: "",
			user_birthdate: "",
			emergency_name: "",
			emergency_number: "",
			headers: () => [ { name: 'token', value: localStorage.getItem('access_token') } ],
		};
	},
	mounted() {
		this.get_user_data();
	},
	methods: {
			ToEdit() {
				this.$router.push('/user/manage/edit/')



			},
		async get_user_data() {
			try {
				const response = await fetch(`https://fortis-api.55technology.com/v1/user/logged/`, {
					method: "GET",
					headers: { token: localStorage.getItem("access_token") },
				});

				if (!response.ok) throw new Error("Network response was not ok");

				const data = await response.json();
				this.user_name = data.user.name;
				this.user_email = data.user.email;
				this.user_phone = data.user.phone;
				this.user_cpf = data.user.cpf;
				this.user_birthdate = data.user.birthdate;
				this.emergency_name = data.user.emergency_name;
				this.emergency_number = data.user.emergency_number;
				this.selfie_url = data.user.selfie_url;

				this.loaded = true;
			} catch (error) {
				// this.$q.notify({
				//   color: "red-5",
				//   textColor: "white",
				//   icon: "cloud_done",
				//   message: "Ops! Falha ao carregar dados.",
				// });
			}
		},
		
		async refresh(done) {
			await this.get_user_data();
			done();
		},

		on_rejected(){
			this.$q.notify({
				color: 'red-4',
				textColor: 'white',
				icon: 'cloud_done',
				message: "Falha ao enviar foto."
			});
		},	
		on_upload(info){
			this.$q.notify({
				color: 'green-6',
				textColor: 'white',
				icon: 'cloud_done',
				message: "foto enviada com sucesso!."
			});

			const xhr = info.xhr;
			const data = JSON.parse(xhr.response);

			this.selfie_url = null;
			this.selfie_url = data.selfie_url;
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