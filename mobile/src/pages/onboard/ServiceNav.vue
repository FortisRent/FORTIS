<template>
  <q-page class="q-pa-sm" v-if="cpf != null && !isLoading">
    <p class="text-h4 text-center text-primary text-bold reset-styles">
      ALUGUE FORTIS
    </p>

    <q-list class="cards-container q-mt-sm">
      <q-card class="bg-black text-white">
        <q-btn class="flex column align-center justify-center reset-styles"
          style="max-height: 150px; height: 100%; width: 100%" flat @click="navigateToCategory(1)">
          <q-img size="80px" src="../../assets/lifting-machine.png" style="max-width: 80px; max-height: 80px" />
          <span style="line-height: 1.1; text-transform: none" class="text-body3 text-center">Içamento</span>
        </q-btn>
      </q-card>
      <q-card class="bg-black text-white">
        <q-btn class="flex column align-center justify-center reset-styles"
          style="max-height: 150px; height: 100%; width: 100%" flat @click="navigateToCategory(1)">
          <q-img size="80px" src="../../assets/cargo-movement-machine.png" style="max-width: 80px; max-height: 80px" />
          <span style="line-height: 1.1; text-transform: none" class="text-body3 text-center">Movimentação de
            Carga</span>
        </q-btn>
      </q-card>
      <q-card class="bg-black text-white">
        <q-btn class="flex column align-center justify-center reset-styles"
          style="max-height: 150px; height: 100%; width: 100%" flat @click="navigateToCategory(0)">
          <q-img size="80px" src="../../assets/lifting-platforms.png" style="max-width: 80px; max-height: 80px" />
          <span style="line-height: 1.1; text-transform: none" class="text-body3 text-center">Plataformas
            Elevatórias</span>
        </q-btn>
      </q-card>
      <q-card class="bg-black text-white">
        <q-btn class="flex column align-center justify-center reset-styles"
          style="max-height: 150px; height: 100%; width: 100%" flat @click="navigateToCategory(0)">
          <q-img size="80px" src="../../assets/excavations-and-demolitions-machine.png"
            style="max-width: 80px; max-height: 80px" />
          <span style="line-height: 1.1; text-transform: none" class="text-body3 text-center">Excavações e
            Demolições</span>
        </q-btn>
      </q-card>
      <q-card class="bg-black text-white">
        <q-btn class="reset-styles" style="max-height: 150px; height: 100%; width: 100%" flat
          @click="navigateToCategory(0)">
          <q-img size="80px" src="../../assets/earthmoving-machine.png" style="max-width: 80px; max-height: 80px" />
          <span style="line-height: 1.1; text-transform: none" class="text-body3 text-center">Terraplanagem</span>
        </q-btn>
      </q-card>
      <q-card class="bg-black text-white">
        <q-btn class="reset-styles" style="max-height: 150px; height: 100%; width: 100%" flat
          @click="navigateToCategory(0)">
          <div class="flex column items-center justify-between q-pa-sm" style="height: 100%; width: 100%">
            <q-img size="80px" src="../../assets/special-transport-machines.png"
              style="max-width: 80px; max-height: 100px" />
            <span style="line-height: 1.1; text-transform: none" class="text-body3 text-center">Transporte Especiais de
              Máquinas</span>
          </div>
        </q-btn>
      </q-card>
    </q-list>

    <div class="q-gutter-y-md q-mt-sm">
      <q-list v-if="filteredProjects.length > 0">
        <div class="flex justify-between items-center">
          <strong class="text-primary text-medium text-body1">Projetos mais recentes</strong>
          <a href="" class="text-secondary">
            <span style="font-size: 0.75rem">Veja todos</span>
            <q-icon name="chevron_right" class="reset-styles" color="secondary" style="font-size: 0.75rem" />
          </a>
        </div>
        <q-card v-for="(project, index) in filteredProjects.slice(0, 3)" :key="index" class="q-mb-sm"
          style="height: 65px">
          <q-btn no-caps flat :to="`/user/project/painel/${project.uuid}`" class="full-width full-height reset-styles">
            <div style="
                display: flex;
                width: 100%;
                justify-content: space-between;
                align-items: center;
                padding: 0 0.5rem;
              ">
              <div style="display: flex; gap: 1rem; align-items: center">
                <div class="bg-black text-white flex items-center justify-center"
                  style="width: 42px; height: 42px; border-radius: 8px">
                  <q-img src="../../assets/excavations-and-demolitions-machine.png" width="24px" height="24px" />
                </div>
                <div class="flex column wrap">
                  <strong style="text-align: start; line-height: 1.2; font-size: 1rem" class="text-primary">
                    {{
                      project.project_name
                        ? project.project_name.charAt(0).toUpperCase() +
                        project.project_name.slice(1).toLowerCase()
                        : ''
                    }}
                  </strong>
                  <span class="text-primary" style="
                      opacity: 0.9;
                      text-align: start;
                      line-height: 1.2;
                      font-size: 0.75rem;
                    " v-html="`${project.street}, ${project.number_street || 0} - ${project.neighborhood
                      }`
                      ">
                  </span>
                </div>
              </div>
              <q-icon name="chevron_right" class="text-primary" />
            </div>
          </q-btn>
        </q-card>
      </q-list>

      <div v-else>
        <div class="flex justify-between items-center">
          <strong class="text-primary text-medium text-body1">Projetos mais recentes</strong>
          <a href="" class="text-secondary">
            <span style="font-size: 0.75rem">Veja todos</span>
            <q-icon name="chevron_right" class="reset-styles" color="secondary" style="font-size: 0.75rem" />
          </a>
        </div>
        <div class="q-mb-sm flex column items-center justify-center text-secondary q-mt-lg q-gutter-sm">
          <p class="reset-styles text-bold">Nenhum projeto cadastrado!</p>
          <a style="text-decoration: underline;">Solicite seu primeiro projeto</a>
        </div>
      </div>
    </div>
  </q-page>

  <q-page class="q-pa-sm flex items-center justify-center" v-else-if="!isLoading">
    <q-card class="q-pa-sm">
      <q-img src="../../assets/TrabalhadorAmarelo.png" style="margin-left: 50px" width="250px" height="250px" />
      <p class="text-secondary text-bold text-center">Quase pronto!</p>
      <p class="text-grey text-bold text-center">
        Finalize seu cadastro para receber seus primeiros serviços
      </p>
      <div class="flex justify-center q-pa-md">
        <q-btn label="Finalizar cadastro" class="bg-black" center style="min-width: 300px" no-caps
          to="/user/manage/edit/data" />
      </div>
    </q-card>
  </q-page>

  <q-page v-if="isLoading" class="q-pa-sm">
    <div class="flex items-center justify-center">
      <q-skeleton type="text" width="200px" height="50px" class="text-center" />
    </div>

    <q-list class="cards-container q-mt-sm">
      <q-card v-for="index in 6" :key="index">
        <div class="flex column items-center justify-center reset-styles"
          style="max-height: 150px; height: 100%; width: 100%; gap: 1rem;">
          <q-skeleton type="QAvatar" size="70px" class="reset-styles" />
          <q-skeleton class="text-center reset-styles" width="100px" />
        </div>
      </q-card>
    </q-list>

    <div class="q-mt-sm flex justify-between">
      <q-skeleton type="text" width="150px" />
      <q-skeleton type="text" width="100px" />
    </div>

    <q-list class="q-mt-sm">
      <q-card v-for="indexCard in 3" :key="indexCard" class="q-mb-sm" style="height: 65px">
        <div class="full-width full-height reset-styles flex">
          <div style="
                display: flex;
                width: 100%;
                gap: 0.5rem;
                align-items: center;
                padding: 0 0.5rem;
              ">
            <div style="display: flex; gap: 1rem; align-items: center">
              <div class="bg-primary text-white flex items-center justify-center"
                style="width: 42px; height: 42px; border-radius: 8px">
                <q-skeleton type="QAvatar" width="24px" height="24px" />
              </div>
              <div class="flex column wrap">
                <q-skeleton type="text" width="100px" />
                <q-skeleton type="text" width="150px" />
              </div>
            </div>
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
      machineCategoryGroups: [],
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

    async get_all_machine_category_group() {
      await fetch('https://fortis-api.55technology.com/v1/project/category/', {
        headers: { token: localStorage.getItem('access_token') },
      })
        .then((response) => response.json())
        .then((data) => {
          this.machineCategoryGroups = data.project_categories;
        })
        .catch((error) => {
          console.error('Erro ao carregar categorias:', error);
          this.$q.notify({
            color: 'red-5',
            textColor: 'white',
            icon: 'cloud_done',
            message: 'Nenhuma categoria carregada.',
          });
        })
    },
    navigateToCategory(index) {
      const uuid = this.machineCategoryGroups[index]?.uuid;
      if (uuid) {
        const routes = [
          `/user/budget/yellow/${uuid}`,
          `/user/budget/lifting/${uuid}`,
          `/user/budget/other/${uuid}`,
        ];
        this.$router.push(routes[index]);
      }
    },
  },
  async mounted() {
    try {
      await this.get_user_by_uuid_logged();
      await this.get_project_by_logged();
      await this.get_all_machine_category_group();
    } catch (error) {
      console.error('Erro ao carregar dados:', error);
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

.cards-container {
  display: grid;
  grid-template-rows: repeat(2, 130px);
  grid-template-columns: repeat(3, 115px);
  gap: 0.5rem;
}
</style>
