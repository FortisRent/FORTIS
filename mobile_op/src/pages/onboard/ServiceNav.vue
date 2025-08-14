<template>
  <q-page class="q-pa-sm">
    <q-img
      class="q-mb-sm"
      src="../../assets/logo-fortis.svg"
      width="150px"
      height="50px"
      fit="none"
      style="margin-left: 100px"
    />
    <div class="q-gutter-y-md">
      <!-- <q-btn class="flex justify-center" icon="person" color="black" label="Trocar o Perfil" to="/user/change" /> -->
      <q-card>
        <div class="flex justify-center">
          <q-input
            v-model="searchTerm"
            class="q-pa-sm"
            filled
            label="Pesquisar"
            style="min-width: 350px"
            color="secondary"
          >
            <template v-slot:prepend>
              <q-icon name="search" color="black" size="sm" class="q-ml-sm" />
            </template>
          </q-input>

          <!-- <q-btn
            label="Solicitar um Orçamento"
            class="text-white text-bold q-ma-sm"
            clickable
            @click="ToCreate"
            color="black"
            style="min-width: 340px"
          /> -->
        </div>

        <div class="row justify-end q-mr-md">
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
                  class="text-secondary text-bold"
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

      <div>
        <q-list v-if="isLoading">
          <q-card
            v-for="n in 3"
            :key="n"
            class="card-style q-pa-sm q-mb-md"
            style="height: 330px"
            bordered
          >
            <div
              class="flex column text-left justify-between full-height q-gutter-md q-pa-sm"
            >
              <div class="flex justify-between items-center">
                <q-skeleton type="text" width="150px"/>
                <q-skeleton type="text" width="150px"/>
              </div>
              <div class="text-center">
                <q-skeleton type="text" height="40px"/>
              </div>
              <div class="text-left">
                <q-skeleton type="text" />
                <q-skeleton type="text" />
                <q-skeleton type="text" />
                <q-skeleton type="text" />
              </div>
              <q-skeleton type="text" width="150px" />
            </div>
          </q-card>
        </q-list>

        <q-list >
          <q-card
            v-for="(budget_machine, index) in budget_machines"
            :key="index"
            class="card-style q-pa-sm q-mb-md"
            style="height: 330px; border-color: secondary"
            bordered
          >
            <q-btn
              no-caps
              flat
              :to="`/user/project/details/${budget_machine.uuid}/${budget_machine.budget_machine_operator_uuid}`"
              class="full-width full-height"
            >
              <div
                class="flex column text-left justify-between full-height q-gutter-md"
              >
              
                <div class="text-center">
                  <p
                    v-if="budget_machine.project_name"
                    class="text-bold text-primary text-h6 no-margin"
                  >
                    {{ budget_machine.project_name }}
                  </p>
                  <p v-else class="text-bold text-grey text-h6 no-margin">
                    Projeto sem nome
                  </p>
                  <q-separator color="secondary" />
                </div>
                <p class="text-center text-primary text-h6">CLIQUE AQUI PARA INICIAR SUA JORNADA</p>
                <div class="text-left">
                  <p class="text-primary">
                    <strong>Data:</strong> {{ budget_machine.expected_date }}
                  </p>
                  <p class="text-primary">
                    <strong>Cliente:</strong> {{ budget_machine.client_name }}
                  </p>
                  <p class="text-primary">
                    <strong>Endereço:</strong> {{ budget_machine.street }},
                    {{ budget_machine.number_street }}. {{ budget_machine.neighborhood }},
                    {{ budget_machine.city_name }}, {{ budget_machine.state_name }}.
                  </p>
                  <p class="text-primary">
                    <strong>CEP: </strong>{{ budget_machine.zip_code }}
                  </p>
                </div>
              </div>
            </q-btn>
          </q-card>
        </q-list>

        <!-- <q-list v-else>
          <q-card class="card-style q-pa-sm q-mb-md">
            <p class="text-center">Nenhum projeto cadastrado</p>
          </q-card>
        </q-list> -->
      </div>

      <q-card class="q-pa-sm" v-if="this.cpf == null && !isLoading">
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
    </div>
  </q-page>
</template>

<script>
export default {
  data() {
    return {
      cpf: null,
      budget_machines: [],
      isLoading: true,
      searchTerm: '',
      filterStatus: {
        ACEITO: false,
        ATENDIMENTO: false,
        INCOMPLETO: false,
      },
    };
  },
  computed: {
    // filteredProjects() {
    //   return this.projects.filter((project) => {
    //     const matchesSearch = (project.project_name || '')
    //       .toLowerCase()
    //       .includes(this.searchTerm.toLowerCase());

    //     const statusName =
    //       project.status_name === null ? 'INCOMPLETO' : project.status_name;

    //     const matchesStatus =
    //       (!this.filterStatus.ACEITO &&
    //         !this.filterStatus.ATENDIMENTO &&
    //         !this.filterStatus.INCOMPLETO) ||
    //       (this.filterStatus.ACEITO && statusName === 'ACEITO') ||
    //       (this.filterStatus.ATENDIMENTO && statusName === 'ATENDIMENTO') ||
    //       (this.filterStatus.INCOMPLETO && statusName === 'INCOMPLETO');

    //     return matchesSearch && matchesStatus;
    //   });
    // },
  },
  mounted() {
    this.get_user_by_uuid_logged();
    this.get_project_by_logged();
  },
  methods: {
    statusClass(status) {
      return {
        'text-green': status === 'ACEITO',
        'text-secondary': status === 'ATENDIMENTO',
      };
    },
    // ToCreate() {
    //   this.$router.push('/user/budget/');
    // },

    async get_user_by_uuid_logged() {
      fetch(`https://fortis-api.55technology.com/v1/user/logged/`, {
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
      this.isLoading = true;
      fetch('https://fortis-api.55technology.com/v1/budget/machine/logged/', {
        headers: { token: localStorage.getItem('access_token') },
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          return response.json();
        })
        .then((data) => {
          console.table(data.budget_machine);
          this.budget_machines = data.budget_machine.map((budget_machine) => ({
            icon: 'local_shipping',
            project_name: budget_machine.project_name,
            client_name: budget_machine.client_name,
            expected_date: budget_machine.expected_date,
            zip_code: budget_machine.zip_code,
            street: budget_machine.street,
            state_name: budget_machine.state_name,
            number_street: budget_machine.number_street,
            neighborhood: budget_machine.neighborhood,
            city_name: budget_machine.city_name,
            identifier: budget_machine.identifier,
            uuid: budget_machine.project_uuid,
            status_name: budget_machine.status_name,
            budget_machine_operator_uuid: budget_machine.budget_machine_operator_uuid,
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
        .finally(() => {
          this.isLoading = false;
        });
    },

    toggleFilter(status) {
      this.filterStatus[status] = !this.filterStatus[status];
    },
  },
};
</script>
<style scoped>
p {
  margin: 0;
  padding: 0;
}
</style>
