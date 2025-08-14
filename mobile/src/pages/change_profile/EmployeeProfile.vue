<template>
	<q-page class="bg-white">
		<div class="row justify-around  items-center q-pa-md q-mt-lg">
			<q-btn flat round icon="chevron_left" class="text-primary" color="secondary" size="18px"
				style="position: absolute; left: 10px;" @click="$router.go(-1)" />
			<div class="text-h6 text-primary text-bold q-ml-md">
				Perfil Funcionário
			</div>
		</div>

		<q-separator class="bg-grey" />

		<div class="q-pa-md">
			<q-card class="card-style q-pa-sm q-mb-md">
				<div class="text-center text-caption">
					<q-avatar size="90px" class="q-mb-lg q-mt-md">
						<q-img v-if="profile_picture_url" :src="`https://fortis-api.55technology.com/${profile_picture_url}`" width="90px"
							height="90px" fit="cover" style="border-radius: 100px;" />
						<q-icon v-if="loaded && profile_picture_url" name="person" size="55px" color="secondary" class="q-mr-sm" />
						<q-skeleton v-if="!loaded" type="QAvatar" size="90px" class="q-mb-sm" />
					</q-avatar>
				</div>
			</q-card>
			<q-card class="card-style q-pa-sm q-mb-md">
				<div class="row flex justify-between q-mb-none">
					<div class="row items-center no-wrap">
						<q-icon name="front_loader" size="22px" color="secondary" class="q-mr-sm" />
						<p v-if="loaded" class="text-bold text-primary text-subtitle1 no-margin">
							Serviços em andamento
						</p>
						<q-skeleton v-else type="text" width="200px" />
					</div>
					<q-btn v-if="loaded" icon="chevron_right" clickcable rounded size="10px" flat color="black"
						:to='`/user/manage/employee/service/${uuid}`' />
				</div>
			</q-card>
			<q-card class="card-style q-pa-sm q-mb-md">
				<div class="row flex justify-between q-mb-none">
					<div class="row items-center no-wrap">
						<q-icon name="person" size="22px" color="secondary" class="q-mr-sm" />
						<p v-if="loaded" class="text-bold text-primary text-subtitle1 no-margin">
							Dados Pessoais
						</p>
						<q-skeleton v-else type="text" width="200px" />
					</div>
				</div>
				<div v-if="loaded" class="text-left q-pa-sm text-caption">
					<p class="text-primary no-margin text-truncate">Nome: {{ full_name }} </p>
					<p class="text-primary no-margin text-truncate">Data de Nascimento: {{ birthdate }} </p>
					<p class="text-primary no-margin text-truncate">CPF: {{ identity_document_number }} </p>
					<p class="text-primary no-margin text-truncate">E-mail: {{ email }} </p>
				</div>
				<q-skeleton v-else type="text" width="200px" />
			</q-card>
			<q-card class="card-style q-pa-sm q-mb-md">
				<div class="row flex justify-between q-mb-none">
					<div class="row items-center no-wrap">
						<q-icon name="badge" size="22px" color="secondary" class="q-mr-sm" />
						<p v-if="loaded" class="text-bold text-primary text-subtitle1 no-margin">
							Documentos
						</p>
						<q-skeleton v-else type="text" width="200px" />
					</div>
					<q-btn v-if="loaded" icon="edit" clickcable rounded size="10px" flat color="black"
						:to='`/user/manage/employee/documents/${uuid}`' />
				</div>
				<div v-if="loaded" class="text-left q-pa-sm text-caption">
					<p class="text-primary no-margin text-truncate">Função: {{ role_name }} </p>
					<p class="text-primary no-margin text-truncate">CNH: {{ full_name }} </p>
					<p class="text-primary no-margin text-truncate">Carteira de Trabalho: {{ ctps_number }} </p>
					<p class="text-primary no-margin text-truncate">R$: {{ hourly_price }} </p>
				</div>
				<q-skeleton v-else type="text" width="200px" />
			</q-card>
			<q-card class="card-style q-pa-sm q-mb-md">
				<div class="row flex justify-between q-mb-none">
					<div class="row items-center no-wrap">
						<q-icon name="picture_as_pdf" size="22px" color="secondary" class="q-mr-sm" />
						<p v-if="loaded" class="text-bold text-primary text-subtitle1 no-margin">
							Certificados
						</p>
						<q-skeleton v-else type="text" width="200px" />
					</div>
					<q-btn v-if="loaded" icon="chevron_right" clickcable rounded size="10px" flat color="black"
						:to='`/user/manage/employee/certificate/${uuid}`' />
				</div>
			</q-card>
			<q-card class="card-style q-pa-sm q-mb-md">
				<div class="row flex justify-between q-mb-none">
					<div class="row items-center no-wrap">
						<q-icon name="work" size="22px" color="secondary" class="q-mr-sm" />
						<p v-if="loaded" class="text-bold text-primary text-subtitle1 no-margin">
							Dados da Empresa
						</p>
						<q-skeleton v-else type="text" width="200px" />
					</div>
				</div>
				<div v-if="loaded" class="text-left q-pa-sm text-caption">
					<p class="text-primary no-margin text-truncate">Nome: {{ full_name }} </p>
					<p class="text-primary no-margin text-truncate">Telefone: {{ birthdate }} </p>
					<p class="text-primary no-margin text-truncate">Cargo: {{ email }} </p>
				</div>
				<q-skeleton v-else type="text" width="200px" />
			</q-card>

			<q-card class="card-style q-pa-sm q-mb-md">
				<div class="row flex justify-between">
					<div class="row items-center no-wrap">
						<q-icon name="location_on" size="22px" color="secondary" class="q-mr-sm" />
						<p v-if="loaded" class="text-bold text-primary text-subtitle1 no-margin">
							Endereço Principal
						</p>
						<q-skeleton v-else type="text" width="200px" />
					</div>
				</div>
				<div v-if="loaded" class="text-left q-pa-sm text-caption">
					<p class="text-primary no-margin text-truncate">CEP: {{ zip_code }}</p>
					<p class="text-primary no-margin text-truncate">Rua: {{ street }} </p>
					<p class="text-primary no-margin text-truncate">Número:{{ number_street }}</p>
					<p class="text-primary no-margin text-truncate">Complemento:{{ complement }}</p>
					<p class="text-primary no-margin text-truncate">Logradouro:{{ neighborhood }}</p>
					<p class="text-primary no-margin text-truncate">Cidade:{{ city_name }}</p>
					<p class="text-primary no-margin text-truncate">UF: {{ state_name }}</p>
				</div>
				<q-skeleton v-else type="text" width="200px" />
			</q-card>

		</div>
	</q-page>
</template>

<script>
export default {
	name: "EmployeeProfile",
	data() {
		return {
			loaded: false,
			profile_picture_url: '',
			full_name: "",
			birthdate: "",
			identity_document_number: "",
			user_email: "",
			zip_code: '',
			street: '',
			number_street: '',
			complement: '',
			neighborhood: '',
			city_name: '',
			state_name: '',
			uuid: '',
			role_name: '',
			ctps_number: '',
			hourly_price: '',

			headers: () => [{ name: 'token', value: localStorage.getItem('access_token') }],
		};
	},
	mounted() {
		this.get_user_data();
		this.get_employee_by_logged();
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
				this.full_name = data.user.full_name;
				this.birthdate = data.user.birthdate;
				this.email = data.user.email;
				this.identity_document_number = data.user.identity_document_number;
				this.complement = data.user.complement;
				this.neighborhood = data.user.neighborhood;
				this.city_name = data.user.city_name;
				this.state_name = data.user.state_name;
				this.number_street = data.user.number_street;
				this.zip_code = data.user.zip_code;
				this.street = data.user.street;
				this.profile_picture_url = data.user.profile_picture_url;

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

		async refresh(done) {
			await this.get_user_data();
			done();
		},

		on_rejected() {
			this.$q.notify({
				color: 'red-4',
				textColor: 'white',
				icon: 'cloud_done',
				message: "Falha ao enviar foto."
			});
		},
		on_upload(info) {
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
				this.role_name = data.employees[0].role_name;
				this.hourly_price = data.employees[0].hourly_price;
				this.ctps_number = data.employees[0].ctps_number;
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
	max-width: 100%;
}

.card-style {
	border-radius: 10px;
	min-width: 300px;
}
</style>