<template>
	<q-page class="q-pa-md bg-info">
	  <q-card class="my-card" flat style="background-color: transparent;">
		<q-img src="https://www.fabricadejogos.net/wp-content/uploads/2016/07/Young_Link_Artwork_1_Ocarina_of_Time-909x1024.png" width="150px"/>

		<q-card-section>
			<q-btn
				fab
				color="primary"
				icon="phone"
				class="absolute"
				style="top: 0; right: 12px; transform: translateY(-50%); border-radius: 200px;"
			/>

			<div class="row no-wrap items-center">
				<div class="col text-h6 ellipsis">
					{{ user.name }}
				</div>
				<div class="col-auto text-grey text-caption q-pt-md row no-wrap items-center">
					<q-icon name="place" />
					250 ft
				</div>
			</div>

			<q-rating v-model="stars" :max="5" size="32px" />
			</q-card-section>
				<q-card-section class="q-pt-none">
					<div class="text-subtitle1">
						{{ user.created_at }}
					</div>
					<div class="text-caption text-grey">
						{{ user.phone }}
					</div>
					<div class="text-caption text-grey">
						{{ user.email }}
					</div>
					<div class="text-caption text-grey">
						{{ user.cpf }}
					</div>
					<div class="text-caption text-grey">
						{{ user.rg }}
					</div>
			</q-card-section>

			<q-separator />

			<q-card-actions>
				<q-btn flat round icon="event" color="secondary"> Suporte </q-btn>
				<q-btn flat icon="timeline" color="secondary"> Histórico </q-btn>
			</q-card-actions>
		</q-card>
	</q-page>
  </template>
<script>
	export default {
		data() {
			return {
				user: {},
				stars: 4,
			};
		},
		mounted() {
			this.check_login_status();
			this.get_user_data();
		},
		methods: {
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
					// Sync user object with API data.
					this.user = data.user;
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
