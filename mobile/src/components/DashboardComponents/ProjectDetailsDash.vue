<template>
  <q-page :class="['bg-white', { 'overflow-hidden': loading }]">
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
      <div v-if="loading" class="q-ml-md">
        <q-skeleton type="text" width="220px" height="1.5rem" />
      </div>
      <div v-else class="text-h4 text-primary text-bold q-ml-md ">
        Detalhes do projeto
      </div>
    </div>
    <div class="flex justify-end" style="margin-right: 10px; margin-bottom: 10px">
      <q-btn label="DESEJA CANCELAR" color="red" @click="confirmSubmissionCancel" style="margin-right: 5px"/>
      <q-btn color="green" label="Agendamento" :to="`/dashboard/project/details/date/${this.$route.params.budget_uuid}`" style="margin-right: 5px" />
      <q-btn color="orange" icon-right="picture_as_pdf" label="Exportar orçamento" @click="getBudgetPDF()"/>

      <!-- Caixa de Confirmação -->
      <q-dialog v-model="showConfirmationCancel">
        <q-card>
          <q-card-section>
            <div class="text-h6">Deseja cancelar?</div>
          </q-card-section>
          <q-card-actions>
            <q-btn flat label="Sim" color="primary" @click="submitProposalCancel" />
            <q-btn
              flat
              label="Não"
              color="secondary"
              @click="showConfirmationCancel = false"
            />
          </q-card-actions>
        </q-card>
      </q-dialog>

    </div>
    <q-separator class="bg-secondary" />
    <div v-if="status_name === 'ACEITO' && !loading">
      <p class="text-primary text-bold text-h5 text-center q-mt-md text-red">AGENDAMENTO SOLICITADO PARA DIA {{ expected_date }}</p>
    </div>
    <!-- Informações do Projeto -->
    <div v-if="loading" class="q-pa-md" style="min-width: 300px">
      <q-list bordered separator style="border-color: secondary; border-radius: 5px" class="text-primary">
        <q-item><q-item-section><q-skeleton type="text" width="200px" /></q-item-section></q-item>
        <q-item><q-item-section><q-skeleton type="text" width="250px" /></q-item-section></q-item>
        <q-item><q-item-section><q-skeleton type="text" width="150px" /></q-item-section></q-item>
        <q-item><q-item-section><q-skeleton type="text" width="200px" /></q-item-section></q-item>
        <q-item><q-item-section><q-skeleton type="text" width="250px" /></q-item-section></q-item>
        <q-item><q-item-section><q-skeleton type="text" width="150px" /></q-item-section></q-item>
        <q-item><q-item-section><q-skeleton type="text" width="200px" /></q-item-section></q-item>
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
          <q-item-section style="display: flex; gap: 0.5rem">
            <q-item-label class="text-weight-light">
              <strong class="text-bold">Data da criação:</strong>
              {{ created_at }}
            </q-item-label>
          </q-item-section>
        </q-item>
        <q-item>
          <q-item-section style="display: flex; gap: 0.5rem">
            <q-item-label class="text-weight-light">
              <strong class="text-bold">Data de Início:</strong>
              {{ expected_date }} {{expected_hour}}
            </q-item-label>
          </q-item-section>
          <q-item-section style="display: flex; gap: 0.5rem">
            <q-item-label class="text-weight-light">
              <strong class="text-bold">Data do Fim:</strong>
              {{ expected_date_end }} {{expected_hour_end}}
            </q-item-label>
          </q-item-section>
        </q-item>
        <q-item>
          <q-item-section style="display: flex; gap: 0.5rem">
            <q-item-label class="text-weight-light">
              <strong class="text-bold">Nome do projeto:</strong>
              {{ project_name }}
            </q-item-label>
          </q-item-section>
        </q-item>
        <q-item>
          <q-item-section style="display: flex; gap: 0.5rem">
            <q-item-label class="text-weight-light">
              <strong class="text-bold">Descrição:</strong>
              {{ project_description }}
            </q-item-label>
          </q-item-section>
        </q-item>
        <q-item>
          <q-item-section style="display: flex; gap: 0.5rem">
            <q-item-label class="text-weight-light">
              <strong class="text-bold">Nome do cliente:</strong>
              {{ client_name  || client_company_name}}
            </q-item-label>
          </q-item-section>
        </q-item>
        <q-item>
          <q-item-section style="display: flex; gap: 0.5rem">
            <q-item-label class="text-weight-light">
              <strong class="text-bold">Email:</strong>
              {{ email || client_email}}
            </q-item-label>
          </q-item-section>
        </q-item>
        <q-item>
          <q-item-section style="display: flex; gap: 0.5rem">
            <q-item-label class="text-weight-light">
              <strong class="text-bold">Número:</strong>
              {{ phone || client_phone}}
            </q-item-label>
          </q-item-section>
        </q-item>
        <q-item>
          <q-item-section>
            <q-item-label class="text-weight-light" style="display: flex; gap: 0.5rem">
              <strong class="text-bold">Categoria:</strong>
              <span v-for="(category, index) in name_machine_category" :key="index">
                {{ category
                }}<span v-if="index < name_machine_category.length - 1">,</span>
              </span>
            </q-item-label>
          </q-item-section>
        </q-item>
        <q-item v-if="payer_name != null ">
          <q-item-section style="display: flex; gap: 0.5rem">
            <q-item-label class="text-weight-light"><strong class="text-bold">Pagador:</strong>
              {{ payer_name }}
              <p> <strong class="text-bold">CNPJ/CPF:</strong> {{cnpj}} </p>
            </q-item-label>
          </q-item-section>
        </q-item>
      </q-list>
    </div>
    <!-- Informações Detalhadas -->
    <div v-if="loading" class="q-pa-md" style="min-width: 300px">
      <q-list bordered separator style="border-color: secondary; border-radius: 5px" class="text-primary">
        <q-item><q-item-section><q-item-label><q-skeleton type="text" width="250px" /></q-item-label></q-item-section></q-item>
      </q-list>
    </div>

    <div v-else class="q-pa-md" style="min-width: 300px">
      <q-list bordered separator style="border-color: secondary; border-radius: 5px" class="text-primary">
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
      <q-list bordered separator style="border-color: secondary; border-radius: 5px" class="text-primary">
        <q-item><q-item-section><q-item-label><q-skeleton type="text" width="150px" /></q-item-label></q-item-section></q-item>
      </q-list>
    </div>

    <div v-else class="q-pa-md" style="min-width: 300px">
      <q-list bordered separator style="border-color: secondary; border-radius: 5px" class="text-primary">
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
    <!-- Historico enviado -->
    <div v-if="loading" class="q-pa-md" style="min-width: 300px">
      <q-list bordered separator style="border-color: secondary; border-radius: 5px" class="text-primary">
        <q-item><q-item-section><q-item-label><q-skeleton type="text" width="200px" /></q-item-label></q-item-section></q-item>
      </q-list>
    </div>

    <div v-else class="q-pa-md" style="min-width: 300px">
      <q-list bordered separator style="border-color: secondary; border-radius: 8px" class="text-primary">
        <q-expansion-item label="Histórico" header-class="text-primary text-h6">
          <div class="q-pa-md">
            <p class="text-primary text-subtitle1 q-ml-md q-pa-sm">
              <strong>Status:</strong>
              <span class="text-red text-bold">{{ status_name }}</span>
            </p>

            <div
              v-for="(machine, index) in budget_machine"
              :key="index"
              class="q-pa-md q-ml-md q-mb-md bg-grey-2 q-rounded-borders shadow-1"
            >
              <div class="q-mb-sm">
                <strong>Nome da máquina:</strong> {{ machine.machine_name }}
              </div>
              <div><strong>Marca:</strong> {{ machine.brand }}</div>
              <div><strong>Categoria:</strong> {{ machine.category_name }}</div>
              <div>
                <strong>Operador:</strong>
                {{ machine.employees?.[0]?.operator_name || 'Sem operador' }}
              </div>
              <div><strong>Placa:</strong> {{ machine.license_plate }}</div>

              <div v-if="machine.parameters?.loader_size && machine.parameters.loader_size !== ''">
                <strong>Tamanho:</strong> {{ machine.parameters.loader_size }}
              </div>

              <div v-if="machine.parameters?.excavator_size && machine.parameters.excavator_size !== ''">
                <strong>Tamanho Escavadeira:</strong> {{ machine.parameters.excavator_size }}
              </div>

              <div v-if="machine.parameters?.backhoe_type && machine.parameters.backhoe_type !== ''">
                <strong>Tipo de Retroescavadeira:</strong>
                {{ machine.parameters.backhoe_type }}
              </div>

              <div v-if="machine.price !== '0,00'">
                <strong>Preço Fixo:</strong> R$ {{ machine.price }}
              </div>

              <div v-if="machine.price_per_hour !== '0,00'">
                <strong>Preço Franquia:</strong> R$ {{ machine.price_per_hour }}
              </div>
            </div>
          </div>
        </q-expansion-item>
      </q-list>
    </div>

    <!-- Novo Orçamento  -->
    <div v-if="loading" style="display: flex; align-items: center; justify-content: center">
      <q-skeleton type="text" width="220px" height="1.5rem" />
    </div>
    <div v-if="status_name === 'ATENDIMENTO' && !loading">
      <p class="text-primary text-bold text-h6 text-center">NOVO ORÇAMENTO</p>
    </div>
    <div v-if="status_name === 'ORÇAMENTAÇÃO' && !loading">
      <p class="text-primary text-bold text-h6 text-center">ORÇAMENTO JÁ ENVIADO</p>
    </div>
    <div v-if="status_name === 'ACEITO' && !loading">
      <p class="text-primary text-bold text-h6 text-center">ANEXAR COLABORADOR</p>
    </div>
    <div v-if="loading" class="q-pa-md" style="min-width: 300px">
      <q-list bordered separator style="border-color: secondary; border-radius: 5px" class="text-primary">
        <q-item><q-item-section><q-item-label><q-skeleton type="text" width="250px" /></q-item-label></q-item-section></q-item>
      </q-list>
    </div>

    <div v-else class="q-pa-md">
      <q-list bordered separator style="border-color: secondary" class="text-primary">
        <q-expansion-item
          v-for="(machine, index) in budget_machine"
          :key="index"
          :label="`Novo Orçamento ${index + 1} - ${machine.category_name || ''} - ${machine.model_name || ''}`"
          header-class="text-primary text-h6"
        >
          <q-btn
            dense
            class="q-ma-md"
            label="DELETAR MÁQUINA"
            icon="delete"
            color="red"
            @click.stop="delete_budget_machine(machine.budget_machine_uuid)"
          />
          <q-form @submit="on_submit" class="q-pa-md">
            <q-btn class="q-mb-md" label="ADCIONAR MÁQUINAS" :to="`/dashboard/project/details/create/${this.$route.params.budget_uuid}`" />
            <q-input class="q-mr-md q-mb-md" v-model="machine.brand" filled label="Marca" style="max-width: 300px" readonly />

            <!-- PARAMETROS PARA ESCAVAÇÃO -->
            <q-input v-if="machine.parameters.excavator_size" class="q-mr-md q-mb-md" v-model="machine.parameters.excavator_size" filled label="Parametro" style="max-width: 600px" readonly />
            <q-input v-if="machine.parameters.backhoe_type" class="q-mr-md q-mb-md" v-model="machine.parameters.backhoe_type" filled label="Parametro" style="max-width: 600px" readonly />
            <q-input v-if="machine.parameters.loader_size" class="q-mr-md q-mb-md" v-model="machine.parameters.loader_size" filled label="Parametro" style="max-width: 600px" readonly />

            <!-- PARAMETROS PARA ELEVAÇÃO -->
            <div v-if="machine.jib !== null && machine.jib !== undefined">
              <p class="text-bold text-h6">Parametros de Elevação</p>
              <q-input v-if="machine.jib" class="q-mr-md q-mb-md" v-model="machine.jib" filled label="JIB (m)" style="max-width: 600px" readonly />
              <q-input v-if="machine.max_weight" class="q-mr-md q-mb-md" v-model="machine.max_weight" filled label="Peso Máximo (kg / tn)" style="max-width: 600px" readonly />
              <q-input v-if="machine.max_height" class="q-mr-md q-mb-md" v-model="machine.max_height" filled label="Altura Máxima (m)" style="max-width: 600px" readonly />
              <q-input v-if="machine.max_radius" class="q-mr-md q-mb-md" v-model="machine.max_radius" filled label="Raio Máximo (m)" style="max-width: 600px" readonly />
              <q-input v-if="machine.parameters.maximum_nominal_lifting_capacity" class="q-mr-md q-mb-md" v-model="machine.parameters.maximum_nominal_lifting_capacity" filled label="Capacidade máxima nomimal de elevação (Kg / tn)" style="max-width: 600px" readonly />
              <q-input v-if="machine.parameters.maximum_nominal_vertical_reach_capacity" class="q-mr-md q-mb-md" v-model="machine.parameters.maximum_nominal_vertical_reach_capacity" filled label="Capacidade máxima nomimal de alcance vertical (m)" style="max-width: 600px" readonly />
              <q-input v-if="machine.parameters.maximum_reach_without_jib" class="q-mr-md q-mb-md" v-model="machine.parameters.maximum_reach_without_jib" filled label="Alcance máximo sem JIB (m)" style="max-width: 600px" readonly />
              <q-input v-if="machine.parameters.maximum_vertical_reach" class="q-mr-md q-mb-md" v-model="machine.parameters.maximum_vertical_reach" filled label="Alcance máximo vertical (m)" style="max-width: 600px" readonly />
              <q-input v-if="machine.parameters.maximum_horizontal_reach" class="q-mr-md q-mb-md" v-model="machine.parameters.maximum_horizontal_reach" filled label="Alcance máximo horizontal (m)" style="max-width: 600px" readonly />
            </div>

            <!-- PARAMETROS crane_truck_data -->
            <div v-if="machine.crane_truck_data && machine.crane_truck_data.brand_truck !== null && machine.crane_truck_data.brand_truck !== undefined">
              <p class="text-bold text-h6">Caminhão / Chassis</p>
              <q-input v-if="machine.crane_truck_data.brand_truck" class="q-mr-md q-mb-md" v-model="machine.crane_truck_data.brand_truck" filled label="Marca" style="max-width: 600px" />
            </div>

            <!-- PARAMETROS trailer_data -->
            <div v-if="machine.trailer_data && machine.trailer_data.brand_trailer !== null && machine.trailer_data.brand_trailer !== undefined">
              <p class="text-bold text-h6">Reboque / Carreta</p>
              <q-input v-if="machine.trailer_data.brand_trailer" class="q-mr-md q-mb-md" v-model="machine.trailer_data.brand_trailer" filled label="Marca" style="max-width: 600px" />
            </div>

            <div v-if="machine.price_per_hour != '0,00'">
              <q-checkbox
                class="text-bold"
                :model-value="Franquia"
                true-value="Franquia"
                false-value=""
                @update:model-value="val => { if (Franquia === 'Franquia') return; Franquia = val; }"
              >
                FRANQUIA
              </q-checkbox>

              <q-input
                v-model.number="machine.budget_machine_minimum_rental_period"
                type="number"
                filled
                label="Horas franquia mínima"
                style="max-width: 300px"
                class="q-mt-md q-mb-md"
                min="0"
                step="0.5"
                @update:model-value="calculateTotals"
              />

              <q-input
                v-model="machine.budget_machine_price_per_hour"
                filled
                label="Valor Hora Enviado"
                style="max-width: 300px"
                class="q-mt-md q-mb-md"
                :prefix="'R$ '"
              />

              <q-input
                v-model="machine.price_per_hour"
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
                label="Valor Distância Enviado (R$/km)"
                style="max-width: 300px"
                class="q-mt-md q-mb-md"
                :prefix="'R$ '"
                @update:model-value="calculateTotals"
              />

              <q-input
                v-model="machine.price_per_distance"
                filled
                label="Valor Distância (R$/km)"
                style="max-width: 300px"
                class="q-mt-md q-mb-md"
                readonly
                :prefix="'R$ '"
              />

              <!-- NOVO: custo de distância calculado para esta máquina -->
              <q-input
                :model-value="formatDistanceCost(machine)"
                filled
                readonly
                label="Custo de Distância (total_distance × R$/km)"
                :prefix="'R$ '"
                style="max-width: 320px"
                class="q-mt-md"
              />
            </div>

            <!-- PARA INSERIR UMA CATEGORIA  -->
            <div>
              <q-checkbox class="text-bold q-mt-md" v-model="Categoria" true-value="Categoria">OPERADOR </q-checkbox>
              <p class="text-bold text-h6">ANEXAR CATEGORIA A MÁQUINA</p>
              <q-btn color="red" class="q-mb-md" label="ANEXAR COLABORADOR A MÁQUINA" :to="`/dashboard/role/collaborator/create/${this.$route.params.budget_uuid}`" />
              <div class="row">
                <q-select
                  v-model="machine.modelMultiple"
                  :options="roles"
                  use-chips
                  stack-label
                  multiple
                  option-label="name"
                  option-value="uuid"
                  label="Categoria Principal"
                  class="q-mb-md"
                  style="min-width: 300px;"
                  outlined
                />
                <div>
                  <q-btn label="Salvar" color="primary" class="q-ml-md" @click="post_role_operator(machine)" />
                </div>
              </div>
              <div style="max-width: 400px">
                <q-list bordered separator>
                  <q-item v-for="(operador, index) in machine.lista_operadores" :key="index">
                    <q-item-section>
                      <q-item-label>
                        <strong>Categoria:</strong> {{ operador.role_name }}
                      </q-item-label>
                      <q-item-label v-if="operador.operator_name">
                        <strong>Operador:</strong> {{ operador.operator_name }}
                      </q-item-label>
                    </q-item-section>
                  </q-item>
                </q-list>
              </div>
            </div>
          </q-form>
        </q-expansion-item>
      </q-list>
    </div>

    <!-- Documentaçao / Condiçao -->
    <div v-if="loading" class="q-pa-md" style="min-width: 300px">
      <q-list bordered separator style="border-color: secondary; border-radius: 5px" class="text-primary">
        <q-item><q-item-section><q-item-label><q-skeleton type="text" width="150px" /></q-item-label></q-item-section></q-item>
      </q-list>
    </div>

    <div v-else class="q-pa-md" style="min-width: 300px">

      <q-list bordered separator style="border-color: secondary" class="text-primary">
        <div v-if="budget_charge.length" style="max-width: 400px" class="q-pa-md">
          <span class="text-black text-h5 q-pa-md">Taxas e licenças enviadas</span>
          <q-list bordered separator>
            <q-item v-for="(charge, index) in budget_charge" :key="index">
              <q-item-section>
                <q-item-label><strong>Nome da taxa:</strong> {{ charge.charge_name }}</q-item-label>
                <q-item-label><strong>Valor da taxa:</strong> R$ {{ charge.fee_amount }}</q-item-label>
                <q-item-label v-if="charge.observation"><strong>Observação:</strong> {{ charge.observation }}</q-item-label>
              </q-item-section>
            </q-item>
          </q-list>
        </div>

        <q-expansion-item
          label="Licenças, Taxas, Seguros e Outros"
          header-class="text-primary text-h6"
        >
          <div class="q-pa-md">
            <div class="q-mb-md">
              <q-btn rounded icon="add" color="secondary" @click="addDocumentItem" label="Adicionar campo" />
            </div>

            <div v-for="(item, index) in documentItems" :key="index" class="q-mb-md">
              <div class="row items-center q-col-gutter-md">
                <div class="col-12 col-md-2">
                  <q-select
                    v-model="item.selected"
                    :options="service_charge_company"
                    option-label="service_charge_name"
                    label="Tipo"
                    outlined
                    emit-value
                    map-options
                    class="q-mt-md"
                    popup-content-style="color: #000; font-weight: 400; scrollbar-width: thin; scrollbar-color: #303940 #ccc;"
                    @update:model-value="onDocumentChange(index)"
                  />
                </div>

                <div class="col-12 col-md-2" v-if="item.selected">
                  <q-input
                    v-model="item.selected.fee_amount"
                    filled
                    label="Valor Taxa"
                    :prefix="'R$ '"
                    @update:model-value="calculateTotals"
                  />
                </div>

                <div class="col-auto" v-if="documentItems.length > 1">
                  <q-btn icon="delete" color="red" round dense flat @click="removeDocumentItem(index)" />
                </div>
              </div>
            </div>

          </div>

          <!-- Textareas -->
          <div class="row q-pa-md q-col-gutter-md">
            <div class="col-12 col-md-6">
              <q-input v-model="condition" label="Condição" filled type="textarea" />
            </div>
            <div class="col-12 col-md-6">
              <q-input v-model="budget_observation" label="Observação Geral" filled type="textarea" />
            </div>
          </div>

          <!-- Totais -->
          <div class="row q-pa-md q-col-gutter-md">
            <!-- NOVO: distância total (km) -->
            <div class="col-12 col-md-4">
              <q-input
                v-model.number="total_distance"
                type="number"
                label="Distância total (km)"
                filled
                min="0"
                step="0.5"
                @update:model-value="calculateTotals"
              />
            </div>

            <div class="col-12 col-md-4">
              <q-input
                v-model="valortaxastotal"
                filled
                readonly
                :prefix="'R$ '"
                label="Valor Total das Taxas e Licenças"
              />
            </div>

            <div class="col-12 col-md-4">
              <q-input
                :model-value="distanceTotalCostFormatado"
                filled
                readonly
                :prefix="'R$ '"
                label="Custo Total de Distância"
              />
            </div>

            <div class="col-12 col-md-4">
              <q-input
                v-for="(machine, index) in budget_machine"
                :key="index"
                :model-value="formatFranquiaMinima(machine)"
                filled
                readonly
                :label="`Valor franquia mínima: ${machine.machine_name}`"
                :prefix="'R$ '"
                class="q-mb-sm"
              />
            </div>

            <div class="col-12 col-md-4">
              <q-input
                filled
                readonly
                label="Valor Total"
                :model-value="ValorTotalFormatado"
                :prefix="'R$ '"
              />
            </div>
          </div>

          <!-- Botão de envio -->
          <div class="q-pa-md flex justify-center items-center">
            <q-btn
              color="primary"
              unelevated
              size="lg"
              class="q-px-lg q-py-sm text-white"
              @click="confirmSubmission"
            >
              <div class="row items-center no-wrap">
                <span class="text-subtitle1 q-mr-sm">Enviar</span>
                <q-icon name="arrow_forward" size="22px" />
              </div>
            </q-btn>
          </div>

        </q-expansion-item>
      </q-list>
    </div>
    <!-- Caixa de Confirmação -->
    <q-dialog v-model="showConfirmation">
      <q-card>
        <q-card-section>
          <div class="text-h6">Deseja enviar o orçamento?</div>
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
      expected_hour_end:'',
      budget_machine_minimum_rental_period:'',
      budget_machine_price_per_hour:'',
      budget_machine_price_per_distance:'',
      showConfirmation: false,
      expected_date_end:'',
      expected_hour:'',
      machine_operator_uuid:'',
      roles: [],
      loading: true,
      payer_name:'',
      cnpj:'',
      condition:
        '● A CONTRATANTE ficará responsável pela liberação da entrada do veículo e do motorista na área\n' +
        'onde será realizado o serviço, sendo providenciada a integração e qualquer outro tipo de liberação que\n' +
        'for solicitada;\n' +
        '● Os tempos demandados para realização de integração, check list e demais atividades assemelhadas, serão\n' +
        'consideradas horas a disposição e cobradas em medição;\n' +
        '● Na impossibilidade de execução do serviço com o equipamento acima tratado por qualquer motivo não\n' +
        'imputável à contratada, não haverá alterações nos valores cobrados e condições de pagamento descritas\n' +
        'nesta proposta de serviços, ficando acordado que a contratante deverá providenciar as licenças e\n' +
        'autorizações necessárias para a execução do serviço;\n' +
        '● A contagem mínima das horas considerando o valor de horas normais, são entre 7:00h às 18:00h horas\n' +
        'de segunda a sexta, trabalhadas ou à disposição;\n' +
        '● As horas trabalhadas e ou disposição da contratada além do horário diário normal de trabalho (antes\n' +
        'das 7h e após às 18h, finais de semana e feriados), serão consideradas Horas Especiais e serão\n' +
        'medidas em separado das horas normais e cobradas com adicional de 30%;\n' +
        '● Período de integração ou treinamento na obra contará como hora trabalhada.',
      company_name: '',
      observation:'',
      budget_observation:'',
      client_name: '',
      client_email:'',
      client_phone:'',
      client_company_name:'',
      showConfirmationCancel: false,
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
      minimum_rental_price:0,
      machine_name: '',
      lista_operadores: '',
      modelMultiple: ref(),
      Fixo: ref('Fixo'),
      Franquia: ref('Franquia'),
      Colaborador: ref('Colaborador'),
      Categoria: ref('Categoria'),

      zip_code: '',
      street: '',
      number_street: '',
      complement: '',
      neighborhood: '',
      city_name: '',
      state_name: '',

      documentItems: [{ selected: null }],
      service_charge_company: [],
      ValorTotal: 0,
      total_fee:'',
      valortaxastotal: 0,
      project_stages: [],
      budget_proposals: [],
      company_uuid: '',
      budget_machine: [],
      budget_charge:[],
      price: '',
      price_per_hour: '',
      price_per_distance: '',
      brand: '',
      // parametros elevaçao
      jib:'',
      articulated_load_capacity:'',
      articulated_maximum_lifting_height: '',
      compactor_type:'',
      crawler_tractor_size:'',
      crawler_tractor_size_type:'',
      lifting_load_capacity:'',
      lifting_maximum_lifting_height:'',
      maximum_horizontal_reach:'',
      maximum_nominal_lifting_capacity:'',
      maximum_nominal_vertical_reach_capacity:'',
      maximum_reach_without_jib:'',
      maximum_vertical_reach:'',
      //trailer_data
      brand_trailer:'',
      model_trailer:'',
      //crane_truck_data
      brand_truck:'',
      model_truck:'',
      // parametros escavação
      excavator_size: '',
      loader_size: '',
      description: '',
      status_name: '',

      // >>> NOVO: distância total e custo total de distância
      total_distance: 0, // km
      distanceTotalCost: 0, // acumulado (todas máquinas)
    };
  },
  async mounted() {
    this.company_uuid = localStorage.getItem('company_uuid');

    await Promise.all([
      this.get_budget_by_project_uuid(),
      this.get_service_charge(),
      this.get_roles()
    ]);

    this.loading = false;
  },
  computed: {
    ValorTotalFormatado() {
      return this.ValorTotal.toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      })
    },
    distanceTotalCostFormatado() {
      return this.distanceTotalCost.toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      })
    }
  },
  watch: {
    budget_machine_price_per_hour() {
      this.calculateTotals()
    },
    budget_machine: {
      deep: true,
      handler() {
        this.calculateTotals()
      }
    },
    // >>> NOVO: recalcular quando distância mudar
    total_distance() {
      this.calculateTotals()
    }
  },

  methods: {
    async get_roles() {

      await fetch(
        `https://fortis-api.55technology.com/v1/role/company/${this.company_uuid}`,
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
          this.roles = data.roles || []
        })
        .catch((error) => {
          console.error('Error fetching data:', error);
          this.$q.notify({
            color: 'red-5',
            textColor: 'white',
            icon: 'cloud_done',
            message: 'Nenhuma categoria encontrada!',
          });
        });
    },
    addDocumentItem() {
      this.documentItems.push({ selected: null });
    },
    removeDocumentItem(index) {
      this.documentItems.splice(index, 1);
      this.calculateTotals();
    },
    onDocumentChange() {
      this.calculateTotals();
    },
    formatFranquiaMinima(machine) {
      const valorHora = this.parseMoney(machine.budget_machine_price_per_hour || 0)
      const horas = this.parseHours(machine.budget_machine_minimum_rental_period, 10) // 10 como fallback
      const total = valorHora * horas
      return total.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
    },
    parseMoney(value) {
      if (typeof value === 'string') {
        // remove pontos de milhar e troca vírgula decimal por ponto
        value = value.replace(/\./g, '').replace(',', '.')
      }
      const n = parseFloat(value)
      return Number.isFinite(n) ? n : 0
    },
    parseHours(value, fallback = 10) {
      // aceita número ou string; se vier vazio/NaN usa fallback
      const n = typeof value === 'string'
        ? parseFloat(value.replace(',', '.'))
        : Number(value)
      return Number.isFinite(n) && n >= 0 ? n : fallback
    },
    // >>> NOVO: helpers de distância
    parseDistance(value) {
      if (value === null || value === undefined || value === '') return 0
      if (typeof value === 'string') value = value.replace(',', '.')
      const n = parseFloat(value)
      return Number.isFinite(n) && n >= 0 ? n : 0
    },
    distanceCostForMachine(machine) {
      const km = this.parseDistance(this.total_distance)
      const pricePerKm = this.parseMoney(machine.budget_machine_price_per_distance || 0)
      return km * pricePerKm
    },
    formatDistanceCost(machine) {
      return this.distanceCostForMachine(machine)
        .toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
    },

    calculateTotals() {
      // total das taxas adicionadas na UI
      const totalTaxasSelect = this.documentItems.reduce((acc, item) => {
        const fee = this.parseMoney(item.selected?.fee_amount || 0)
        return acc + fee
      }, 0)

      // total das taxas vindas do GET
      const totalTaxasGet = this.budget_charge.reduce((acc, charge) => {
        const fee = this.parseMoney(charge.fee_amount || 0)
        return acc + fee
      }, 0)

      this.valortaxastotal = totalTaxasSelect + totalTaxasGet

      // Horas * valor hora (franquia mínima) por máquina
      const totalPricePerHour = this.budget_machine.reduce((acc, machine) => {
        const valorHora = this.parseMoney(machine.budget_machine_price_per_hour || 0)
        const horas = this.parseHours(machine.budget_machine_minimum_rental_period, 10)
        return acc + (valorHora * horas)
      }, 0)

      // >>> NOVO: custo de distância por máquina somado
      const totalDistanceCost = this.budget_machine.reduce((acc, machine) => {
        return acc + this.distanceCostForMachine(machine)
      }, 0)

      this.distanceTotalCost = totalDistanceCost
      this.ValorTotal = this.valortaxastotal + totalPricePerHour + totalDistanceCost
    },

    async get_project_by_uuid() {
      fetch(`https://fortis-api.55technology.com/v1/project/${this.project_uuid}`, {
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
          this.client_email = data.project.client_email;
          this.client_phone = data.project.client_phone;
          this.project_name = data.project.project_name;
          this.project_description = data.project.project_description;
          this.name_machine_category = data.project_stages.map(
            (stage) => stage.name_machine_category
          );
          this.project_stages = data.project_stages;

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
    async get_service_charge() {
      await fetch(
        `https://fortis-api.55technology.com/v1/service/charge/company/${this.company_uuid}`,
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
          this.service_charge_company = data.service_charge_company;
        })
        .catch((error) => {
          console.error('Error fetching data:', error);
          this.$q.notify({
            color: 'red-5',
            textColor: 'white',
            icon: 'cloud_done',
            message: 'Nenhum Documento cadastrado!',
          });
        });
    },
    async get_budget_by_project_uuid() {
      await fetch(
        `https://fortis-api.55technology.com/v1/budget/${this.$route.params.budget_uuid}`,
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
          this.client_company_name = data.budget.client_company_name;
          this.client_name = data.budget.client_name;
          this.created_at = data.budget.created_at;
          this.status_name = data.budget.status_name;
          this.project_uuid = data.budget.project_uuid;
          this.uuid = this.$route.params.budget_uuid;
          this.expected_hour_end = data.budget.expected_hour_end;
          this.payer_name = data.budget.payer_name;
          this.cnpj = data.budget.cnpj;
          this.expected_date = data.budget.expected_date;
          this.expected_hour = data.budget.expected_hour;
          this.expected_date_end = data.budget.expected_date_end;
          this.minimum_rental_price = data.minimum_rental_price;

          // >>> NOVO: se backend já envia, inicializa distância total
          this.total_distance = this.parseDistance(data.budget?.total_distance ?? 0);

          // Novo array para armazenar todas as máquinas
          this.budget_machine = [];
          this.budget_charge = [];
          this.budget_observation = data.budget.budget_observation;

          // Itera sobre cada proposta
          data.budget_machine.forEach((machine) => {
            // Adiciona informações da proposta em cada máquina
            machine.proposal_observation = machine.proposal_observation;
            machine.amount = machine.amount;
            machine.condition = machine.condition;
            machine.machine_operator_uuid = machine.machine_operator_uuid;

            // valores que podem ser diferentes do padrão
            machine.budget_machine_price_per_hour = machine.budget_machine_price_per_hour;
            machine.budget_machine_price_per_distance = machine.budget_machine_price_per_distance;

            machine.expected_date = machine.expected_date;
            machine.role_name = machine.role_name;
            machine.lista_operadores = machine.employees || [];
            machine.modelMultiple = [];
            machine.budget_machine_uuid = machine.budget_machine_uuid;
            machine.budget_machine_minimum_rental_period = machine.budget_machine_minimum_rental_period;

            // Monta lista de operadores
            machine.lista_operadores = (machine.employees || []).map(op => {
              return {
                role_name: op.role_name,
                operator_name: op.operator_name || ''
              };
            });
            this.budget_machine.push(machine);
          });

          data.budget_charge.forEach((charge) => {
            this.budget_charge.push({
              charge_name: charge.charge_name,
              fee_amount: charge.fee_amount,
              observation: charge.observation,
              budget_uuid: charge.budget_uuid
            });
          });

          // calcula totais com dados carregados
          this.calculateTotals();

          if (this.project_uuid) {
            this.get_project_by_uuid();
          }
        })
        .catch((error) => {
          console.error('Error fetching data:', error);
        });
    },
    confirmSubmissionCancel() {
      this.showConfirmationCancel = true;
    },
    submitProposalCancel() {
      this.showConfirmationCancel = false;
      this.cancel_date();
    },

    async cancel_date() {
      try {
        const response = await fetch(
          `https://fortis-api.55technology.com/v1/budget/cancel/${this.$route.params.budget_uuid}`,
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
        this.$router.go(0);

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
    confirmSubmission() {
      this.showConfirmation = true;
    },
    submitProposal() {
      this.showConfirmation = false;
      this.on_submit();
    },
    toCents(value) {
      return Math.round(this.parseMoney(value) * 100)
    },
    async on_submit() {
      const selected_charges = this.documentItems
        .map((item) => item.selected)
        .filter((selected) => !!selected); // remove undefined/null

      const listaDeUuids = this.budget_machine.map(machine => ({
        budget_machine_uuid: machine.budget_machine_uuid,
        budget_machine_price_per_hour: machine.budget_machine_price_per_hour,
        budget_machine_price_per_distance: machine.budget_machine_price_per_distance,
        budget_machine_minimum_rental_period: machine.budget_machine_minimum_rental_period,
      }));

      fetch('https://fortis-api.55technology.com/v1/budget/', {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          token: localStorage.getItem('access_token'),
        },
        body: JSON.stringify({
          budget_uuid: this.$route.params.budget_uuid,
          description: this.description,
          condition: this.condition,
          observation: this.budget_observation,
          amount: this.toCents(this.ValorTotal),
          total_fee: this.toCents(this.valortaxastotal),
          total_distance: this.parseDistance(this.total_distance), // >>> NOVO no payload
          service_charge_list: selected_charges, // agora só os selecionados
          budget_machine_uuid_list: listaDeUuids,
        }),
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error('Erro ao cadastrar orçamento.');
          }

          this.$q.notify({
            color: 'green-4',
            textColor: 'white',
            icon: 'cloud_done',
            message: 'Orçamento Enviado com Sucesso!',
          });

          const company_uuid = localStorage.getItem('company_uuid');
          this.$router.push(`/dashboard/${company_uuid}`);
        })
        .catch((error) => {
          this.$q.notify({
            color: 'red-4',
            textColor: 'white',
            icon: 'error',
            message: error.message,
          });
        });
    },
    async post_role_operator(machine) {
      if (!machine.modelMultiple || !Array.isArray(machine.modelMultiple)) {
        return;
      }

      if (!machine.budget_machine_uuid) {
        this.$q.notify({
          color: 'red-4',
          textColor: 'white',
          icon: 'error',
          message: 'UUID da máquina não encontrado.',
        });
        return;
      }

      try {
        const response = await fetch(
          `https://fortis-api.55technology.com/v1/budget/machine/operator/`,
          {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              token: localStorage.getItem('access_token'),
            },
            body: JSON.stringify({
              budget_machine_uuid: machine.budget_machine_uuid,
              employee_list: machine.modelMultiple.map((e) => ({
                role_uuid: e.uuid,
              })),
            }),
          }
        );

        if (!response.ok) {
          throw new Error('Erro ao cadastrar Categoria.');
        }

        this.$q.notify({
          color: 'green-4',
          textColor: 'white',
          icon: 'cloud_done',
          message: `Categoria anexados com sucesso à máquina ${machine.model_name}`,
        });

        window.location.reload();
      } catch (error) {
        this.$q.notify({
          color: 'red-4',
          textColor: 'white',
          icon: 'error',
          message: error.message,
        });
      }
    },
    async delete_budget_machine(budget_machine_uuid) {
      try {
        const response = await fetch(
          `https://fortis-api.55technology.com/v1/budget/machine/${budget_machine_uuid}`,
          {
            method: 'DELETE',
            headers: {
              'Content-Type': 'application/json',
              token: localStorage.getItem('access_token'),
            },
          }
        );

        if (!response.ok) {
          throw new Error('Erro ao deletar máquina do orçamento.');
        }

        this.$q.notify({
          color: 'green-4',
          textColor: 'white',
          icon: 'cloud_done',
          message: 'Máquina removida com sucesso!',
        });

        // Remove da lista local sem reload
        this.budget_machine = this.budget_machine.filter(
          (machine) => machine.budget_machine_uuid !== budget_machine_uuid
        );

      } catch (error) {
        this.$q.notify({
          color: 'red-4',
          textColor: 'white',
          icon: 'error',
          message: error.message,
        });
      }
    },
    async getBudgetPDF() {
      try {
        const response = await fetch(`https://fortis-api.55technology.com/v1/budget/pdf/${this.$route.params.budget_uuid}`, {
          method: 'GET',
          headers: { token: localStorage.getItem('access_token') },
        });

        if (!response.ok) {
          throw new Error(`Erro ao buscar caminho do PDF: ${response.statusText}`);
        }

        const data = await response.json();
        const filePath = data.output;

        // Abre o arquivo direto numa nova aba
        window.open(`https://fortis-api.55technology.com${filePath}`, '_blank');

      } catch (error) {
        console.error('Erro ao buscar ou abrir o PDF:', error);
      }
    }

  },
};
</script>
