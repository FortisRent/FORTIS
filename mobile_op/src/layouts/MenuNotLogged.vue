<template>
	<q-layout view="lHh Lpr lFf">
		<!-- <q-header elevated class="bg-6" style="background-color: #048267;">
		<q-toolbar>
		  <q-btn class="hamburguer"
			flat
			dense
			round
			@click="toggleLeftDrawer"
			aria-label="Menu"
			icon="menu"
		  />

		  <q-toolbar-title>
			MeetMed
		  </q-toolbar-title>
		</q-toolbar>
	  </q-header> -->

		<!-- <q-drawer
			v-model="leftDrawerOpen"
			show-if-above
			:width="250"
			:breakpoint="600"
			class="bg-info"
		>
		<q-scroll-area style="height: calc(100% - 150px);">
		  <q-list padding>

			<q-item-label header>Menu</q-item-label>

			<q-item to="/login" class="menu-color" exact clickable v-ripple>
			  <q-item-section avatar> <q-icon name="login" /> </q-item-section>
			  <q-item-section> Login </q-item-section>
			</q-item>

			<q-item to="/register" class="menu-color" exact clickable v-ripple>
			  <q-item-section avatar> <q-icon name="person" /> </q-item-section>
			  <q-item-section> Cadastrar </q-item-section>
			</q-item>

			<q-item to="/support" class="menu-color" exact clickable v-ripple>
			  <q-item-section avatar> <q-icon name="info" /> </q-item-section>
			  <q-item-section> Fale conosco </q-item-section>
			</q-item>


		  </q-list>
		</q-scroll-area>
	  </q-drawer> -->
		<q-page-container>
			<keep-alive>
				<router-view />
			</keep-alive>
		</q-page-container>
	</q-layout>
</template>

<script lang="ts">
	import { ref } from 'vue';
	import { date } from 'quasar';

	export default {
		name: 'MenuClient',
		data() {
			return {
				user_name: localStorage.getItem('user_name'),
				user_email: localStorage.getItem('user_email'),
			}
		},
		computed: {
			todaysDate(){
				let timestamp = Date.now();

				return date.formatDate(timestamp, 'dddd DD, MMMM')
			}
		},
		setup () {
			const leftDrawerOpen = ref(false)

			return {
				leftDrawerOpen,
				toggleLeftDrawer () {
					leftDrawerOpen.value = !leftDrawerOpen.value
				}
			}
		}
	};
</script>
<style lang="scss">
	.header-image {
		height: 100%;
		z-index: -10;
		opacity: 0.4;
		filter: opacity(0%) greyscale(100%);
	}

	#client_header {
		background-color: inherit;
	}
</style>
