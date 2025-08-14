<template>
  <div
    class="q-pa-md"
    style="height: calc(100vh - 80px); display: flex; flex-direction: column"
  >
    <div class="row q-mb-md">
      <div class="q-ml-lg text-primary">
          <q-btn icon="star" class="q-mr-md" label="Ferramentas" :to="`/dashboard/finance/${this.$route.params.company_uuid}`"/>
          <q-btn icon="history" class="q-mr-md" label="Histórico"  :to="`/dashboard/history/${this.$route.params.company_uuid}`"/>
      </div>
      <q-input
        v-model="searchTerm"
        label="Digite para filtrar..."
        outlined
        dense
        class=""
        style="min-width: 500px; margin-left: 2cqmin"
        clearable
        text-color="black"
        color="grey"
      >
        <template v-slot:append>
          <q-icon name="search" />
        </template>
      </q-input>
    </div>

    <template v-if="isLoading">
      <div class="q-pa-md">
        <q-markup-table>
          <thead>
            <tr>
              <th class="text-left" style="width: 150px">
                <q-skeleton animation="blink" type="text" />
              </th>
              <th class="text-right">
                <q-skeleton animation="blink" type="text" />
              </th>
              <th class="text-right">
                <q-skeleton animation="blink" type="text" />
              </th>
              <th class="text-right">
                <q-skeleton animation="blink" type="text" />
              </th>
              <th class="text-right">
                <q-skeleton animation="blink" type="text" />
              </th>
              <th class="text-right">
                <q-skeleton animation="blink" type="text" />
              </th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="n in 15" :key="n">
              <td class="text-left">
                <q-skeleton animation="blink" type="text" width="100px" />
              </td>
              <td class="text-right">
                <q-skeleton animation="blink" type="text" width="100px" />
              </td>
              <td class="text-right">
                <q-skeleton animation="blink" type="text" width="100px" />
              </td>
              <td class="text-right">
                <q-skeleton animation="blink" type="text" width="100px" />
              </td>
              <td class="text-right">
                <q-skeleton animation="blink" type="text" width="100px" />
              </td>
              <td class="text-right">
                <q-skeleton animation="blink" type="text" width="100px" />
              </td>
            </tr>
          </tbody>
        </q-markup-table>
      </div>
    </template>

    <q-table
      :rows="filteredRows"
      :columns="columns"
      row-key="budget_uuid"
      @row-click="change_method"
      :pagination="pagination"
      v-else
      style="flex-grow: 1; overflow-y: auto"
      class="table scroll"
    >
      <template v-slot:body-cell-status="props">
        {{ console.log(props.rows) }}

        <q-td
          :props="props"
          @click.stop="openModal(props.row)"
          class="cursor-pointer"
        >
          <template v-if="props.row.status">
            <q-icon
              :name="getStatusIcon(props.row.status)"
              :color="getStatusColor(props.row.status)"
              size="sm"
              class="q-mr-sm"
            />
            {{ props.row.status }}
          </template>
          <template v-else>
            <q-icon name="help" color="grey" size="sm" class="q-mr-sm" />
            <span>Sem status</span>
          </template>
        </q-td>
      </template>
    </q-table>

    <!-- MODAL -->
    <q-dialog v-model="modalOpen">
      <q-card style="min-width: 400px">
        <q-card-section style="padding-bottom: 0">
          <div class="text-h6 text-primary">
            PROJETO: {{ selectedItem?.project_name }}
          </div>
        </q-card-section>

        <q-card-section>
          <div class="client-info">
            <strong>Cliente:</strong>
            <span>{{ selectedItem?.client_name }}</span>
          </div>
          <div class="client-info">
            <strong>Contato:</strong>
            <span>{{ selectedItem?.contact }}</span>
          </div>
          <q-select
            v-model="selectedItem.status"
            :options="statusOptions"
            label="Alterar Status"
            outlined
            dense
            emit-value
            map-options
            class="q-mt-md"
            popup-content-style="color: #000; font-weight: 400; scrollbar-width: thin; scrollbar-color: #303940 #ccc;"
          />
        </q-card-section>

        <q-card-actions align="right" style="padding-right: 15px">
          <q-btn flat label="Fechar" v-close-popup color="secondary" />
          <q-btn color="primary" label="Salvar" @click="saveStatus" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>

<script>
export default {
  data() {
    return {
      isLoading: true,
      searchTerm: '',
      selectedColumn: 'project_name',
      modalOpen: false,
      selectedItem: null,
      columnOptions: [
        { label: 'Status', value: 'status' },
        { label: 'Projeto', value: 'project' },
        { label: 'Cliente', value: 'client' },
        { label: 'Contato', value: 'contact' },
        { label: 'Criação', value: 'machine_name' },
      ],
      columns: [
        {
          name: 'status',
          label: 'Status',
          field: 'status',
          align: 'left',
          sortable: true,
          headerStyle: 'font-size: 18px',
          style: 'font-weight: 400'
        },
        {
          name: 'created_at',
          label: 'Data de Criação',
          field: 'created_at',
          align: 'left',
          sortable: true,
          headerStyle: 'font-size: 18px',
          style: 'font-weight: 400'
        },
        {
          name: 'project_name',
          label: 'Nome do Projeto',
          field: 'project_name',
          align: 'left',
          sortable: true,
          headerStyle: 'font-size: 18px',
          style: 'font-weight: 400'
        },
        {
          name: 'address',
          label: 'Endereço',
          field: 'address',
          align: 'left',
          sortable: true,
          headerStyle: 'font-size: 18px',
          style: 'font-weight: 400'
        },
        {
          name: 'client_name',
          label: 'Nome do Cliente',
          field: 'client_name',
          align: 'left',
          sortable: true,
          headerStyle: 'font-size: 18px',
          style: 'font-weight: 400'
        },
        {
          name: 'contact',
          label: 'Contato',
          field: 'contact',
          align: 'left',
          sortable: true,
          headerStyle: 'font-size: 18px',
          style: 'font-weight: 400'
        },
        {
          name: 'machine_name',
          label: 'Máquina',
          field: 'machine_name',
          align: 'left',
          sortable: true,
          headerStyle: 'font-size: 18px',
          style: 'font-weight: 400'
        },
        {
          name: 'date_operation',
          label: 'Data de Operação',
          field: 'date_operation',
          align: 'left',
          sortable: true,
          headerStyle: 'font-size: 18px',
          style: 'font-weight: 400'
        },
        {
          name: 'identifier',
          label: 'Código',
          field: 'identifier',
          align: 'left',
          sortable: true,
          headerStyle: 'font-size: 18px',
          style: 'font-weight: 400'
        },
      ],
      statusOptions: [
        'ATENDIMENTO',
        'Recusado',
        'Análise Técnica',
        'Vistoria',
        'ORÇAMENTAÇÃO',
        'Formulação de Proposta',
        'Reformulação de Proposta',
        'Proposta Enviada',
        'ACEITO',
        'Agendado',
        'Em Execução',
        'Concluído',
        'Em Cobrança',
        'Pago',
      ],
      rows: [],
      pagination: { rowsPerPage: 14 },
    };
  },
  computed: {
    filteredRows() {
      if (!this.searchTerm || !this.selectedColumn) return this.rows;
      const search = this.searchTerm.toLowerCase();
      return this.rows.filter((row) =>
        String(row[this.selectedColumn]).toLowerCase().includes(search)
      );
    },
  },
  methods: {
    openModal(item) {
      this.selectedItem = { ...item };
      this.modalOpen = true;
    },
    saveStatus() {
      const index = this.rows.findIndex(
        (row) => row.budget_uuid === this.selectedItem.budget_uuid
      );
      if (index !== -1) {
        this.rows[index].status = this.selectedItem.status;
      }
      this.modalOpen = false;
    },
    change_method(evt, row, index) {
      console.log(row);
      this.$router.push('/dashboard/project/details/' + row.budget_uuid);
    },
    getStatusIcon(status) {
      switch (status) {
        case 'ATENDIMENTO':
          return 'record_voice_over';
        case 'Recusado':
          return 'cancel';
        case 'Análise Técnica':
          return 'science';
        case 'Vistoria':
          return 'visibility';
        case 'ORÇAMENTAÇÃO':
          return 'request_quote';
        case 'Formulação de Proposta':
          return 'edit_document';
        case 'Reformulação de Proposta':
          return 'sync';
        case 'Proposta Enviada':
          return 'send';
        case 'ACEITO':
          return 'thumb_up';
        case 'Agendado':
          return 'event';
        case 'Em Execução':
          return 'construction';
        case 'Concluído':
          return 'check_circle';
        case 'Em Cobrança':
          return 'attach_money';
        case 'Pago':
          return 'credit_score';
        default:
          return 'help_outline';
      }
    },
    getStatusColor(status) {
      switch (status) {
        case 'ATENDIMENTO':
          return 'blue';
        case 'Recusado':
          return 'red';
        case 'Análise Técnica':
          return 'purple';
        case 'Vistoria':
          return 'indigo';
        case 'ORÇAMENTAÇÃO':
          return 'green';
        case 'Formulação de Proposta':
          return 'teal';
        case 'Reformulação de Proposta':
          return 'secondary';
        case 'Proposta Enviada':
          return 'blue';
        case 'ACEITO':
          return 'green';
        case 'Agendado':
          return 'blue-grey';
        case 'Em Execução':
          return 'amber';
        case 'Concluído':
          return 'green';
        case 'Em Cobrança':
          return 'deep-secondary';
        case 'Pago':
          return 'light-green';
        default:
          return 'grey';
      }
    },
    async get_budget_by_company_uuid() {
      this.isLoading = true;
      try {
        const response = await fetch(
          `https://fortis-api.55technology.com/v1/budget/company/${this.$route.params.company_uuid}`,
          {
            headers: { token: localStorage.getItem('access_token') },
          }
        );

        if (!response.ok) throw new Error('Network response was not ok');

        const data = await response.json();
        console.log('Dados recebidos:', data);

        // Preenchendo a tabela com os dados da API
        this.rows = data.company_budget.map((item) => ({
          budget_uuid: item.budget_uuid,
          status: item.status_name,
          created_at: item.created_at,
          project_name: item.project_name,
          client_name: item.client_name,
          contact: item.contact,
          machine_name: item.machine_name,
          identifier: item.identifier,
        }));

        this.pagination = { rowsPerPage: 14 }; // Se precisar de paginação
      } catch (error) {
        console.error('Erro ao buscar dados:', error);
        this.$q.notify({
          color: 'red-5',
          textColor: 'white',
          icon: 'cloud_done',
          message: 'Nenhum Projeto cadastrado.',
        });
      } finally {
        this.isLoading = false;
      }
    },
  },
  mounted() {
    localStorage.setItem('company_uuid', this.$route.params.company_uuid);
    this.get_budget_by_company_uuid();
  },
};
</script>

<style lang="scss" scoped>
.client-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: $primary;

  strong {
    font-size: 1rem;
  }

  span {
    font-size: 1.2rem;
    font-weight: 400;
    color: $secondary;
  }
}
</style>
