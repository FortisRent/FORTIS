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
        style="position: absolute; left: 10px;"
        @click="$router.go(-1)"
      />
      <div class="text-h6 text-primary text-bold q-ml-md">
        Projetos
      </div>
    </div> 

    <q-separator class="bg-grey" />
    
    <!-- Barra de pesquisa -->
    <div class="flex justify-center">
      <q-input 
        v-model="searchTerm"
        class="q-pa-sm"
        filled 
        label="Pesquisar" 
        style="min-width: 350px;"
        color="secondary"
        @input="debouncedApplyFilters"
      >
        <template v-slot:prepend>
          <q-icon name="search" color="black" size="sm" class="q-ml-sm" />
        </template>
      </q-input>
    </div>

    <!-- Botão de filtro -->
    <div class="row justify-end q-mr-md">
      <q-btn
        flat
        no-caps
        label="Filtrar"
        color="black"
        icon="tune"
        size="13px"
      >
        <q-menu>
          <q-list dense>
            <q-item class="text-green text-bold" clickable v-ripple>
              <q-item-section>
                <q-checkbox v-model="filterStatus.ACEITO" label="Aceito" @update:model-value="applyFilters"/>
              </q-item-section>
            </q-item>
            <q-item class="text-secondary text-bold" clickable v-ripple>
              <q-item-section>
                <q-checkbox v-model="filterStatus.ATENDIMENTO" label="Atendimento" @update:model-value="applyFilters"/>
              </q-item-section>
            </q-item>
          </q-list>
        </q-menu>
      </q-btn>
    </div>

    <!-- Lista filtrada de projetos -->
    <div class="q-pa-md">
      <q-card 
        v-for="(budget, index) in filteredBudgets" 
        :key="index" 
        class="card-style q-pa-sm q-mb-md"
      >
        <div class="row flex justify-between q-mb-none">
          <div class="row items-center no-wrap">
            <q-icon
              name="analytics"
              size="22px"
              color="secondary"
              class="q-mr-sm"
            />
            <p class="text-bold text-primary text-subtitle1 no-margin">
              {{ budget.project_name || 'Sem nome' }}
            </p>
          </div>
        </div>
        <div class="text-left q-pa-sm text-caption">
          <p class="text-primary no-margin text-truncate">Status: {{ budget.status_name }}</p>
          <p class="text-primary no-margin text-truncate">Data do serviço: {{ budget.expected_date }}</p>
          <p class="text-primary no-margin text-truncate">Nome do Projeto: {{ budget.project_name }}</p>
          <p class="text-primary no-margin text-truncate">Máquina: {{ budget.machine_name }}</p>
          <p class="text-primary no-margin text-truncate">Contato: </p>
          <p class="text-primary no-margin text-truncate">Valor: </p>
        </div>
      </q-card>

      <!-- Mensagem caso a lista esteja vazia -->
      <div v-if="filteredBudgets.length === 0" class="text-center q-pa-md text-grey">
        <p>Nenhum projeto encontrado.</p>
      </div>
    </div>
  </q-page>  
</template>

<script>
import { debounce } from 'lodash';

export default {
  name: 'CompanyProjects',
  data() {
    return {
      loading: true,
      budget_list: [], // Aqui você armazena a lista completa de projetos
      searchTerm: "", // Termo da pesquisa
      filterStatus: { ACEITO: false, ATENDIMENTO: false }, // Estado dos filtros
      filteredBudgets: [] // Lista filtrada de projetos
    };
  },
  mounted() {
    this.get_budget_by_company_uuid();
    this.debouncedApplyFilters = debounce(this.applyFilters, 500);
  },
  methods: {
    async get_budget_by_company_uuid() {
      try {
        const response = await fetch(`http://localhost:5510/v1/budget/company/${this.$route.params.company_uuid}`, {
          headers: { 'token': localStorage.getItem('access_token') }
        });

        if (!response.ok) throw new Error('Network response was not ok');

        const data = await response.json();
        console.log("Dados recebidos:", data);

        this.budget_list = data.company_budget || [];
        this.filteredBudgets = [...this.budget_list]; 
        this.loading = false;
      } catch (error) {
        console.error('Erro ao buscar dados:', error);
        this.$q.notify({
          color: 'red-5',
          textColor: 'white',
          icon: 'cloud_done',
          message: 'Nenhum Projeto cadastrado.'
        });
        this.loading = false;
      }
    },

    applyFilters() {
      this.filteredBudgets = this.budget_list.filter(budget => {
        const matchesSearch = budget.project_name && budget.project_name.toLowerCase().includes(this.searchTerm.toLowerCase());

        const matchesStatus = 
          (!this.filterStatus.ACEITO && !this.filterStatus.ATENDIMENTO) || 
          (this.filterStatus.ACEITO && budget.status_name === 'ACEITO') || 
          (this.filterStatus.ATENDIMENTO && budget.status_name === 'ATENDIMENTO');
        
        return matchesSearch && matchesStatus;
      });
    }
  }
};
</script>

<style scoped>
.text-truncate {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  display: block;
  max-width: 100%;
}
.card-style {
  border-radius: 10px;
  max-width: 350px;
}
</style>
