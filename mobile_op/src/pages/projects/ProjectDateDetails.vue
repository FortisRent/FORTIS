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
        <div class="text-h6 text-primary text-bold q-ml-md">Orçamento AGENDADO</div>
      </div>
      <q-separator class="bg-secondary" />
      <div class="q-pa-md" style="min-width: 300px;">
            <q-list bordered separator style="border-color: secondary;" class="text-primary">
                <q-item>
                    <q-item-section>
                        <q-item-label overline class="text-primary"><strong>IMPORTANTE</strong></q-item-label>
                        <q-item-label caption class="text-primary">Aqui estão as informações detalhadas do seu agendamento, Por favor entrar em contato
                          com a Empresa contratada para combinar o encontro, pagamento e afins do serviço que será feito.
                        </q-item-label>
                    </q-item-section>
                </q-item>
            </q-list>
        </div>
        <div class="q-pa-md" style="min-width: 300px;">
            <q-list bordered separator style="border-color: secondary;" class="text-primary">
                <q-item>
                    <q-item-section>
                        <q-item-label overline class="text-primary"><strong>CONTATO DA EMPRESA CONTRATADA</strong></q-item-label>
                        <a  href="https://wa.me/5548996955010" target="_blank" >Fale com a {{ company_name }} através do WhattsApp</a>

                    </q-item-section>
                </q-item>
            </q-list>
        </div>
      <!-- Informações do Projeto -->
      <div v-if="loading" class="q-pa-md" style="min-width: 300px">
        <q-list
          bordered
          separator
          style="border-color: secondary; border-radius: 5px"
          class="text-primary"
        >
          <q-item>
            <q-item-section>
              <q-skeleton type="text" />
            </q-item-section>
          </q-item>
          <q-item>
            <q-item-section>
              <q-skeleton type="text" />
            </q-item-section>
          </q-item>
          <q-item>
            <q-item-section>
              <q-skeleton type="text" />
            </q-item-section>
          </q-item>
          <q-item>
            <q-item-section>
              <q-skeleton type="text" />
            </q-item-section>
          </q-item>
          <q-item>
            <q-item-section>
              <q-skeleton type="text" />
            </q-item-section>
          </q-item>
          <q-item>
            <q-item-section>
              <q-skeleton type="text" />
            </q-item-section>
          </q-item>
          <q-item>
            <q-item-section>
              <q-skeleton type="text" />
            </q-item-section>
          </q-item>
        </q-list>
      </div>
  
      <div v-else class="q-pa-md" style="min-width: 300px">
        <q-list
          bordered
          separator
          style="border-color: secondary; border-radius: 5px"
          class="text-primary"
        >
          <q-item>
            <q-item-section>
              <q-item-label overline class="text-bold"
                ><strong class="text-primary text-h6"
                  >Informações do Projeto</strong
                ></q-item-label
              >
            </q-item-section>
          </q-item>
          <q-item>
            <q-item-section style="display: flex; gap: 0.5rem">
              <q-item-label class="text-weight-light"
                ><strong class="text-bold">Data do serviço:</strong>
                {{ expected_date }}</q-item-label
              >
            </q-item-section>
          </q-item>
          <q-item>
            <q-item-section style="display: flex; gap: 0.5rem">
              <q-item-label class="text-weight-light"
                ><strong class="text-bold">Nome do projeto:</strong>
                {{ project_name }}</q-item-label
              >
            </q-item-section>
          </q-item>
          <q-item>
            <q-item-section style="display: flex; gap: 0.5rem">
              <q-item-label class="text-weight-light"
                ><strong class="text-subtitle1 text-bold">Descrição:</strong>
                {{ project_description }}</q-item-label
              >
            </q-item-section>
          </q-item>
          <q-item>
            <q-item-section style="display: flex; gap: 0.5rem">
              <q-item-label class="text-weight-light"
                ><strong class="text-bold">Nome do cliente:</strong>
                {{ user_name }}</q-item-label
              >
            </q-item-section>
          </q-item>
          <q-item>
            <q-item-section style="display: flex; gap: 0.5rem">
              <q-item-label class="text-weight-light"
                ><strong class="text-bold">Número:</strong>
                {{ phone }}</q-item-label
              >
            </q-item-section>
          </q-item>
          <q-item>
            <q-item-label style="display: flex; gap: 0.5rem">
              <strong class="text-bold">Categoria:</strong>
              <span
                class="text-weight-light"
                v-for="(category, index) in name_machine_category"
                :key="index"
              >
                {{ category
                }}<span v-if="index < name_machine_category.length - 1">, </span>
              </span>
            </q-item-label>
          </q-item>
        </q-list>
      </div>
      <!-- Informações Detalhadas do projeto -->
      <div v-if="loading" class="q-pa-md" style="min-width: 300px">
        <q-list
          bordered
          separator
          style="border-color: secondary; border-radius: 5px"
          class="text-primary"
        >
          <q-item>
            <q-item-section>
              <q-item-label>
                <q-skeleton type="text" />
              </q-item-label>
            </q-item-section>
          </q-item>
        </q-list>
      </div>
  
      <div v-else class="q-pa-md" style="min-width: 300px">
        <q-list
          bordered
          separator
          style="border-color: secondary; border-radius: 5px"
          class="text-primary"
        >
          <q-expansion-item
            label="Informações Detalhadas"
            header-class="text-primary text-h6"
          >
          <div v-if="project_stages && project_stages.length">
            <div
              v-for="(stage, index) in project_stages"
              :key="index"
              class="q-mb-md"
            >
              <q-separator spaced />
              <div class="text-h6 q-ml-md">
                {{ stage.name_machine_category }}
              </div>

              <div v-if="stage.parameters">
                <!-- Se só tiver 1 parâmetro -->
                <div v-if="Object.keys(stage.parameters).length === 1">
                  <div
                    v-for="(value, key) in stage.parameters"
                    :key="key"
                    class="q-ml-md q-mt-sm"
                  >
                    <p class="text-bold q-mb-xs">Tamanho:</p>
                    <p class="q-mb-md">{{ value }}</p>
                  </div>
                </div>

                <!-- Se tiver mais de 1 parâmetro, trata personalizado -->
                <div v-else class="q-ml-md q-mt-sm">
                  <p v-if="stage.parameters.maximum_horizontal_reach">Alcance máximo horizontal (m): {{ stage.parameters.maximum_horizontal_reach }}</p>
                  <p v-if="stage.parameters.maximum_nominal_lifting_capacity">Capacidade máxima nomimal de elevação (Kg / tn): {{ stage.parameters.maximum_nominal_lifting_capacity }}</p>
                  <p v-if="stage.parameters.maximum_nominal_vertical_reach_capacity">Capacidade máxima nomimal de alcance vertical (m): {{ stage.parameters.maximum_nominal_vertical_reach_capacity }}</p>
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
        <q-list
          bordered
          separator
          style="border-color: secondary; border-radius: 5px"
          class="text-primary"
        >
          <q-item>
            <q-item-section>
              <q-item-label>
                <q-skeleton type="text" />
              </q-item-label>
            </q-item-section>
          </q-item>
        </q-list>
      </div>
  
      <div v-else class="q-pa-md" style="min-width: 300px">
        <q-list
          bordered
          separator
          style="border-color: secondary; border-radius: 5px"
          class="text-primary"
        >
          <q-expansion-item
            label="Endereço do Projeto"
            header-class="text-primary text-h6"
          >
            <p class="text-weight-light q-ml-md">
              <strong class="text-bold">Cep:</strong> {{ zip_code }}
            </p>
            <p class="text-weight-light q-ml-md">
              <strong class="text-bold">Rua:</strong> {{ street }}
            </p>
            <p class="text-weight-light q-ml-md">
              <strong class="text-bold">Número:</strong> {{ number_street }}
            </p>
            <p class="text-weight-light q-ml-md">
              <strong class="text-bold">Complemento:</strong> {{ complement }}
            </p>
            <p class="text-weight-light q-ml-md">
              <strong class="text-bold">Bairro:</strong> {{ neighborhood }}
            </p>
            <p class="text-weight-light q-ml-md">
              <strong class="text-bold">Cidade:</strong> {{ city_name }}
            </p>
            <p class="text-weight-light q-ml-md">
              <strong class="text-bold">Estado:</strong> {{ state_name }}
            </p>
          </q-expansion-item>
        </q-list>
      </div>
      <!-- Novo Orçamento -->
      <div v-if="loading" class="q-pa-md" style="min-width: 300px">
        <q-list
          bordered
          separator
          style="border-color: secondary; border-radius: 5px"
          class="text-primary"
        >
          <q-item>
            <q-item-section>
              <q-item-label>
                <q-skeleton type="text" />
              </q-item-label>
            </q-item-section>
          </q-item>
        </q-list>
      </div>
  
      <div v-else class="q-pa-md" style="min-width: 300px">
        <q-list
          bordered
          separator
          style="border-color: secondary; border-radius: 5px"
          class="text-primary"
        >
          <q-expansion-item
            v-for="(machine, index) in budget_machine"
            :key="index"
            :label="`${index + 1} - ${machine.category_name || ''} - ${
              machine.model_name || ''
            }`"
            header-class="text-primary text-h6"
          >
            <q-form @submit="on_submit" class="q-pa-md">
              <q-input
                class="q-mr-md q-mb-md"
                dense
                v-model="machine.brand"
                filled
                label="Marca"
                readonly
                style="max-width: 300px"
              />
              <q-input
                v-if="machine.parameters.excavator_size"
                class="q-mr-md q-mb-md"
                v-model="machine.parameters.excavator_size"
                filled
                label="Parametro"
                autogrow
                style="max-width: 600px"
              />
              <q-input
                v-if="machine.parameters.backhoe_type"
                class="q-mr-md q-mb-md"
                v-model="machine.parameters.backhoe_type"
                filled
                label="Parametro"
                autogrow
                style="max-width: 600px"
              />
              <q-input
                v-if="machine.parameters.loader_size"
                class="q-mr-md q-mb-md"
                v-model="machine.parameters.loader_size"
                filled
                label="Parametro"
                autogrow
                style="max-width: 600px"
              />
                <!-- PARAMETROS PARA ELEVAÇÃO -->
                <div v-if="machine.jib !== null && machine.jib !== undefined">
  
                <p class="text-bold text-h6">Parametros de Elevação</p>
                <q-input
                v-if="machine.jib"
                class="q-mr-md q-mb-md"
                v-model="machine.jib"
                filled
                label="JIB (m)"
                style="max-width: 600px"
                readonly
              />
              <q-input
                v-if="machine.max_weight"
                class="q-mr-md q-mb-md"
                v-model="machine.max_weight"
                filled
                label="Peso Máximo (kg / tn)"
                style="max-width: 600px"
                readonly
              />
              <q-input
                v-if="machine.max_height"
                class="q-mr-md q-mb-md"
                v-model="machine.max_height"
                filled
                label="Altura Máxima (m)"
                style="max-width: 600px"
                readonly
              />
              <q-input
                v-if="machine.max_radius"
                class="q-mr-md q-mb-md"
                v-model="machine.max_radius"
                filled
                label="Raio Máximo (m)"
                style="max-width: 600px"
                readonly
              />
              <q-input
                v-if="machine.parameters.maximum_nominal_lifting_capacity"
                class="q-mr-md q-mb-md"
                v-model="machine.parameters.maximum_nominal_lifting_capacity"
                filled
                label="Capacidade máxima nomimal de elevação (Kg / tn)"
                style="max-width: 600px"
                readonly
              />
              <q-input
                v-if="machine.parameters.maximum_nominal_vertical_reach_capacity"
                class="q-mr-md q-mb-md"
                v-model="machine.parameters.maximum_nominal_vertical_reach_capacity"
                filled
                label="Capacidade máxima nomimal de alcance vertical (m)"
                style="max-width: 600px"
                readonly
              />
              <q-input
                v-if="machine.parameters.maximum_reach_without_jib"
                class="q-mr-md q-mb-md"
                v-model="machine.parameters.maximum_reach_without_jib"
                filled
                label="Alcance máximo sem JIB (m)"
                style="max-width: 600px"
                readonly
              />
              <q-input
                v-if="machine.parameters.maximum_vertical_reach"
                class="q-mr-md q-mb-md"
                v-model="machine.parameters.maximum_vertical_reach"
                filled
                label="Alcance máximo vertical (m)"
                style="max-width: 600px"
                readonly
              />
              <q-input
                v-if="machine.parameters.maximum_horizontal_reach"
                class="q-mr-md q-mb-md"
                v-model="machine.parameters.maximum_horizontal_reach"
                filled
                label="Alcance máximo horizontal (m)"
                style="max-width: 600px"
                readonly
              />
              </div>
              <!-- PARAMETROS crane_truck_data -->
              <div v-if="machine.crane_truck_data && machine.crane_truck_data.brand_truck !== null && machine.crane_truck_data.brand_truck !== undefined">
               <p class="text-bold text-h6">Caminhão / Chassis</p>
              <q-input
                v-if="machine.crane_truck_data.brand_truck"
                class="q-mr-md q-mb-md"
                v-model="machine.crane_truck_data.brand_truck"
                filled
                label="Marca"
                style="max-width: 600px"
              />
              </div>
              <!-- PARAMETROS trailer_data -->
              <div v-if="machine.trailer_data && machine.trailer_data.brand_trailer !== null && machine.trailer_data.brand_trailer !== undefined">
              <p class="text-bold text-h6" >Reboque / Carreta</p>
              <q-input
                v-if="machine.trailer_data.brand_trailer"
                class="q-mr-md q-mb-md"
                v-model="machine.trailer_data.brand_trailer"
                filled
                label="Marca"
                style="max-width: 600px"
              />
              </div>
              <div v-if="machine.price != '0,00'">
                <q-checkbox
                  class="text-bold"
                  :model-value="Fixo"
                  true-value="Fixo"
                  false-value=""
                  @update:model-value="
                    (val) => {
                      if (Fixo === 'Fixo') return; // trava se já estiver marcado
                      Fixo = val;
                    }
                  "
                >
                  FIXO
                </q-checkbox>
                <q-input
                  v-model="machine.price"
                  filled
                  label="Valor hora"
                  style="max-width: 300px"
                  readonly
                />
              </div>
              <div v-if="machine.price_per_hour != '0,00'">
                <q-checkbox
                  class="text-bold"
                  :model-value="Franquia"
                  true-value="Franquia"
                  false-value=""
                  @update:model-value="
                    (val) => {
                      if (Franquia === 'Franquia') return; // trava se já estiver marcado
                      Franquia = val;
                    }
                  "
                >
                  FRANQUIA
                </q-checkbox>
                <p class="text-primary text-bold">Franquia mímina de 10 horas</p>
                <q-input
                  v-model="machine.price_per_hour"
                  filled
                  label="Valor Hora"
                  style="max-width: 300px"
                  class="q-mt-md q-mb-mt"
                  readonly
                />
                <q-input
                  v-model="machine.price_per_distance"
                  filled
                  label="Valor por km"
                  style="max-width: 300px"
                  class="q-mt-md q-mb-mt"
                  readonly
                />
              </div>
  
              <div v-if="need_operator === 1">
                <div class="row">
                  <q-checkbox
                    class="text-bold q-mt-md"
                    v-model="Colaborador"
                    disable
                    true-value="Colaborador"
                    >OPERADOR
                  </q-checkbox>
                  <!-- <div>
              <q-list>
              <p>{{ machine.lista_operadores }}</p>
              </q-list>
            </div> -->
                </div>
              </div>
            </q-form>
          </q-expansion-item>
        </q-list>
      </div>
      <!-- Documentaçao / Condiçao  -->
      <div v-if="loading" class="q-pa-md" style="min-width: 300px">
        <q-list
          bordered
          separator
          style="border-color: secondary; border-radius: 5px"
          class="text-primary"
        >
          <q-item>
            <q-item-section>
              <q-item-label>
                <q-skeleton type="text" />
              </q-item-label>
            </q-item-section>
          </q-item>
        </q-list>
      </div>
  
      <div v-else class="q-pa-md" style="min-width: 300px">
        <q-list
          bordered
          separator
          style="border-color: secondary; border-radius: 5px"
          class="text-primary"
        >
          <q-expansion-item
            label="Licenças, Taxas e Outros"
            header-class="text-primary text-h6"
          >
            <div
              v-for="(item, index) in documentItems"
              :key="index"
              class="q-pa-md"
            >
              <p class="text-primary text-bold q-mb-sm">{{ item.charge_name }}</p>
              <div class="row q-col-gutter-md">
                <div class="col-12 col-md-4">
                  <q-input
                    v-model="item.fee_amount"
                    readonly
                    filled
                    label="Valor taxas"
                    
                  />
                </div>
                <div v-if="item.observation" class="col-12 col-md-8">
                  <q-input
                    v-model="item.observation"
                    readonly
                    filled
                    label="Observações"
                  />
                </div>
              </div>
            </div>
  
            <div class="q-pa-md">
              <p class="text-bold q-mb-xs">CONDIÇÃO:</p>
              <p class="q-mb-md">{{ condition2 }}</p>
  
              <p class="text-bold q-mb-xs">DESCRIÇÃO:</p>
              <p class="q-mb-md">{{ condition2 }}</p>
            </div>
  
            <div class="q-pa-md">
              <div class="row q-col-gutter-md">
                <div class="col-12 col-md-4">
                  <q-input
                    v-model="total_fee"
                    filled
                    label="Valor Total das Taxas e Licenças"
                    readonly
                  />
                </div>
                <div class="col-12 col-md-4">
                  <q-input
                    v-model="minimum_rental_price"
                    filled
                    label="Valor da Franquia Mínima"
                    readonly
                  />
                </div>
                <div class="col-12 col-md-4">
                  <q-input
                    v-model="amountTotal"
                    filled
                    label="Valor Total"
                    readonly
                  />
                </div>
              </div>
            </div>
          </q-expansion-item>
        </q-list>
      </div>
      <div class="q-pa-md" style="min-width: 300px;">
            <q-list bordered separator style="border-color: secondary;" class="text-primary">
                <q-item>
                    <q-item-section>
                        <q-item-label overline class="text-primary"><strong>CANCELAMENTO</strong></q-item-label>
                        <q-item-label caption class="text-primary">O cancelamento pode ser feito no máximo de 24 horas antes do serviço.
                        </q-item-label>
                        <q-btn  label="DESEJA CANCELAR?" color="red" class="q-ma-sm"
                        @click="confirmSubmission" />
                    </q-item-section>
                </q-item>
            </q-list>
          </div>
             <!-- Caixa de Confirmação -->
            <q-dialog v-model="showConfirmation">
              <q-card>
                <q-card-section>
                  <div class="text-h6">Deseja cancelar o agendamento?</div>
                </q-card-section>
                <q-card-actions>
                  <q-btn flat label="Sim" color="primary" @click="submitProposal" />
                  <q-btn
                    flat
                    label="Não"
                    color="secondary"
                    @click="showConfirmation = false"
                  />
                </q-card-actions>
              </q-card>
            </q-dialog>
    </q-page>
  </template>
  
  <script>
  import { ref } from 'vue';
  
  export default {
    name: 'ProjectBudget',
    data() {
      return {
        showConfirmation: false, 
        loading: true,
        condition: '', // Descrição do orçamento
        company_operators: [],
        company_name: '',
        client_name: '',
        user_name: '',
        created_at: '',
        email: '',
        phone: '',
        name_machine_category: '',
        expected_date: '',
        project_name: '',
        project_description: '',
        project_uuid: '',
        need_operator: '',
        fixed_price: '',
        uuid: '',

        minimum_rental_price:'',
        total_fee:'',


        machine_name: '',
        lista_operadores: '',
        modelMultiple: ref(),
        Fixo: ref('Fixo'),
        Franquia: ref('Franquia'),
        Colaborador: ref('Colaborador'),
        condition2: '',
        zip_code: '',
        street: '',
        number_street: '',
        complement: '',
        neighborhood: '',
        city_name: '',
        state_name: '',
        charge_name: '',
        service_charge_company: [],
        amount: '',
        project_stages: [],
        budget_proposals: [],
        company_uuid: '',
        budget_machine: [],
        brand: '',
        price: '',
        price_per_hour: '',
        price_per_distance: '',
        description: '',
        status_name: '',
        budget_proposal_uuid: '',
        amountTotal: '',
      };
    },
  
    async mounted() {
      this.company_uuid = localStorage.getItem('company_uuid');
      await this.get_budget_by_project_uuid();
      if (this.project_uuid) {
        await this.get_project_by_uuid();
      }
      this.loading = false;
    },
    methods: {
      confirmSubmission() {
      this.showConfirmation = true;
    },
    submitProposal() {
      this.showConfirmation = false;
      this.cancel_date();
    },
      async get_project_by_uuid() {
        await fetch(`http://localhost:5510/v1/project/${this.project_uuid}`, {
          headers: { token: localStorage.getItem('access_token') },
        })
          .then((response) => {
            if (!response.ok) {
              throw new Error('Network response was not ok');
            }
            return response.json();
          })
          .then((data) => {
            this.user_name = data.project.user_name;
            this.created_at = data.project.created_at;
            this.email = data.project.email;
            this.phone = data.project.phone;
            this.project_name = data.project.project_name;
            this.project_description = data.project.project_description;
            this.name_machine_category = data.project_stages.map(
              (stage) => stage.name_machine_category
            );
            this.project_stages = data.project_stages;
            
            this.user_name = data.project.user_name;

            this.zip_code = data.project.zip_code;
            this.street = data.project.street;
            this.number_street = data.project.number_street;
            this.complement = data.project.complement;
            this.neighborhood = data.project.neighborhood;
            this.city_name = data.project.city_name;
            this.state_name = data.project.state_name;
            this.need_operator = data.project_stages[0].need_operator;
            this.fixed_price = data.project.fixed_price;
          })
          .catch((error) => {
            console.error('Error fetching data:', error);
            this.$q.notify({
              color: 'red-5',
              textColor: 'white',
              icon: 'cloud_done',
              message: 'Nenhum orçamento cadastrado!',
            });
          });
      },
      async get_budget_by_project_uuid() {
        await fetch(
          `http://localhost:5510/v1/budget/proposal/details/${this.$route.params.budget_proposal_uuid}`,
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
            this.project_name = data.budget.project_name;
            this.client_name = data.budget.client_name;
            this.created_at = data.budget.created_at;
            this.status_name = data.budget.status_name;
            this.project_uuid = data.budget.project_uuid;
            this.uuid = data.budget.budget_uuid;
  
            this.documentItems = data.budget_service_charge || [];
            this.minimum_rental_price = data.minimum_rental_price;
            this.company_name = data.budget_company_data.name;
            this.expected_date= data.budget.expected_date;
            this.total_fee = data.budget_machine[0].total_fee;
            // Novo array para armazenar todas as máquinas
            this.budget_machine = [];
  
            // Itera sobre cada proposta
            data.budget_machine.forEach((proposal) => {
              // Adiciona informações da proposta em cada máquina
  
              this.amountTotal = data.budget_machine[0].amount;
              this.condition2 = data.budget_machine[0].condition;
              proposal.machines.forEach((machine) => {
                machine.proposal_observation = proposal.proposal_observation;
                machine.amount = proposal.amount;
                machine.condition = proposal.condition;
                machine.expected_date = proposal.expected_date;
                machine.lista_operadores = machine.employees || [];
                machine.modelMultiple = [];
  
                this.budget_proposal_uuid =
                  data.budget_machine[0].budget_proposal_uuid;
  
                this.budget_machine.push(machine);
              });
            });
            if (this.project_uuid) {
              this.get_project_by_uuid();
            }
          })
          .catch((error) => {
            console.error('Error fetching data:', error);
          });
      },
      async cancel_date() {
      try {
        const response = await fetch(
          `http://localhost:5510/v1/budget/proposal/cancel/${this.$route.params.budget_proposal_uuid}`,
          {
            method: 'DELETE',
            headers: {
              token: localStorage.getItem('access_token'),
            },
          }
        );
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        this.$q.notify({
          color: 'green-5',
          textColor: 'white',
          icon: 'check',
          message: 'Projeto cancelado com sucesso.',
        });
        this.$router.push(`/user/service`);
      } catch (error) {
        console.error('Error Delete project:', error);
        this.$q.notify({
          color: 'red-5',
          textColor: 'white',
          icon: 'error',
          message: 'Erro ao cancelar o Projeto.',
        });
      }
    },
    },
  };
  </script>
  