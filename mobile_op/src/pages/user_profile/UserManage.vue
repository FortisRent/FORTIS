<template>
	<q-page class="bg-white">
		<q-pull-to-refresh @refresh="refresh">
			<div class="text-bold text-h5 text-primary text-left q-mb-sm q-ml-md q-pt-xl">
				Painel de Configurações
			</div>

			<q-separator class="bg-grey" />

			<!-- Informações do Perfil -->
			<div class="row reverse items-center justify-between">
				<div class="col text-left q-ml-md q-mr-md q-mb-lg">
					<h6 v-if="loaded" class="q-mb-none text-primary text-bold text-truncate">{{ full_name }}</h6>
					<q-skeleton v-else type="text" width="150px" />

					<p v-if="loaded" class="text-grey-7 text-caption text-truncate">{{ user_email }}</p>
					<q-skeleton v-else type="text" width="100px" />
				</div>

				<div class="col flex flex-column items-center justify-around">
					<q-avatar v-if="loaded" size="90px" class="q-mt-md">
						<q-img
							v-if="profile_picture_url"
							:src="`https://fortis-api.55technology.com/${profile_picture_url}`"
							width="90px"
							height="90px"
							fit="cover"
							style="border-radius: 100px;"
						/>
						<q-icon v-if="!profile_picture_url"
								name="person"
								size="55px"
								color="secondary"
								class="q-mr-sm"
							/>
					</q-avatar>
					<q-skeleton v-else type="QAvatar" size="80px" class="q-mb-sm" />
				</div>
			</div>

			<q-list separator class="text-primary text-left q-mt-xs">

				<q-item class="q-mb-sm q-mt-sm" clickable @click="ToEdit">
					<q-btn flat no-caps>
						<q-icon
							name="person"
							class="icon-bordered q-mr-lg"
							color="secondary"
							size="md"
						/>
						<div class="column flex text-left item-size">
							<div v-if="loaded" class="text-truncate text-subtitle1 text-bold">Informações Pessoais</div>
							<div v-if="loaded" class="text-truncate text-caption text-grey">Nome, Endereço, Foto de Perfil</div>
							<q-skeleton v-else type="text" width="80%" />
						</div>
					</q-btn>
				</q-item>

				<!-- <q-item class="q-mb-sm q-mt-sm" clickable to="/user/manage/contact">
					<q-btn flat no-caps>
						<q-icon
							name="call"
							class="icon-bordered q-mr-lg"
							color="secondary"
							size="md"
						/>
						<div class="column flex text-left item-size">
							<div v-if="loaded" class="text-truncate text-subtitle1 text-bold">Informações de Contato</div>
							<div v-if="loaded" class="text-truncate text-caption text-grey">Telefone, Contato de Emergência</div>
							<q-skeleton v-else type="text" width="80%" />
						</div>
					</q-btn>
				</q-item> -->

				<q-item class="q-mb-sm q-mt-sm" clickable to="/user/manage/profilechange">
					<q-btn flat no-caps>
						<q-icon
							name="groups"
							class="icon-bordered q-mr-lg"
							color="secondary"
							size="md"
						/>
						<div class="column flex text-left item-size">
							<div v-if="loaded" class="text-truncate text-subtitle1 text-bold">Perfil Colaborador</div>
							<div v-if="loaded" class="text-truncate text-caption text-grey">Operador, financeiro, Consultor Técnico</div>
							<q-skeleton v-else type="text" width="80%" />
						</div>
					</q-btn>
				</q-item>

				<!-- <q-item class="q-mb-sm q-mt-sm" clickable to="/user/company">
					<q-btn flat no-caps>
						<q-icon
							name="front_loader"
							class="icon-bordered q-mr-lg"
							color="secondary"
							size="md"
						/>
						<div class="column flex text-left item-size">
							<div v-if="loaded" class="text-truncate text-subtitle1 text-bold">Tenho uma Empresa</div>
							<div v-if="loaded" class="text-truncate text-caption text-grey">Cadastrar Empresa</div>
							<q-skeleton v-else type="text" width="80%" />
						</div>
					</q-btn>
				</q-item> -->
				
				<q-item class="q-mb-sm q-mt-sm" clickable to="/user/manage/invite">
					<q-btn flat no-caps>
						<q-icon
							name="mail"
							class="icon-bordered q-mr-lg"
							color="secondary"
							size="md"
						/>
						<div class="column flex text-left item-size">
							<div v-if="loaded" class="text-truncate text-subtitle1 text-bold">Convites</div>
							<div v-if="loaded" class="text-truncate text-caption text-grey">Fazer parte de uma Empresa</div>
							<q-skeleton v-else type="text" width="80%" />
						</div>
					</q-btn>
				</q-item>
				
				<!-- <q-item class="q-mb-sm q-mt-sm">
					<q-btn flat no-caps>
						<q-icon
							name="credit_card"
							class="icon-bordered q-mr-lg"
							color="secondary"
							size="md"
						/>
						<div class="column flex text-left item-size">
							<div v-if="loaded" class="text-truncate text-subtitle1 text-bold">Formas de Pagamento</div>
							<div v-if="loaded" class="text-truncate text-caption text-grey">Cartões e Outros</div>
							<q-skeleton v-else type="text" width="80%" />
						</div>
					</q-btn>
				</q-item> -->

				<q-item class="q-mb-sm q-mt-sm" clickable to="/user/manage/config">
					<q-btn flat no-caps>
						<q-icon
							name="settings"
							class="icon-bordered q-mr-lg"
							color="secondary"
							size="md"
						/>
						<div class="column flex text-left item-size">
							<div v-if="loaded" class="text-truncate text-subtitle1 text-bold">Configurações Gerais</div>
							<div v-if="loaded" class="text-truncate text-caption text-grey">Privacidade, Segurança, Senha</div>
							<q-skeleton v-else type="text" width="80%" />
						</div>
					</q-btn>
				</q-item>


				<q-item class="q-mb-sm q-mt-sm" clickable to="/logout">
					<q-btn flat no-caps>
						<q-icon
							name="power_settings_new"
							class="icon-bordered q-mr-lg"
							color="secondary"
							size="md"
						/>
						<div class="column flex text-left item-size">
							<div v-if="loaded" class="text-truncate text-subtitle1 text-bold" style="cursor: pointer;">
 							 Sair
							</div>
							<div v-if="loaded" class="text-truncate text-caption text-grey">Fazer Logout</div>
							<q-skeleton v-else type="text" width="80%" />
						</div>
					</q-btn>
				</q-item>
			</q-list>
		</q-pull-to-refresh>
	</q-page>  
</template>

<script>
export default {
	name: "UserProfile",
	data() {
		return {
			loaded: false,
			profile_picture_url: null,
			full_name: "",
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
				this.$router.push('manage/info')



			},
		async get_user_data() {
			try {
				const response = await fetch(`https://fortis-api.55technology.com/v1/user/logged/`, {
					method: "GET",
					headers: { token: localStorage.getItem("access_token") },
				});

				if (!response.ok) throw new Error("Network response was not ok");

				const data = await response.json();
				this.full_name = data.user.full_name;
				this.user_email = data.user.email;
				this.user_phone = data.user.phone;
				this.user_cpf = data.user.cpf;
				this.user_birthdate = data.user.birthdate;
				this.emergency_name = data.user.emergency_name;
				this.emergency_number = data.user.emergency_number;
				this.profile_picture_url = data.user.profile_picture_url;

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

			this.profile_picture_url = null;
			this.profile_picture_url = data.profile_picture_url;
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
.item-size {
	width: 240px;
}
</style>