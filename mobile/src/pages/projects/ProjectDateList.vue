<template>
    <q-page class="bg-white">
      <div class="row justify-around items-center q-pa-md q-mt-lg">
        <q-btn
          flat
          round
          icon="chevron_left"
          class="text-primary"
          color="secondary"
          size="18px"
          style="position: absolute; left: 10px"
          @click="$router.go(-1)"
        />
        <div class="text-h6 text-primary text-bold q-ml-md">
          Lista de Orçamentos Aceito
        </div>
      </div>
  
      <q-separator class="bg-grey" />
  
      <div v-if="loading" class="q-pa-md">
        <q-card v-for="n in 3" :key="n" class="card-style q-pa-sm q-mb-md">
          <div class="col items-left">
            <div class="row items-center no-wrap q-pa-sm">
              <q-skeleton type="text" width="200px"/>
            </div>
  
            <div class="text-left q-pa-sm text-caption">
              <q-skeleton type="text"/>
              <q-skeleton type="text"/>
              <q-skeleton type="text"/>
            </div>
          </div>
        </q-card>
      </div>
  
      <div v-else class="q-pa-md">
        <q-card
          v-for="budget in project_list"
          :key="budget.budget_uuid"
          class="card-style q-pa-sm q-mb-md"
        >
          <q-btn
            no-caps
            flat
            :to="`/user/project/date/details/${budget.budget_uuid}`"
            class="full-width"
          >
            <div class="col items-left">
              <div class="row items-center no-wrap">
                <q-icon
                  name="front_loader"
                  size="22px"
                  color="secondary"
                  class="q-mr-sm"
                />
                <p
                  class="text-bold text-primary text-subtitle1 no-margin text-truncate"
                >
                  {{ budget.company_name }}
                </p>
              </div>
  
              <div class="text-left q-pa-sm text-caption">
                <p class="text-primary no-margin">
                  Nome do Projeto: {{ budget.machine_name }}
                </p>
                <p class="text-primary no-margin">
                  Data criada: {{ budget.created_at }}
                </p>
                <p class="text-primary no-margin">
                  Status: {{ budget.status_name }}
                </p>
              </div>
            </div>
          </q-btn>
        </q-card>
      </div>
    </q-page>
  </template>
  
  <script>
  export default {
    name: 'ProposalList',
    data() {
      return {
        loading: true,
        project_list: [],
      };
    },
    mounted() {
      this.get_budget_proposal_by_uuid();
    },
    methods: {
      to_edit() {
        this.$router.push('/user/manage/edit/');
      },
      async get_budget_proposal_by_uuid() {
        fetch(
          `https://fortis-api.55technology.com/v1/budget/project/${this.$route.params.project_uuid}`,
          {
            headers: { token: localStorage.getItem('access_token') },
          }
        )
          .then((response) => {
            if (!response.ok) {
              throw new Error('Network response was not ok');
            }
            return response.json();
          })
          .then((data) => {
            console.table(data);
            this.project_list = data.budget;
  
            this.loading = false;
          })
          .catch((error) => {
            console.error('Error fetching data:', error);
            this.$q.notify({
              color: 'red-5',
              textColor: 'white',
              icon: 'cloud_done',
              message: 'Nenhuma orçamento cadastrado.',
            });
            this.loading = false;
          });
      },
    },
  };
  </script>
  
  <style scoped>
  .text-truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
    max-width: 80%;
  }
  .card-style {
    border-radius: 10px;
    min-width: 350px;
  }
  </style>
  