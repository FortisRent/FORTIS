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
				Perfil Colaborador
			</div>
		</div>

		<q-separator class="bg-grey" />

        <div class="q-pa-md">

			<!-- <q-card class="card-style q-pa-sm q-mb-md">
                <q-item class="q-mb-sm q-mt-sm" clickable @click="ToEdit">
                    <q-icon
                        name="person"
                        class="icon-bordered q-mr-md"
                        color="secondary"
                        size="md"
                    />
                    <div class="column flex text-left">
                        <div v-if="loaded" class="text-truncate text-subtitle1 text-bold text-primary">Preciso de Equipamento</div>
                        <div v-if="loaded" class="text-truncate text-caption text-grey">Solicita e acompanha projetos</div>
                        <q-skeleton v-else type="text" width="80%" />
                    </div>
            </q-item>
            </q-card> -->

            <!-- <q-card class="card-style q-pa-sm q-mb-md">
                <q-item class="q-mb-sm q-mt-sm" clickable @click="ToEdit">
                    <q-icon
                        name="person"
                        class="icon-bordered q-mr-md"
                        color="secondary"
                        size="md"
                    />
                    <div class="column flex text-left">
                        <div v-if="loaded" class="text-truncate text-subtitle1 text-bold text-primary">Alugo Equipamento</div>
                        <div v-if="loaded" class="text-truncate text-caption text-grey">Oferece equipamentos para locação</div>
                        <q-skeleton v-else type="text" width="80%" />
                    </div>
            </q-item>
            </q-card> -->

			<q-card class="card-style q-pa-sm q-mb-md">
				<q-item
					class="q-mb-sm q-mt-sm"
					:clickable="employee?.role_name === 'Operador'"
					@click="redirectToManageEmployee"
				>
					<q-icon
						name="person"
						class="icon-bordered q-mr-md"
						color="secondary"
						size="md"
						/>
					<div v-if="loaded" class="column flex text-left">
						<div class="text-truncate text-subtitle1 text-bold text-primary">Sou Operador</div>
						<div class="text-truncate text-caption text-grey">Presta serviços para projetos</div>
					</div>
					<q-skeleton v-else type="text" width="100%" class="column flex text-left" />
				</q-item>
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
			employee: null,
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
		async get_employee_by_logged() {
            try {
                const response = await fetch(`https://fortis-api.55technology.com/v1/employee/logged/`, {
                    method: "GET",
                    headers: { token: localStorage.getItem("access_token") },
                });

                if (!response.ok) throw new Error("Network response was not ok");

                const data = await response.json();
                this.employee = data.employees[0]; // Pegando o primeiro item da lista

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
        redirectToManageEmployee() {
            if (this.employee?.role_name === "Operador") {
                this.$router.push(`/user/manage/employee/${this.employee.uuid}`);
            }
        }

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