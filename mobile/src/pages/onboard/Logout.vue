<template>
	<div class="q-pa-md" style="max-width: 650px">
		<h5 class="q-mb-xl">Até a próxima.</h5>
	</div>
</template>

<script>
	import { useQuasar } from 'quasar';

	export default {
		name: 'LoginView',
		methods: {
			logout () {
				const $q = useQuasar();
				localStorage.clear();
				
				// $q.notify({
				// 	color: 'green-4',
				// 	textColor: 'white',
				// 	icon: 'cloud_done',
				// 	message: 'Até mais!'
				// });

				this.$router.push('/login')
			}
		},
		mounted() {
			this.logout();
		}
	};
</script>
