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
      <div class="text-h6 text-primary text-bold q-ml-md">Orçamento recebido</div>
    </div>

    <q-separator class="bg-secondary" />

    <!-- Informações do Projeto -->
    <div v-if="loading" class="q-pa-md" style="min-width: 300px">
      <q-list bordered separator style="border-color: secondary; border-radius: 5px" class="text-primary">
        <q-item v-for="i in 7" :key="i"><q-item-section><q-skeleton type="text" /></q-item-section></q-item>
      </q-list>
    </div>

    <div v-else class="q-pa-md" style="min-width: 300px">
      <q-list bordered separator style="border-color: secondary; border-radius: 5px" class="text-primary">
        <q-item>
          <q-item-section>
            <q-item-label overline class="text-bold">
              <strong class="text-primary text-h6">Informações do Projeto</strong>
            </q-item-label>
          </q-item-section>
        </q-item>

        <q-item>
          <q-item-section style="display:flex; gap:.5rem">
            <q-item-label class="text-weight-light">
              <strong class="text-bold">Data de criação:</strong> {{ created_at }}
            </q-item-label>
          </q-item-section>
        </q-item>

        <q-item>
          <q-item-section style="display:flex; gap:.5rem">
            <q-item-label class="text-weight-light">
              <strong class="text-bold">Data de Início:</strong> {{ expected_date }} {{ expected_hour }}
            </q-item-label>
          </q-item-section>
          <q-item-section style="display:flex; gap:.5rem">
            <q-item-label class="text-weight-light">
              <strong class="text-bold">Data do Fim:</strong> {{ expected_date_end }} {{ expected_hour_end }}
            </q-item-label>
          </q-item-section>
        </q-item>

        <q-item>
          <q-item-section style="display:flex; gap:.5rem">
            <q-item-label class="text-weight-light">
              <strong class="text-bold">Nome do projeto:</strong> {{ project_name }}
            </q-item-label>
          </q-item-section>
        </q-item>

        <q-item>
          <q-item-section style="display:flex; gap:.5rem">
            <q-item-label class="text-weight-light">
              <strong class="text-subtitle1 text-bold">Descrição:</strong> {{ project_description }}
            </q-item-label>
          </q-item-section>
        </q-item>

        <q-item>
          <q-item-section style="display:flex; gap:.5rem">
            <q-item-label class="text-weight-light">
              <strong class="text-bold">Nome do cliente:</strong> {{ client_name }}
            </q-item-label>
          </q-item-section>
        </q-item>

        <q-item>
          <q-item-section style="display:flex; gap:.5rem">
            <q-item-label class="text-weight-light">
              <strong class="text-bold">Número:</strong> {{ phone }}
            </q-item-label>
          </q-item-section>
        </q-item>

        <q-item>
          <q-item-label style="display:flex; gap:.5rem">
            <strong class="text-bold">Categoria:</strong>
            <span class="text-weight-light" v-for="(category, index) in name_machine_category" :key="index">
              {{ category }}<span v-if="index < name_machine_category.length - 1">, </span>
            </span>
          </q-item-label>
        </q-item>
      </q-list>
    </div>

    <!-- Informações Detalhadas -->
    <div v-if="loading" class="q-pa-md" style="min-width: 300px">
      <q-list bordered separator style="border-color: secondary; border-radius: 5px" class="text-primary">
        <q-item><q-item-section><q-item-label><q-skeleton type="text" /></q-item-label></q-item-section></q-item>
      </q-list>
    </div>

    <div v-else class="q-pa-md" style="min-width: 300px">
      <q-list bordered separator style="border-color: secondary; border-radius: 5px" class="text-primary">
        <q-expansion-item label="Categorias de referência" header-class="text-primary text-h6">
          <div v-if="project_stages && project_stages.length">
            <div v-for="(stage, index) in project_stages" :key="index" class="q-mb-md">
              <q-separator spaced />
              <div class="text-h6 q-ml-md">{{ stage.name_machine_category }}</div>

              <div v-if="stage.parameters">
                <div v-if="Object.keys(stage.parameters).length === 1">
                  <div v-for="(value, key) in stage.parameters" :key="key" class="q-ml-md q-mt-sm">
                    <p class="text-bold q-mb-xs">Tamanho:</p>
                    <p class="q-mb-md">{{ value }}</p>
                  </div>
                </div>
                <div v-else class="q-ml-md q-mt-sm">
                  <p v-if="stage.parameters.maximum_horizontal_reach">Alcance máximo horizontal (m): {{ stage.parameters.maximum_horizontal_reach }}</p>
                  <p v-if="stage.parameters.maximum_nominal_lifting_capacity">Capacidade máxima nominal de elevação (Kg / tn): {{ stage.parameters.maximum_nominal_lifting_capacity }}</p>
                  <p v-if="stage.parameters.maximum_nominal_vertical_reach_capacity">Capacidade máxima nominal de alcance vertical (m): {{ stage.parameters.maximum_nominal_vertical_reach_capacity }}</p>
                  <p v-if="stage.parameters.maximum_reach_without_jib">Alcance máximo sem JIB (m): {{ stage.parameters.maximum_reach_without_jib }}</p>
                  <p v-if="stage.parameters.maximum_vertical_reach">Alcance máximo vertical (m): {{ stage.parameters.maximum_vertical_reach }}</p>
                </div>
              </div>
            </div>
          </div>
        </q-expansion-item>
      </q-list>
    </div>

    <!-- Endereço do Projeto -->
    <div v-if="loading" class="q-pa-md" style="min-width: 300px">
      <q-list bordered separator style="border-color: secondary; border-radius: 5px" class="text-primary">
        <q-item><q-item-section><q-item-label><q-skeleton type="text" /></q-item-label></q-item-section></q-item>
      </q-list>
    </div>

    <div v-else class="q-pa-md" style="min-width: 300px">
      <q-list bordered separator style="border-color: secondary; border-radius: 5px" class="text-primary">
        <q-expansion-item label="Endereço do Projeto" header-class="text-primary text-h6">
          <p class="text-weight-light q-ml-md"><strong class="text-bold">Cep:</strong> {{ zip_code }}</p>
          <p class="text-weight-light q-ml-md"><strong class="text-bold">Rua:</strong> {{ street }}</p>
          <p class="text-weight-light q-ml-md"><strong class="text-bold">Número:</strong> {{ number_street }}</p>
          <p class="text-weight-light q-ml-md"><strong class="text-bold">Complemento:</strong> {{ complement }}</p>
          <p class="text-weight-light q-ml-md"><strong class="text-bold">Bairro:</strong> {{ neighborhood }}</p>
          <p class="text-weight-light q-ml-md"><strong class="text-bold">Cidade:</strong> {{ city_name }}</p>
          <p class="text-weight-light q-ml-md"><strong class="text-bold">Estado:</strong> {{ state_name }}</p>
        </q-expansion-item>
      </q-list>
    </div>

    <!-- Orçamento (visualização) -->
    <div v-if="loading" class="q-pa-md" style="min-width: 300px">
      <q-list bordered separator style="border-color: secondary; border-radius: 5px" class="text-primary">
        <q-item><q-item-section><q-item-label><q-skeleton type="text" /></q-item-label></q-item-section></q-item>
      </q-list>
    </div>

    <div v-else class="q-pa-md" style="min-width: 300px">
      <q-list bordered separator style="border-color: secondary; border-radius: 5px" class="text-primary">
        <q-expansion-item
          v-for="(machine, index) in budget_machine"
          :key="index"
          :label="`${index + 1} - ${machine.category_name || ''} - ${machine.model_name || ''}`"
          header-class="text-primary text-h6"
        >
          <q-form class="q-pa-md">
            <q-input class="q-mr-md q-mb-md" dense v-model="machine.brand" filled label="Marca" readonly style="max-width: 300px" />

            <!-- Parametrizações -->
            <q-input v-if="machine.parameters.excavator_size" class="q-mr-md q-mb-md" v-model="machine.parameters.excavator_size" filled label="Parâmetro" autogrow style="max-width: 600px" readonly />
            <q-input v-if="machine.parameters.backhoe_type" class="q-mr-md q-mb-md" v-model="machine.parameters.backhoe_type" filled label="Parâmetro" autogrow style="max-width: 600px" readonly />
            <q-input v-if="machine.parameters.loader_size" class="q-mr-md q-mb-md" v-model="machine.parameters.loader_size" filled label="Parâmetro" autogrow style="max-width: 600px" readonly />

            <!-- Elevação -->
            <div v-if="machine.jib !== null && machine.jib !== undefined">
              <p class="text-bold text-h6">Parâmetros de Elevação</p>
              <q-input v-if="machine.jib" class="q-mr-md q-mb-md" v-model="machine.jib" filled label="JIB (m)" style="max-width: 600px" readonly />
              <q-input v-if="machine.max_weight" class="q-mr-md q-mb-md" v-model="machine.max_weight" filled label="Peso Máximo (kg / tn)" style="max-width: 600px" readonly />
              <q-input v-if="machine.max_height" class="q-mr-md q-mb-md" v-model="machine.max_height" filled label="Altura Máxima (m)" style="max-width: 600px" readonly />
              <q-input v-if="machine.max_radius" class="q-mr-md q-mb-md" v-model="machine.max_radius" filled label="Raio Máximo (m)" style="max-width: 600px" readonly />
              <q-input v-if="machine.parameters.maximum_nominal_lifting_capacity" class="q-mr-md q-mb-md" v-model="machine.parameters.maximum_nominal_lifting_capacity" filled label="Capacidade máxima nominal de elevação (Kg / tn)" style="max-width: 600px" readonly />
              <q-input v-if="machine.parameters.maximum_nominal_vertical_reach_capacity" class="q-mr-md q-mb-md" v-model="machine.parameters.maximum_nominal_vertical_reach_capacity" filled label="Capacidade máxima nominal de alcance vertical (m)" style="max-width: 600px" readonly />
              <q-input v-if="machine.parameters.maximum_reach_without_jib" class="q-mr-md q-mb-md" v-model="machine.parameters.maximum_reach_without_jib" filled label="Alcance máximo sem JIB (m)" style="max-width: 600px" readonly />
              <q-input v-if="machine.parameters.maximum_vertical_reach" class="q-mr-md q-mb-md" v-model="machine.parameters.maximum_vertical_reach" filled label="Alcance máximo vertical (m)" style="max-width: 600px" readonly />
              <q-input v-if="machine.parameters.maximum_horizontal_reach" class="q-mr-md q-mb-md" v-model="machine.parameters.maximum_horizontal_reach" filled label="Alcance máximo horizontal (m)" style="max-width: 600px" readonly />
            </div>

            <!-- Valores financeiros (somente leitura) -->
            <div v-if="machine.price_per_hour != '0,00'">
              <q-checkbox
                class="text-bold"
                :model-value="'Franquia'"
                true-value="Franquia"
                false-value=""
                disable
              >
                FRANQUIA
              </q-checkbox>

              <p class="text-primary text-bold">
                Franquia mínima de {{ machine.budget_machine_minimum_rental_period }} horas
              </p>

              <q-input
                v-model="machine.budget_machine_price_per_hour"
                filled
                label="Valor Hora"
                style="max-width: 300px"
                class="q-mt-md q-mb-md"
                readonly
                :prefix="'R$ '"
              />

              <q-input
                v-model="machine.budget_machine_price_per_distance"
                filled
                label="Valor por km (R$/km)"
                style="max-width: 300px"
                class="q-mt-md q-mb-md"
                readonly
                :prefix="'R$ '"
              />

              <!-- Distância total (somente leitura) -->
              <q-input
                :model-value="total_distance"
                filled
                readonly
                label="Distância total (km)"
                style="max-width: 300px"
              />

              <!-- Custo de distância desta máquina -->
              <q-input
                :model-value="formatCurrency(distanceCostForMachine(machine))"
                filled
                readonly
                label="Custo de Distância"
                style="max-width: 320px"
                class="q-mt-md"
              />
            </div>
          </q-form>
        </q-expansion-item>
      </q-list>
    </div>

    <!-- Documentação / Condição  -->
    <div v-if="loading" class="q-pa-md" style="min-width: 300px">
      <q-list bordered separator style="border-color: secondary; border-radius: 5px" class="text-primary">
        <q-item><q-item-section><q-item-label><q-skeleton type="text" /></q-item-label></q-item-section></q-item>
      </q-list>
    </div>

    <div v-else class="q-pa-md" style="min-width: 300px">
      <q-list bordered separator style="border-color: secondary; border-radius: 5px" class="text-primary">
        <q-expansion-item label="Licenças, Taxas e Outros" header-class="text-primary text-h6">
          <div v-for="(item, index) in documentItems" :key="index" class="q-pa-md">
            <p class="text-primary text-bold q-mb-sm">{{ item.charge_name }}</p>
            <div class="row q-col-gutter-md">
              <div class="col-12 col-md-4">
                <q-input
                  :model-value="item.fee_amount"
                  readonly
                  filled
                  label="Valor"
                  :prefix="'R$ '"
                />
              </div>
              <div v-if="item.observation" class="col-12 col-md-8">
                <q-input v-model="item.observation" readonly filled label="Observações" />
              </div>
            </div>
          </div>

          <div class="q-pa-md">
            <p class="text-bold q-mb-xs">CONDIÇÃO:</p>
            <p class="q-mb-md">{{ condition2 }}</p>

            <p class="text-bold q-mb-xs">DESCRIÇÃO:</p>
            <p class="q-mb-md">{{ observation2 }}</p>
          </div>

          <!-- Totais (somente leitura) -->
          <div class="q-pa-md">
            <div class="row q-col-gutter-md">
              <div class="col-12 col-md-4">
                <q-input
                  :model-value="totalFeeBRL"
                  filled
                  label="Valor Total das Taxas e Licenças"
                  readonly
                />
              </div>

              <div class="col-12 col-md-4">
                <q-input
                  :model-value="total_distance"
                  filled
                  label="Distância total (km)"
                  readonly
                />
              </div>

              <div class="col-12 col-md-4">
                <q-input
                  :model-value="distanceTotalCostBRL"
                  filled
                  label="Custo Total de Distância"
                  readonly
                />
              </div>

              <div class="col-12 col-md-4">
                <q-input
                  v-for="(machine, index) in budget_machine"
                  :key="'franquia-' + index"
                  :model-value="formatCurrency(
                    parseMoney(machine.budget_machine_price_per_hour) *
                    (parseFloat(machine.budget_machine_minimum_rental_period) || 10)
                  )"
                  :label="`Valor franquia mínima: ${machine.machine_name}`"
                  filled
                  readonly
                />
              </div>

              <div class="col-12 col-md-4">
                <q-input
                  :model-value="amountTotalBRL"
                  filled
                  label="Valor Total"
                  readonly
                />
              </div>
            </div>

            <div class="q-mt-md">
              <q-btn
                v-if="status_name !== 'ACEITO'"
                color="secondary"
                :to="`/user/project/date/${this.$route.params.budget_uuid}`"
                class="full-width"
              >
                Desejo agendar o serviço
              </q-btn>
              <div class="flex justify-center" style="margin-top: 5px">
                <q-btn color="orange" icon-right="picture_as_pdf" label="Exportar orçamento" @click="getBudgetPDF()"/>
              </div>
            </div>
          </div>
        </q-expansion-item>
      </q-list>
    </div>
  </q-page>
</template>

<script>
import { ref } from 'vue';

export default {
  name: 'ProjectBudget',
  data() {
    return {
      loading: true,

      // cabeçalho
      client_name: '',
      created_at: '',
      email: '',
      phone: '',
      name_machine_category: [],
      expected_date: '',
      expected_date_end: '',
      expected_hour: '',
      expected_hour_end: '',
      project_name: '',
      project_description: '',
      project_uuid: '',
      status_name: '',

      // endereço
      zip_code: '',
      street: '',
      number_street: '',
      complement: '',
      neighborhood: '',
      city_name: '',
      state_name: '',

      // listas e itens do orçamento
      project_stages: [],
      budget_machine: [],
      documentItems: [],

      // campos textuais do orçamento
      condition2: '',
      observation2: '',

      // totais normalizados (em centavos)
      total_fee_cents: 0,
      amount_cents: 0,

      // distância total enviada pelo prestador
      total_distance: 0,
    };
  },

  async mounted() {
    await this.get_budget_by_project_uuid();
    if (this.project_uuid) {
      await this.get_project_by_uuid();
    }
    this.loading = false;
  },

  computed: {
    totalFeeBRL() {
      return this.formatCurrency(this.fromCents(this.total_fee_cents));
    },
    amountTotalBRL() {
      return this.formatCurrency(this.fromCents(this.amount_cents));
    },
    distanceTotalCostBRL() {
      const total = this.budget_machine.reduce((acc, m) => acc + this.distanceCostForMachine(m), 0);
      return this.formatCurrency(total);
    },
  },

  methods: {
    // ---- helpers de formato/parse ----
    parseMoney(value) {
      if (typeof value === 'string') value = value.replace(/\./g, '').replace(',', '.');
      const n = parseFloat(value);
      return Number.isFinite(n) ? n : 0;
    },
    formatCurrency(value) {
      const num = Number(value) || 0;
      return num.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    },
    fromCents(cents) {
      const n = Number(cents);
      return Number.isFinite(n) ? n / 100 : 0;
    },
    // converte qualquer formato para centavos
    normalizeToCents(v) {
      if (v === null || v === undefined || v === '') return null;
      if (typeof v === 'string') return Math.round(this.parseMoney(v) * 100); // "4.160,00" -> 416000
      if (!Number.isInteger(v))  return Math.round(Number(v) * 100);         // 4160.00 -> 416000
      return v; // já são centavos
    },
    parseDistance(value) {
      if (value === null || value === undefined || value === '') return 0;
      if (typeof value === 'string') value = value.replace(',', '.');
      const n = parseFloat(value);
      return Number.isFinite(n) && n >= 0 ? n : 0;
    },
    distanceCostForMachine(machine) {
      const km = this.parseDistance(this.total_distance);
      const pricePerKm = this.parseMoney(machine.budget_machine_price_per_distance || 0);
      return km * pricePerKm;
    },

    // ---- fetchers ----
    async get_project_by_uuid() {
      await fetch(`http://localhost:5510/v1/project/${this.project_uuid}`, {
        headers: { token: localStorage.getItem('access_token') },
      })
        .then((r) => {
          if (!r.ok) throw new Error('Network response was not ok');
          return r.json();
        })
        .then((data) => {
          this.created_at = data.project.created_at;
          this.email = data.project.email;
          this.phone = data.project.phone;
          this.project_name = data.project.project_name;
          this.project_description = data.project.project_description;
          this.name_machine_category = data.project_stages.map((s) => s.name_machine_category);
          this.project_stages = data.project_stages;

          this.zip_code = data.project.zip_code;
          this.street = data.project.street;
          this.number_street = data.project.number_street;
          this.complement = data.project.complement;
          this.neighborhood = data.project.neighborhood;
          this.city_name = data.project.city_name;
          this.state_name = data.project.state_name;
        })
        .catch((err) => {
          console.error('Error fetching data:', err);
          this.$q.notify({ color: 'red-5', textColor: 'white', icon: 'error', message: 'Nenhum orçamento cadastrado!' });
        });
    },

    async get_budget_by_project_uuid() {
      await fetch(`http://localhost:5510/v1/budget/${this.$route.params.budget_uuid}`, {
        headers: { token: localStorage.getItem('access_token') },
      })
        .then((r) => {
          if (!r.ok) throw new Error('Network response was not ok');
          return r.json();
        })
        .then((data) => {
          // cabeçalho
          this.project_name     = data.budget.project_name;
          this.client_name      = data.budget.client_name;
          this.created_at       = data.budget.created_at;
          this.status_name      = data.budget.status_name;
          this.project_uuid     = data.budget.project_uuid;
          this.expected_date    = data.budget.expected_date;
          this.expected_date_end= data.budget.expected_date_end;
          this.expected_hour    = data.budget.expected_hour;
          this.expected_hour_end= data.budget.expected_hour_end;

          // textos
          this.condition2       = data.budget.condition;
          this.observation2     = data.budget.budget_observation;

          // taxas/itens
          this.documentItems    = data.budget_charge || [];

          // distância total (se o backend mandar dentro de budget; se não, fica 0)
          this.total_distance   = data.budget?.total_distance ?? 0;

          // máquinas
          this.budget_machine = (data.budget_machine || []).map((machine) => ({
            ...machine,
            parameters: machine.parameters || {},
            budget_machine_price_per_hour: machine.budget_machine_price_per_hour,
            budget_machine_price_per_distance: machine.budget_machine_price_per_distance,
            budget_machine_minimum_rental_period: machine.budget_machine_minimum_rental_period,
          }));

          // ---- NORMALIZAÇÃO DOS TOTAIS (strings pt-BR -> centavos) ----
          this.total_fee_cents = this.normalizeToCents(data.total_fee) ?? 0;
          this.amount_cents    = this.normalizeToCents(data.amount)    ?? null;

          // Se o backend NÃO mandou amount (ou mandou vazio), recalcula na view:
          if (this.amount_cents === null) {
            const franquiaBRL = this.budget_machine.reduce((acc, m) => {
              const valorHora = this.parseMoney(m.budget_machine_price_per_hour || 0);
              const horas     = parseFloat(m.budget_machine_minimum_rental_period) || 10;
              return acc + (valorHora * horas);
            }, 0);
            const distanceBRL = this.budget_machine.reduce((acc, m) => acc + this.distanceCostForMachine(m), 0);
            const feesBRL     = this.fromCents(this.total_fee_cents);
            this.amount_cents = Math.round((franquiaBRL + distanceBRL + feesBRL) * 100);
          }
        })
        .catch((err) => {
          console.error('Error fetching data:', err);
        });
    },

    async getBudgetPDF() {
      try {
        const response = await fetch(`http://localhost:5510/v1/budget/pdf/${this.$route.params.budget_uuid}`, {
          method: 'GET',
          headers: { token: localStorage.getItem('access_token') },
        });
        if (!response.ok) throw new Error(`Erro ao buscar caminho do PDF: ${response.statusText}`);
        const data = await response.json();
        window.open(`http://localhost:5510${data.output}`, '_blank');
      } catch (error) {
        console.error('Erro ao buscar ou abrir o PDF:', error);
      }
    },
  },
};
</script>
