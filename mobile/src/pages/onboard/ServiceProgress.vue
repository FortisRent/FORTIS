<template>
  <q-page
    class="q-pa-sm flex column"
    style="gap: 1rem"
    v-if="cpf != null && !isLoading"
  >
    <p class="text-h4 text-center text-primary text-bold reset-styles">
      MEUS PROJETOS
    </p>

    <q-card>
      <div class="flex justify-center">
        <q-input
          v-model="searchTerm"
          class="q-pa-sm"
          filled
          label="Pesquisar"
          style="min-width: 350px"
          color="primary"
        >
          <template v-slot:prepend>
            <q-icon name="search" color="black" size="sm" class="q-ml-sm" />
          </template>
        </q-input>
      </div>

      <div class="flex justify-between">
        <q-btn
          label="Criar projeto"
          class="text-white text-bold q-ma-sm"
          clickable
          @click="ToCreate"
          color="black"
          style="flex: 1"
        />
        <q-btn
          name="movies"
          flat
          no-caps
          label="Filtrar"
          color="black"
          icon="tune"
          size="13px"
        >
          <q-menu>
            <q-list dense>
              <q-item
                class="text-green text-bold"
                clickable
                v-ripple
                @click="toggleFilter('ACEITO')"
              >
                <q-item-section>
                  <q-checkbox v-model="filterStatus.ACEITO" label="Aceito" />
                </q-item-section>
              </q-item>
              <q-item
                class="text-orange text-bold"
                clickable
                v-ripple
                @click="toggleFilter('ATENDIMENTO')"
              >
                <q-item-section>
                  <q-checkbox
                    v-model="filterStatus.ATENDIMENTO"
                    label="Atendimento"
                  />
                </q-item-section>
              </q-item>
              <q-item
                class="text-red text-bold"
                clickable
                v-ripple
                @click="toggleFilter('INCOMPLETO')"
              >
                <q-item-section>
                  <q-checkbox
                    v-model="filterStatus.INCOMPLETO"
                    label="Incompleto"
                  />
                </q-item-section>
              </q-item>
            </q-list>
          </q-menu>
        </q-btn>
      </div>
    </q-card>

    <q-list v-if="filteredProjects.length > 0">
      <q-card
        style="height: 80px; overflow: hidden"
        v-for="(project, index) in filteredProjects"
        :key="index"
        class="q-mt-sm"
      >
        <q-btn
          :to="`/user/project/painel/${project.uuid}`"
          class="reset-styles text-primary"
          style="height: 100%; width: 100%"
          flat
        >
          <div
            style="
              height: 100%;
              width: 100%;
              display: flex;
              align-items: center;
              justify-content: space-between;
              padding: 0.5rem;
            "
          >
            <div style="display: flex; gap: 0.5rem">
              <q-icon name="home" size="42px" class="reset-styles" />

              <div
                style="
                  display: flex;
                  flex-direction: column;
                  max-width: 250px;
                  white-space: nowrap;
                  text-align: left;
                "
              >
                <strong style="overflow: hidden; text-overflow: ellipsis">
                  {{ project.project_name }}
                </strong>
                <span style="overflow: hidden; text-overflow: ellipsis">{{
                  `${project.street}, ${project.number_street} - ${project.neighborhood}`
                }}</span>
              </div>
            </div>
            <q-icon name="chevron_right" size="24px" class="reset-styles" />
          </div>
        </q-btn>
      </q-card>
    </q-list>

    <span
      v-else
      class="flex items-center justify-center text-primary text-subtitle1"
      style="flex: 1"
      >Nenhum Projeto cadastrado</span
    >
  </q-page>

  <q-page
    class="q-pa-sm flex items-center justify-center"
    v-else-if="!isLoading"
  >
    <q-card class="q-pa-sm">
      <q-img
        src="../../assets/TrabalhadorAmarelo.png"
        style="margin-left: 50px"
        width="250px"
        height="250px"
      />
      <p class="text-secondary text-bold text-center">Quase pronto!</p>
      <p class="text-grey text-bold text-center">
        Finalize seu cadastro para receber seus primeiros serviços
      </p>
      <div class="flex justify-center q-pa-md">
        <q-btn
          label="Finalizar cadastro"
          class="bg-black"
          center
          style="min-width: 300px"
          no-caps
          to="/user/manage/edit/data"
        />
      </div>
    </q-card>
  </q-page>

  <q-page v-if="isLoading" class="flex column items-center q-pa-sm">
    <q-skeleton type="text" width="200px" height="80px" />
    <q-card class="flex column items-center q-pa-sm" style="gap: 1rem; width: 100%;">
      <q-skeleton type="input" width="340px" height="40px" />

      <div class="flex" style="gap: 0.5rem;">
        <q-skeleton type="QBtn" width="165px" height="30px" />
        <q-skeleton type="QBtn" width="165px" height="30px" />
      </div>
    </q-card>

    <q-list class="flex column q-mt-md" style="gap: 0.5rem; width: 100%;">
      <q-card v-for="index in 4" :key="index" style="height: 80px; width: 100%;" class="flex items-center">
        <div class="flex items-center q-pa-sm" style="gap: 1rem;">
          <q-skeleton type="circle" />
          <div>
            <q-skeleton type="text" width="200px" height="20px" />
            <q-skeleton type="text" width="250px" height="20px" />
          </div>
        </div>
      </q-card>
    </q-list>
  </q-page>
</template>

<script>
export default {
  data() {
    return {
      isLoading: true,
      cpf: null,
      projects: [],
      searchTerm: '',
      filterStatus: {
        ACEITO: false,
        ATENDIMENTO: false,
        INCOMPLETO: false,
      },
    };
  },
  computed: {
    filteredProjects() {
      return this.projects.filter((project) => {
        const matchesSearch = (project.project_name || '')
          .toLowerCase()
          .includes(this.searchTerm.toLowerCase());

        const statusName =
          project.status_name === null ? 'INCOMPLETO' : project.status_name;

        const matchesStatus =
          (!this.filterStatus.ACEITO &&
            !this.filterStatus.ATENDIMENTO &&
            !this.filterStatus.INCOMPLETO) ||
          (this.filterStatus.ACEITO && statusName === 'ACEITO') ||
          (this.filterStatus.ATENDIMENTO && statusName === 'ATENDIMENTO') ||
          (this.filterStatus.INCOMPLETO && statusName === 'INCOMPLETO');

        return matchesSearch && matchesStatus;
      });
    },
  },
  methods: {
    ToCreate() {
      this.$router.push('/user/budget/');
    },
    async get_user_by_uuid_logged() {
      await fetch('https://fortis-api.55technology.com/v1/user/logged/', {
        headers: { token: localStorage.getItem('access_token') },
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          return response.json();
        })
        .then((data) => {
          console.table(data);
          this.cpf = data.user.cpf;
        })
        .catch((error) => {
          console.error('Error fetching data:', error);
        });
    },

    async get_project_by_logged() {
      await fetch('https://fortis-api.55technology.com/v1/project/logged/', {
        headers: { token: localStorage.getItem('access_token') },
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          return response.json();
        })
        .then((data) => {
          console.table(data.project);
          this.projects = data.project.map((project) => ({
            icon: 'local_shipping',
            project_name: project.project_name,
            client_name: project.client_name,
            expected_date: project.expected_date,
            zip_code: project.zip_code,
            street: project.street,
            state_name: project.state_name,
            number_street: project.number_street,
            neighborhood: project.neighborhood,
            city_name: project.city_name,
            identifier: project.identifier,
            uuid: project.project_uuid,
            status_name: project.status_name,
          }));
        })
        .catch((error) => {
          console.error('Error fetching data:', error);
          this.$q.notify({
            color: 'red-5',
            textColor: 'white',
            icon: 'cloud_done',
            message: 'Nenhum projeto cadastrado.',
          });
        })
    },
    toggleFilter(status) {
      this.filterStatus[status] = !this.filterStatus[status];
    },
  },
  async mounted() {
    try {
      this.isLoading = true;
      await this.get_user_by_uuid_logged();
      await this.get_project_by_logged();
    } catch (error) {
      console.error('Error during mounted:', error);
    } finally {
      this.isLoading = false;
    }
  },
};
</script>

<style scoped>
.reset-styles {
  margin: 0;
  padding: 0;
}

* {
  text-decoration: none;
}
</style>
