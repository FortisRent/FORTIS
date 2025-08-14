<template>
	<q-page class="bg-white">
		<q-pull-to-refresh @refresh="refresh">
			<div class="text-bold text-h5 text-primary text-left q-mb-sm q-ml-md q-pt-xl">
			    Configurações
			</div>

			<q-separator class="bg-grey" />



			<q-list separator class="text-primary text-left q-mt-xs" clickable to="">

				<q-item class="q-mb-sm q-mt-sm">
					<q-btn flat no-caps>
						<q-icon
							name="lock"
							class="icon-bordered q-mr-lg"
							color="secondary"
							size="md"
						/>
						<div class="column flex text-left item-size">
							<div v-if="loaded" class="text-truncate text-subtitle1 text-bold">Senha</div>
							<div v-if="loaded" class="text-truncate text-caption text-grey">Trocar senha, Esqueceu senha</div>
							<q-skeleton v-else type="text" width="80%" />
						</div>
					</q-btn>
				</q-item>

				<q-item class="q-mb-sm q-mt-sm" clickable to="/policy">
					<q-btn flat no-caps>
						<q-icon
							name="security"
							class="icon-bordered q-mr-lg"
							color="secondary"
							size="md"
						/>
						<div class="column flex text-left item-size">
							<div v-if="loaded" class="text-truncate text-subtitle1 text-bold">Politicas de Privacidade</div>
							<q-skeleton v-else type="text" width="80%" />
						</div>
					</q-btn>
				</q-item>

				<q-item class="q-mb-sm q-mt-sm" clickable to="/terms">
					<q-btn flat no-caps>
						<q-icon
							name="gpp_maybe"
							class="icon-bordered q-mr-lg"
							color="secondary"
							size="md"
						/>
						<div class="column flex text-left item-size">
							<div v-if="loaded" class="text-truncate text-subtitle1 text-bold">Termos de Uso</div>
							<q-skeleton v-else type="text" width="80%" />
						</div>
					</q-btn>
				</q-item>
			</q-list>
		</q-pull-to-refresh>
        
			<q-separator class="bg-grey" />
	</q-page>  
</template>

<script>
export default {
	name: "UserConfig",
	data() {
		return {
			loaded: false,
			headers: () => [ { name: 'token', value: localStorage.getItem('access_token') } ],
		};
	},
	mounted() {
		this.get_user_data();
	},
	methods: {
		async get_user_data() {
			try {
				const response = await fetch(`https://fortis-api.55technology.com/v1/user/logged/`, {
					method: "GET",
					headers: { token: localStorage.getItem("access_token") },
				});

				if (!response.ok) throw new Error("Network response was not ok");

				const data = await response.json();
				console.table(data)
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