<template>
	<div class="q-pa-md" style="max-width: 650px;">
	  <q-list class="scroll" v-if="!loading">
		<q-pull-to-refresh
		  @refresh="refreshList"
		  color="secondary-2"
		  bg-color="black"
		  icon="autorenew"
		  scroll-target="body"
		>
		  <q-item-label header>
			Users
		  </q-item-label>
  
		  <q-item v-for="user in users" :key="user.id" class="q-mb-sm item" clickable v-ripple>
			<q-item-section avatar>
			  <q-avatar>
				<img :src="`https://cdn.quasar.dev/img/avatar1.jpg`">
			  </q-avatar>
			</q-item-section>
  
			<q-item-section>
			  <q-item-label>{{ user.name }}</q-item-label>
			  <q-item-label>{{ user.birthdate }}</q-item-label>
			  <q-item-label>{{ user.email }}</q-item-label>
			  <q-item-label>{{ user.phone }}</q-item-label>
			</q-item-section>
  
			<q-item-section side>
			  <q-btn
				@click.stop="deleteTask(user.id)"
				flat
				round
				dense
				color="white"
				icon="delete"
			  />
			</q-item-section>
  
			<q-item-section side>
			  <q-btn
				:to="`/user/profile/${user.id}`"
				flat
				round
				dense
				color="white"
				icon="edit"
			  />
			</q-item-section>
  
			<q-item-section side>
			  <q-btn
				:to="`/user/profile/${user.id}`"
				flat
				round
				dense
				color="white"
				icon="account_circle"
			  />
			</q-item-section>
		  </q-item>
		</q-pull-to-refresh>
	  </q-list>
  
	  <div v-else>
		<LoaderComponent />
	  </div>
  
	  <q-page-sticky position="bottom-right" :offset="[18, 18]">
		<q-btn fab icon="add" color="primary" to="/user/create" />
	  </q-page-sticky>
	</div>
  </template>
  
  <script>
  import { useQuasar } from 'quasar';
  import { ref, onMounted } from 'vue';
  import LoaderComponent from '../../../components/LoaderComponent.vue';
  
  export default {
	name: 'UserList',
	components: {
	  LoaderComponent,
	},
	methods: {
	  deleteTask(index) {
		this.$q.dialog({
		  title: 'Confirma?',
		  message: 'Você realmente quer deletar?',
		  cancel: true,
		  persistent: true,
		  dark: true
		}).onOk(() => {
		  fetch(`https://api.omnifitness.com.br/v1/user/${index}`, {
			method: 'DELETE',
		  })
		  .then(response => response.json())
		  .then(data => {
			this.$q.notify(data);
			this.refreshList(() => {
			  console.log('Initial data fetched');
			});
		  })
		  .catch(error => {
			this.$q.notify({
			  color: 'red-5',
			  textColor: 'white',
			  icon: 'cloud_done',
			  message: 'Ops! Falha ao carregar dados.'
			});
			console.log(error);
		  });
		}).onCancel(() => {
		  // console.log('>>>> Cancel')
		}).onDismiss(() => {
		  // console.log('I am triggered on both OK and Cancel')
		})
	  },
	},
	setup() {
	  const $q = useQuasar();
	  const users = ref([]);
	  const loading = ref(true);
  
	  const refreshList = (done) => {
		loading.value = true;
		fetch('https://api.omnifitness.com.br/v1/user')
		  .then(response => response.json())
		  .then(data => {
			users.value.splice(0, users.value.length, ...data.users);
			loading.value = false;
			done(); 
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
			done(); // Call done in case of an error
		  });
	  };
  
	  onMounted(() => {
		// Fetch initial data
		refreshList(() => {
		  console.log('Initial data fetched');
		});
	  });
  
	  return {
		users,
		loading,
		refreshList
	  };
	}
  }
  </script>
  
  <style>
	.item {
	  background-image: linear-gradient(to right, #292929, #464646);
	}
  
	.empty-space {
	  margin-bottom: 15px;
	}
  </style>
  