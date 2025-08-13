<template>
  <q-page class="bg-white text-center">
    <div class="row items-center justify-between q-mb-sm">
      <div class="col text-left q-ml-md">
        <h5 class="q-mb-none text-primary text-truncate">{{ user_name }}</h5>
        <p class="text-grey-7 text-caption text-truncate">{{ user_email }}</p>
      </div>
      <div class="q-mr-md">
        <q-avatar v-if="loaded" size="80px" class="q-mr-sm">
            <q-img
              v-if="selfie_url"
              :src="`http://localhost:5510/${selfie_url}`"
              width="80px"
              height="80px"
              fit="cover"
              style="border-radius: 100px;"
            />
          </q-avatar>
        </div>
    </div>
     <q-separator class="bg-info"/>

    <div class="text-primary q-mb-xl q-ma-md flex no-wrap justify-between">
      <q-btn stack no-caps icon="content_paste" label="Histórico de Consulta" to="/timeline/list"
        style="
          border-radius: 15px;
          background-color: #D9D9D9;
          font-size: 0.75rem;
          width: 100px;
          height: 100px;"
      />
      <q-btn stack no-caps icon="help" label="Ajuda" to="/help"
        style="
          border-radius: 15px;
          background-color: #D9D9D9;
          font-size: 0.75rem;
          width: 100px;
          height: 100px;"
      />
      <q-btn stack no-caps icon="monitor_heart" label="Histórico de Saúde" to="/user/health"
        style="
          border-radius: 15px;
          background-color: #D9D9D9;
          font-size: 0.75rem;
          width: 100px;
          height: 100px;"
      />
    </div>

    <q-list separator class="text-primary text-left">
      <q-item v-for="(item, index) in menuItems" :key="index" clickable v-ripple @click="navigate(item.route)">
        <q-item-section avatar>
          <q-icon :name="item.icon" class="text-primary" />
        </q-item-section>
        <q-item-section>{{ item.label }}</q-item-section>
      </q-item>
    </q-list>
  </q-page>
</template>

<script>
export default {
  name: 'UserProfile',
  data() {
    return {
      user_name: localStorage.getItem('user_name')    || 'Nome do Usuário',
      user_email: localStorage.getItem('user_email')  || 'email@exemplo.com',
      selfie_url: null,
      loaded: false,
      menuItems: [
        { label: 'Gerenciamento de conta', icon: 'settings', route: '/user/manage' },
        { label: 'Pagamentos', icon: 'credit_card', route: '/user/payments' },
        { label: 'Favoritos', icon: 'favorite', route: '/user/favorite' },
        // { label: 'Receita', icon: 'calendar_today', route: '/user/prescription' },
        { label: 'Saúde', icon: 'medical_services', route: '/user/health' },

        // Admin e médico
        { label: 'Minhas Clínicas', icon: 'domain_add', route: '/clinic' },
        { label: 'Minhas Farmácias', icon: 'home_work', route: '/drugstore' },
        { label: 'Minhas Agendas', icon: 'pending_actions', route: '/invite' },

        // Suporte
        { label: 'Termos de Uso', icon: 'privacy_tip', route: '/terms' },
        { label: 'Termos de Privacidade', icon: 'local_police', route: '/policy' },
        
        { label: 'Logout', icon: 'logout', route: '/logout' },
      ],
    };
  },
  methods: {
    navigate(route) {
      if (route) {
        this.$router.push(route);
      }
    },
    async get_user_data() {
			try {
				const response = await fetch(`http://localhost:5510/v1/user/logged/`, {
					method: "GET",
					headers: { token: localStorage.getItem("access_token") },
				});

				if (!response.ok) throw new Error("Network response was not ok");

				const data = await response.json();

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
    
  },
  mounted() {
		this.get_user_data();
	},
};
</script>

<style scoped>
.text-truncate {
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		display: block;
		max-width: 90%;
	}
</style>
