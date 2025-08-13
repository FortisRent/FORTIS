<template>
	<q-layout view="lHh Lpr lFf">
		<q-header id="client_header" class="bg-info" elevated>
			<q-toolbar>
				<q-btn
					flat
					dense
					round
					icon="menu"
					aria-label="Menu"
					@click="toggleLeftDrawer"
				/>
				<div class="text-subtitle1 q-px-lg q-pt-l">{{todaysDate}}</div>
			</q-toolbar>
			<!-- <q-img src="../assets/dash2.jpg" class="header-image absolute-top"/> -->
		</q-header>

		<q-drawer
			v-model="leftDrawerOpen"
			show-if-above
			:width="250"
			:breakpoint="550"
			class="bg-info"
		>
        <q-scroll-area style="height: calc(100% - 150px); margin-top: 150px;">
          <q-list padding>

			<q-item to="/client" class="menu-color" exact clickable v-ripple>
              <q-item-section avatar> <q-icon name="home" /> </q-item-section>
              <q-item-section> Home </q-item-section>
            </q-item>

            <q-item to="/client/event" class="menu-color" exact clickable v-ripple>
				<q-item-section avatar> <q-icon name="schedule" /> </q-item-section>
				<q-item-section> Agendamentos </q-item-section>
			</q-item>

            <q-item to="/client/pill" class="menu-color" exact clickable v-ripple>
				<q-item-section avatar> <q-icon name="vaccines" /> </q-item-section>
				<q-item-section> Medicações </q-item-section>
			</q-item>

            <q-item to="/client/timeline" class="menu-color" exact clickable v-ripple>
				<q-item-section avatar> <q-icon name="timeline" /> </q-item-section>
				<q-item-section> Histórico </q-item-section>
			</q-item>

            <q-item to="/client/wallet" class="menu-color" exact clickable v-ripple>
				<q-item-section avatar> <q-icon name="payments" /> </q-item-section>
				<q-item-section> Financeiro </q-item-section>
			</q-item>

            <q-separator color="white"/>

            <q-span class="q-pl-lg q-my-lg">
                Área do médico e adm
            </q-span>

            <q-item to="/client/clinic" class="menu-color" exact clickable v-ripple>
				<q-item-section avatar> <q-icon name="store" /> </q-item-section>
				<q-item-section> Minhas clínicas </q-item-section>
			</q-item>
            
            <q-item to="/client/drugstore" class="menu-color" exact clickable v-ripple>
				<q-item-section avatar> <q-icon name="vaccines" /> </q-item-section>
				<q-item-section> Minhas farmácias </q-item-section>
			</q-item>

            <q-item to="/client/invite" class="menu-color" exact clickable v-ripple>
				<q-item-section avatar> <q-icon name="schedule" /> </q-item-section>
				<q-item-section> Agendas </q-item-section>
			</q-item>

            <q-separator color="white"/>

            <q-item to="/client/profile" class="menu-color" exact clickable v-ripple>
				<q-item-section avatar> <q-icon name="person" /> </q-item-section>
				<q-item-section> Perfil </q-item-section>
			</q-item>

			<q-item to="/logout" class="menu-color" exact clickable v-ripple>
				<q-item-section avatar> <q-icon name="logout" /> </q-item-section>
				<q-item-section> Logout </q-item-section>
			</q-item>
          </q-list>
        </q-scroll-area>

        <q-img class="absolute-top" src="../assets/omni_logo.png" style="height: 150px">
          <div class="absolute-bottom bg-transparent">
            <q-avatar size="56px" class="q-mb-sm">
              <img src="https://cdn.quasar.dev/img/boy-avatar.png">
            </q-avatar>
            <div class="text-weight-bold">{{ user_name }}</div>
            <div>{{ user_email }}</div>
          </div>
        </q-img>
      </q-drawer>
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
