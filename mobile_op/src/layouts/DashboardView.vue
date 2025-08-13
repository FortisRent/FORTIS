<template>
	<HeaderDashboard />

	<q-page-container>
		<keep-alive>
			<router-view />
		</keep-alive>
	</q-page-container>
</template>
	
<script>
	import HeaderDashboard from '../components/DashboardComponents/HeaderDashboard.vue';

	export default {
		name: 'DashboardView',
		components: {
			HeaderDashboard,
		}
	};
</script>