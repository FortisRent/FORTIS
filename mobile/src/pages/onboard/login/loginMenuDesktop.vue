<template>
  <q-page>
    <div class="q-pa-md flex column items-center">
      <q-btn
        flat
        round
        icon="arrow_back"
        class="q-mr-sm text-primary"
        color="black"
        style="position: absolute; left: 10px;"
        @click="$router.go(-1)"
      />

      <h5 class="text-black">Qual empresa deseja acessar?</h5>

      <div class="q-pa-md" style="min-width: 300px">
        <q-list v-if="!loading" bordered separator>
          <q-item
            v-for="company in companies"
            :key="company.uuid"
            clickable
            v-ripple
            @click="goToCompany(company.uuid)"
          >
            <q-item-section avatar>
              <q-icon name="business" color="primary" size="md" />
            </q-item-section>
            <q-item-section class="text-black text-bold">
              <div>{{ company.company_name }}</div>
              {{ company.cnpj }}
             <div>{{company.user_name}}</div>
            </q-item-section>
            <q-item-section side>
              <q-icon name="chevron_right" size="30px" color="black" />
            </q-item-section>
          </q-item>

          <q-separator />
        </q-list>

        <!-- Skeleton de Carregamento -->
        <q-list v-else bordered separator>
          <q-item v-for="i in 3" :key="i">
            <q-item-section avatar>
              <q-skeleton type="QAvatar" />
            </q-item-section>
            <q-item-section>
              <q-skeleton type="text" width="80%" />
              <q-skeleton type="text" width="60%" />
            </q-item-section>
            <q-item-section side>
              <q-skeleton type="QIcon" />
            </q-item-section>
          </q-item>
        </q-list>

      </div>
    </div>
  </q-page>
</template>

<script>
export default {
  name: "LoginMenuCompany",
  data() {
    return {
      companies: [],
      loading: true,
    };
  },
  mounted() {
    this.get_user();
  },
  methods: {
    async get_user() {
      try {
        const response = await fetch("http://localhost:5510/v1/user/logged/profile/", {
          method: "GET",
          headers: {
            token: localStorage.getItem("access_token"),
          },
        });
        if (!response.ok) throw new Error("Network response was not ok");

        const data = await response.json();
        console.log("API Response:", data);

        if (data && data.company) {
          this.companies = data.company;
        } else {
          console.error("Nenhuma empresa encontrada.");
        }
      } catch (error) {
        console.error("Erro ao buscar dados:", error);
        this.$q.notify({
          color: "grey",
          textColor: "white",
          icon: "cloud_done",
          message: "Ops! Falha ao carregar dados.",
        });
      } finally {
        this.loading = false;
      }
    },
    goToCompany(uuid) {
      localStorage.setItem("company_uuid", uuid);
      this.$router.push(`/dashboard/${uuid}`);
    },
  },
};
</script>
