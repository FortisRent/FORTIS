<template>
  <!-- <q-btn
    flat
    round
    icon="arrow_back"
    class="q-mr-sm text-primary"
    color="black"
    style="position: absolute; left: 10px;"
    @click="$router.go(-1)"
  /> -->
  <div class="q-pa-sm">
    <q-card class="q-mt-lg" bordered style="border-color: secondary;">
      <q-tab-panels v-model="tab" animated>
        <!-- Aba Orçamentos -->
        <q-tab-panel name="orcamentos">
          <div>
            <div class="q-mt-sm">
              <div class="text-h5 text-bold text-secondary q-mb-md text-center">
                Criando um Projeto
              </div>
              <p class="text-subtitle1 q-mb-lg text-primary text-center">
                Escolha o tipo de equipamento que você precisa e o tipo de serviço.
              </p>
              <q-separator class="bg-grey" />

              <div class="q-mt-md">
                <!-- Skeleton Loader -->
                <template v-if="loading">
                  <q-card
                    v-for="index in 3"
                    :key="index"
                    class="q-mb-sm bg-grey-4 shadow-2 rounded"
                  >
                    <div class="q-pa-sm">
                      <q-skeleton type="circle" size="32px" class="q-mr-sm" />
                      <q-skeleton type="text" width="60%" />
                      <q-skeleton type="text" width="40%" />
                    </div>
                  </q-card>
                </template>

                <!-- Lista real -->
                <template v-else>
                  <q-card
                    v-for="(group, index) in machineCategoryGroups"
                    :key="group.uuid"
                    class="q-mb-sm bg-grey-4 shadow-2 rounded card-hover"
                    clickable
                    @click="navigateToCategory(index)"
                  >
                    <div class="flex items-center justify-between q-pa-sm">
                      <div class="flex items-center no-wrap gap-3">
                        <q-icon :name="categoryIcons[index]" size="32px" color="black" class="q-mr-sm" />
                        <div>
                          <p class="text-body1 text-bold m-0 text-primary">
                            {{ group.project_category_name }}
                          </p>
                          <span class="text-caption text-grey-7">
                            {{ categoryDescriptions[index] }}
                          </span>
                        </div>
                        <q-icon name="arrow_forward_ios" size="15px" color="black" />
                      </div>
                    </div>
                  </q-card>
                </template>
              </div>
            </div>
          </div>
        </q-tab-panel>
      </q-tab-panels>
    </q-card>
  </div>
</template>

<script>
export default {
  name: 'SolicitarOrcamento',
  data() {
    return {
      tab: 'orcamentos',
      machineCategoryGroups: [],
      categoryIcons: ['front_loader', 'fire_truck', 'construction'],
      categoryDescriptions: [
        'Escavadeiras, retroescavadeiras, caçambas,...',
        'Guindates, Munks, Plataformas Elevatórias, Empilhadeira',
        'Transporte de cargas, aluguel de ferramentas'
      ],
      loading: true, // Estado de carregamento
    };
  },
  methods: {
    async get_all_machine_category_group() {
      this.loading = true;
      fetch('https://fortis-api.55technology.com/v1/project/category/', {
        headers: { 'token': localStorage.getItem('access_token') }
      })
        .then(response => response.json())
        .then(data => {
          this.machineCategoryGroups = data.project_categories;
        })
        .catch(error => {
          console.error('Erro ao carregar categorias:', error);
          this.$q.notify({
            color: 'red-5',
            textColor: 'white',
            icon: 'cloud_done',
            message: 'Nenhuma categoria carregada.'
          });
        })
        .finally(() => {
          this.loading = false; // Desativa o loading quando a requisição terminar
        });
    },
    navigateToCategory(index) {
      const uuid = this.machineCategoryGroups[index]?.uuid;
      if (uuid) {
        const routes = [
          `/dashboard/create/yellow/project/${uuid}`,
          `/dashboard/create/lifting/project/${uuid}`,

        ];
        this.$router.push(routes[index]);
      }
    }
  },
  mounted() {
    this.get_all_machine_category_group();
  }
};
</script>

<style scoped>
.shadow-2 {
  box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
}

.card-hover {
  transition: transform 0.3s ease, background-color 0.3s ease;
}

.card-hover:hover {
  transform: scale(1.02);
  background-color: rgba(0, 0, 0, 0.05) !important;
}
</style>
