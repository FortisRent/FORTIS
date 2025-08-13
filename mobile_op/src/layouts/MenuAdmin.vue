<template>
	<q-layout view="lHh Lpr lFf">
		<!-- <q-header elevated>
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
		</q-header> -->

		<q-drawer
			elevated
			v-model="leftDrawerOpen"
			show-if-above
			:width="250"
			:breakpoint="600"
			class="bg-white relative-position"
		>
				<!-- <q-img
				class="absolute-top q-ml-xl"
				src="../assets/meetmedlogo.png"
				style="height: 140px; width: 140px;"
				/> -->
				<q-scroll-area style="height: calc(100% - 250px); margin-top: 150px;">
					<q-list padding>
						<q-item
							to="/admin/dash"
							class="drawer-icon"
							exact
							clickable
							v-ripple
							:class="{ 'active-icon': $route.path === '/admin/dash' }"
						>
							<q-item-section avatar>
								<q-icon name="bar_chart" class="icon-bordered" />
							</q-item-section>
						</q-item>

						<q-item
							to="/admin/calendar"
							class="drawer-icon"
							exact
							clickable
							v-ripple
							:class="{ 'active-icon': $route.path === '/admin/calendar' }"
						>
							<q-item-section avatar>
								<q-icon name="calendar_month" class="icon-bordered" />
							</q-item-section>
						</q-item>

						<q-item
							to="/admin/financial"
							class="drawer-icon"
							exact
							clickable
							v-ripple
							:class="{ 'active-icon': $route.path === '/admin/financial' }"
						>
							<q-item-section avatar>
								<q-icon name="attach_money" class="icon-bordered" />
							</q-item-section>
						</q-item>

                        <q-item
							to="/admin/patients"
							class="drawer-icon"
							exact
							clickable
							v-ripple
							:class="{ 'active-icon': $route.path === '/admin/patients' }"
						>
							<q-item-section avatar>
								<q-icon name="group" class="icon-bordered" />
							</q-item-section>
						</q-item>

						<q-item
							to="/admin/chat"
							class="drawer-icon"
							exact
							clickable
							v-ripple
							:class="{ 'active-icon': $route.path === '/admin/chat' }"
						>
							<q-item-section avatar>
								<q-icon name="question_answer" class="icon-bordered" />
							</q-item-section>
						</q-item>

                        <q-item
							to="/admin/config"
							class="drawer-icon"
							exact
							clickable
							v-ripple
							:class="{ 'active-icon': $route.path === '/admin/config' }"
						>
							<q-item-section avatar>
								<q-icon name="settings" class="icon-bordered" />
							</q-item-section>
						</q-item>

						<q-item
							to="/logout"
							class="drawer-icon"
							exact
							clickable
							v-ripple
							:class="{ 'active-icon': $route.path === '/logout' }"
						>
							<q-item-section avatar>
								<q-icon name="logout" class="icon-bordered"/>
							</q-item-section>
						</q-item>
					</q-list>
				</q-scroll-area>

				<div class="absolute-bottom bg-transparent q-mb-lg text-primary flex align-center">
					<q-avatar size="35px" class="q-mr-sm q-ml-md">
						<img src="https://cdn.quasar.dev/img/boy-avatar.png">
					</q-avatar>
					<div>
						<div class="text-weight-bold">{{ user_name }}</div>
						<div>{{ user_email }}</div>
					</div>
				</div>

			</q-drawer>
		<q-page-container>
			<keep-alive>
				<router-view />
			</keep-alive>
		</q-page-container>
	</q-layout>
</template>

<script>
	import { ref } from 'vue';
	import { date } from 'quasar';

	export default {
		name: 'MenuAdmin',
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
<style>
	.drawer-icon {
		justify-content: center;
	}
	.icon-bordered {
		border: 2px solid rgba(101, 184, 166, 0.5);
		border-radius: 15px;
		padding: 10px;
		color: black;
		font-size: 24px;
		width: 75px;
		height: 40px;
		display: flex;
		align-items: center;
		justify-content: center;
		box-sizing: border-box;
	}
	.active-icon::before,
	.active-icon::after {
	content: '';
	position: absolute;
	width: 5px;
	height: 40px;
	background-color: #65B8A6;
	border-radius: 30px;
	top: 50;
	}

	.active-icon::before {
	left: 10px;
	}

	.active-icon::after {
	right: 10px;
	}
</style>
