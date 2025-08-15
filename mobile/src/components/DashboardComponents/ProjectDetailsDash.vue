<template>
  <q-page :class="['bg-white', { 'overflow-hidden': loading }]">
    <!-- Header -->
    <div class="row justify-around items-center q-pa-md q-mt-lg">
      <q-btn flat round icon="chevron_left" class="text-primary" color="secondary" size="18px" style="position: absolute; left: 10px" @click="$router.go(-1)"/>
      <div v-if="loading" class="q-ml-md"><q-skeleton type="text" width="220px" height="1.5rem" /></div>
      <div v-else class="text-h4 text-primary text-bold q-ml-md">Detalhes do projeto</div>
    </div>

    <!-- Top actions -->
    <div class="flex justify-end" style="margin-right: 10px; margin-bottom: 10px" v-if="loading">
      <q-skeleton type="rect" class="q-mr-sm" width="160px" height="36px" />
      <q-skeleton type="rect" class="q-mr-sm" width="140px" height="36px" />
      <q-skeleton type="rect" width="200px" height="36px" />
    </div>

    <div class="flex justify-end" style="margin-right: 10px; margin-bottom: 10px" v-else>
      <q-btn label="DESEJA CANCELAR" color="red" @click="confirmSubmissionCancel" style="margin-right: 5px"/>
      <q-btn color="green" label="Agendamento" :to="`/dashboard/project/details/date/${$route.params.budget_uuid}`" style="margin-right: 5px" />
      <q-btn color="orange" icon-right="picture_as_pdf" label="Exportar orçamento" @click="getBudgetPDF()"/>

      <!-- Caixa de Confirmação -->
      <q-dialog v-model="showConfirmationCancel">
        <q-card>
          <q-card-section><div class="text-h6">Deseja cancelar?</div></q-card-section>
          <q-card-actions>
            <q-btn flat label="Sim" color="primary" @click="submitProposalCancel" />
            <q-btn flat label="Não" color="secondary" @click="showConfirmationCancel = false" />
          </q-card-actions>
        </q-card>
      </q-dialog>
    </div>


    <q-separator class="bg-secondary" />

    <div v-if="status_name === 'ACEITO' && !loading">
      <p class="text-primary text-bold text-h5 text-center q-mt-md text-red">AGENDAMENTO SOLICITADO PARA DIA {{ expected_date }}</p>
    </div>

    <!-- Info projeto -->
    <div v-if="loading" class="q-pa-md" style="min-width: 300px">
      <q-list bordered separator class="text-primary" style="border-color: secondary; border-radius: 5px">
        <q-item v-for="i in 7" :key="i"><q-item-section><q-skeleton type="text" :width="(140+i*20)+'px'"/></q-item-section></q-item>
      </q-list>
    </div>

    <div v-else class="q-pa-md" style="min-width: 300px">
      <q-list bordered separator class="text-primary" style="border-color: secondary; border-radius: 5px">
        <q-item><q-item-section><q-item-label overline class="text-bold"><strong class="text-primary text-h6">Informações do Projeto</strong></q-item-label></q-item-section></q-item>
        <q-item><q-item-section><q-item-label class="text-weight-light"><strong>Data da criação:</strong> {{ created_at }}</q-item-label></q-item-section></q-item>
        <q-item>
          <q-item-section><q-item-label class="text-weight-light"><strong>Data de Início:</strong> {{ expected_date }} {{ expected_hour }}</q-item-label></q-item-section>
          <q-item-section><q-item-label class="text-weight-light"><strong>Data do Fim:</strong> {{ expected_date_end }} {{ expected_hour_end }}</q-item-label></q-item-section>
        </q-item>
        <q-item><q-item-section><q-item-label class="text-weight-light"><strong>Nome do projeto:</strong> {{ project_name }}</q-item-label></q-item-section></q-item>
        <q-item><q-item-section><q-item-label class="text-weight-light"><strong>Descrição:</strong> {{ project_description }}</q-item-label></q-item-section></q-item>
        <q-item><q-item-section><q-item-label class="text-weight-light"><strong>Nome do cliente:</strong> {{ client_name || client_company_name }}</q-item-label></q-item-section></q-item>
        <q-item><q-item-section><q-item-label class="text-weight-light"><strong>CPF / CNPJ:</strong> {{ client_cpf || user_cpf }}</q-item-label></q-item-section></q-item>
        <q-item><q-item-section><q-item-label class="text-weight-light"><strong>Email:</strong> {{ email || client_email }}</q-item-label></q-item-section></q-item>
        <q-item><q-item-section><q-item-label class="text-weight-light"><strong>Número:</strong> {{ phone || client_phone }}</q-item-label></q-item-section></q-item>
        <q-item>
          <q-item-section>
            <q-item-label class="text-weight-light"><strong>Categoria:</strong>
              <span v-for="(category, idx) in name_machine_category" :key="idx">{{ category }}<span v-if="idx < name_machine_category.length-1">, </span></span>
            </q-item-label>
          </q-item-section>
        </q-item>
        <q-item v-if="payer_name">
          <q-item-section><q-item-label class="text-weight-light"><strong>Pagador:</strong> {{ payer_name }} <p><strong>CNPJ/CPF:</strong> {{ cnpj }}</p></q-item-label></q-item-section>
        </q-item>
      </q-list>
    </div>

    <!-- Detalhes -->
    <div v-if="!loading" class="q-pa-md" style="min-width: 300px">
      <q-list bordered separator class="text-primary" style="border-color: secondary; border-radius: 5px">
        <q-expansion-item label="Informações Detalhadas" header-class="text-primary text-h6">
          <div v-for="(stage, i) in project_stages" :key="i" class="q-mb-md">
            <q-separator spaced />
            <div class="text-h6 q-ml-md">{{ stage.name_machine_category }}</div>
            <div v-if="stage.parameters" class="q-ml-md q-mt-sm">
              <p v-if="stage.parameters.maximum_horizontal_reach">Alc. máx horizontal (m): {{ stage.parameters.maximum_horizontal_reach }}</p>
              <p v-if="stage.parameters.maximum_nominal_lifting_capacity">Cap. máx elevação (Kg/tn): {{ stage.parameters.maximum_nominal_lifting_capacity }}</p>
              <p v-if="stage.parameters.maximum_nominal_vertical_reach_capacity">Cap. máx alcance vertical (m): {{ stage.parameters.maximum_nominal_vertical_reach_capacity }}</p>
              <p v-if="stage.parameters.maximum_reach_without_jib">Alc. máx sem JIB (m): {{ stage.parameters.maximum_reach_without_jib }}</p>
              <p v-if="stage.parameters.maximum_vertical_reach">Alc. máx vertical (m): {{ stage.parameters.maximum_vertical_reach }}</p>
              <p v-if="stage.parameters.excavator_size && Object.keys(stage.parameters).length===1">Tamanho: {{ stage.parameters.excavator_size }}</p>
            </div>
          </div>
        </q-expansion-item>
      </q-list>
    </div>

    <!-- Endereço -->
    <div v-if="!loading" class="q-pa-md" style="min-width: 300px">
      <q-list bordered separator class="text-primary" style="border-color: secondary; border-radius: 5px">
        <q-expansion-item label="Endereço do Projeto" header-class="text-primary text-h6">
          <p class="text-weight-light q-ml-md"><strong>Cep:</strong> {{ zip_code }}</p>
          <p class="text-weight-light q-ml-md"><strong>Rua:</strong> {{ street }}</p>
          <p class="text-weight-light q-ml-md"><strong>Número:</strong> {{ number_street }}</p>
          <p class="text-weight-light q-ml-md"><strong>Complemento:</strong> {{ complement }}</p>
          <p class="text-weight-light q-ml-md"><strong>Bairro:</strong> {{ neighborhood }}</p>
          <p class="text-weight-light q-ml-md"><strong>Cidade:</strong> {{ city_name }}</p>
          <p class="text-weight-light q-ml-md"><strong>Estado:</strong> {{ state_name }}</p>
        </q-expansion-item>
      </q-list>
    </div>

    <!-- Histórico -->
    <div v-if="!loading" class="q-pa-md" style="min-width: 300px">
      <q-list bordered separator style="border-color: secondary; border-radius: 8px" class="text-primary">
        <q-expansion-item label="Histórico" header-class="text-primary text-h6">
          <div class="q-pa-md">
            <p class="text-primary text-subtitle1 q-ml-md q-pa-sm"><strong>Status:</strong> <span class="text-red text-bold">{{ status_name }}</span></p>

            <div v-for="(machine, index) in budget_machine" :key="index" class="q-pa-md q-ml-md q-mb-md bg-grey-2 q-rounded-borders shadow-1">
              <div class="q-mb-sm"><strong>Nome da máquina:</strong> {{ machine.machine_name }}</div>
              <div><strong>Marca:</strong> {{ machine.brand }}</div>
              <div><strong>Categoria:</strong> {{ machine.category_name }}</div>
              <div><strong>Operador:</strong> {{ machine.employees?.[0]?.operator_name || 'Sem operador' }}</div>
              <div><strong>Placa:</strong> {{ machine.license_plate }}</div>
              <div v-if="machine.parameters?.loader_size"><strong>Tamanho:</strong> {{ machine.parameters.loader_size }}</div>
              <div v-if="machine.parameters?.excavator_size"><strong>Tamanho Escavadeira:</strong> {{ machine.parameters.excavator_size }}</div>
              <div v-if="machine.parameters?.backhoe_type"><strong>Tipo de Retroescavadeira:</strong> {{ machine.parameters.backhoe_type }}</div>
              <div v-if="machine.budget_machine_price_per_distance !== '0,00'"><strong>Preço Fixo:</strong> R$ {{ machine.budget_machine_price_per_distance }}</div>
              <div v-if="machine.budget_machine_price_per_hour !== '0,00'"><strong>Preço Franquia:</strong> R$ {{ machine.budget_machine_price_per_hour }}</div>

            </div>
          </div>
        </q-expansion-item>
      </q-list>
    </div>

    <!-- Novo Orçamento / máquinas -->
    <div v-if="!loading" class="q-pa-md">
      <q-list bordered separator class="text-primary" style="border-color: secondary">
        <q-expansion-item
          v-for="(machine, index) in budget_machine"
          :key="index"
          :label="`Novo Orçamento ${index + 1} - ${machine.category_name || ''} - ${machine.model_name || ''}`"
          header-class="text-primary text-h6"
        >
          <q-btn dense class="q-ma-md" label="DELETAR MÁQUINA" icon="delete" color="red"
                 :loading="!!machine._deleting" :disable="!!machine._deleting"
                 @click.stop="delete_budget_machine(machine.budget_machine_uuid, machine)" />

          <q-form @submit="on_submit" class="q-pa-md">
            <q-btn class="q-mb-md" label="ADCIONAR MÁQUINAS" :to="`/dashboard/project/details/create/${$route.params.budget_uuid}`" />
            <q-input class="q-mr-md q-mb-md" v-model="machine.brand" filled label="Marca" style="max-width: 300px" readonly />

            <!-- parâmetros escavação -->
            <q-input v-if="machine.parameters?.excavator_size" class="q-mr-md q-mb-md" v-model="machine.parameters.excavator_size" filled label="Parametro" style="max-width: 600px" readonly />
            <q-input v-if="machine.parameters?.backhoe_type" class="q-mr-md q-mb-md" v-model="machine.parameters.backhoe_type" filled label="Parametro" style="max-width: 600px" readonly />
            <q-input v-if="machine.parameters?.loader_size" class="q-mr-md q-mb-md" v-model="machine.parameters.loader_size" filled label="Parametro" style="max-width: 600px" readonly />

            <!-- parâmetros elevação -->
            <div v-if="machine.jib !== null && machine.jib !== undefined">
              <p class="text-bold text-h6">Parametros de Elevação</p>
              <q-input v-if="machine.jib" class="q-mr-md q-mb-md" v-model="machine.jib" filled label="JIB (m)" style="max-width: 600px" readonly />
              <q-input v-if="machine.max_weight" class="q-mr-md q-mb-md" v-model="machine.max_weight" filled label="Peso Máximo (kg / tn)" style="max-width: 600px" readonly />
              <q-input v-if="machine.max_height" class="q-mr-md q-mb-md" v-model="machine.max_height" filled label="Altura Máxima (m)" style="max-width: 600px" readonly />
              <q-input v-if="machine.max_radius" class="q-mr-md q-mb-md" v-model="machine.max_radius" filled label="Raio Máximo (m)" style="max-width: 600px" readonly />
              <q-input v-if="machine.parameters?.maximum_nominal_lifting_capacity" class="q-mr-md q-mb-md" v-model="machine.parameters.maximum_nominal_lifting_capacity" filled label="Capacidade máxima nomimal de elevação (Kg / tn)" style="max-width: 600px" readonly />
              <q-input v-if="machine.parameters?.maximum_nominal_vertical_reach_capacity" class="q-mr-md q-mb-md" v-model="machine.parameters.maximum_nominal_vertical_reach_capacity" filled label="Capacidade máxima nomimal de alcance vertical (m)" style="max-width: 600px" readonly />
              <q-input v-if="machine.parameters?.maximum_reach_without_jib" class="q-mr-md q-mb-md" v-model="machine.parameters.maximum_reach_without_jib" filled label="Alcance máximo sem JIB (m)" style="max-width: 600px" readonly />
              <q-input v-if="machine.parameters?.maximum_vertical_reach" class="q-mr-md q-mb-md" v-model="machine.parameters.maximum_vertical_reach" filled label="Alcance máximo vertical (m)" style="max-width: 600px" readonly />
              <q-input v-if="machine.parameters?.maximum_horizontal_reach" class="q-mr-md q-mb-md" v-model="machine.parameters.maximum_horizontal_reach" filled label="Alcance máximo horizontal (m)" style="max-width: 600px" readonly />
            </div>

            <!-- preço/hora e distância -->
            <div v-if="machine.price_per_hour != '0,00'">
              <q-checkbox class="text-bold" :model-value="Franquia" true-value="Franquia" false-value="" @update:model-value="val => { if (Franquia === 'Franquia') return; Franquia = val; }">FRANQUIA</q-checkbox>
              <q-input v-model.number="machine.budget_machine_minimum_rental_period" type="number" filled label="Horas franquia mínima" style="max-width: 300px" class="q-mt-md q-mb-md" min="0" step="0.5" @update:model-value="calculateTotals"/>
              <q-input v-model="machine.budget_machine_price_per_hour" filled label="Valor Hora Enviado" style="max-width: 300px" class="q-mt-md q-mb-md" :prefix="'R$ '"/>
              <q-input v-model="machine.price_per_hour" filled label="Valor Hora" style="max-width: 300px" class="q-mt-md q-mb-md" readonly :prefix="'R$ '"/>
              <q-input v-model="machine.budget_machine_price_per_distance" filled label="Valor Distância Enviado (R$/km)" style="max-width: 300px" class="q-mt-md q-mb-md" :prefix="'R$ '" @update:model-value="calculateTotals"/>
              <q-input v-model="machine.price_per_distance" filled label="Valor Distância (R$/km)" style="max-width: 300px" class="q-mt-md q-mb-md" readonly :prefix="'R$ '"/>
              <q-input :model-value="formatDistanceCost(machine)" filled readonly label="Custo de Distância" :prefix="'R$ '" style="max-width: 320px" class="q-mt-md"/>
            </div>

            <!-- Operador/Categoria -->
            <div>
              <q-checkbox class="text-bold q-mt-md" v-model="Categoria" true-value="Categoria">OPERADOR</q-checkbox>
              <p class="text-bold text-h6">ANEXAR CATEGORIA A MÁQUINA</p>
              <q-btn color="red" class="q-mb-md" label="ANEXAR COLABORADOR A MÁQUINA" :to="`/dashboard/role/collaborator/create/${$route.params.budget_uuid}`" />
              <div class="row">
                <q-select
                  v-model="machine.modelMultiple"
                  :options="roles"
                  use-chips multiple stack-label outlined
                  option-label="name" option-value="uuid"
                  label="Categoria Principal"
                  class="q-mb-md" style="min-width:300px;"
                />
                <div>
                  <q-btn
                    label="Salvar" color="primary" class="q-ml-md"
                    :loading="!!machine._posting"
                    :disable="!machine.modelMultiple?.length || !!machine._posting"
                    @click="post_role_operator(machine)"
                  />
                </div>
              </div>

              <!-- lista de anexos -->
              <div style="max-width: 480px">
                <q-list bordered separator>
                  <q-item v-for="(operador, idxOp) in machine.lista_operadores" :key="operador._key || idxOp">
                    <q-item-section>
                      <q-item-label><strong>Categoria:</strong> {{ operador.role_name }}</q-item-label>
                      <q-item-label v-if="operador.operator_name"><strong>Operador:</strong> {{ operador.operator_name }}</q-item-label>
                    </q-item-section>
                    <q-item-section side top>
                      <q-btn round dense flat icon="delete" color="negative"
                             :loading="!!operador._removing"
                             :disable="!operador.machine_operator_uuid || !!operador._removing"
                             @click="detach_role_operator(machine, operador, idxOp)"/>
                    </q-item-section>
                  </q-item>
                </q-list>
              </div>
            </div>
          </q-form>
        </q-expansion-item>
      </q-list>
    </div>

    <!-- Documentação / Condição -->
    <div v-if="!loading" class="q-pa-md" style="min-width: 300px">
      <q-list bordered separator class="text-primary" style="border-color: secondary">
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

        <q-expansion-item label="Licenças, Taxas, Seguros e Outros" header-class="text-primary text-h6">
          <div class="q-pa-md">
            <div class="q-mb-md"><q-btn rounded icon="add" color="secondary" @click="addDocumentItem" label="Adicionar campo" /></div>

            <div v-for="(item, index) in documentItems" :key="index" class="q-mb-md">
              <div class="row items-center q-col-gutter-md">
                <div class="col-12 col-md-2">
                  <q-select v-model="item.selected" :options="service_charge_company" option-label="service_charge_name"
                            label="Tipo" outlined emit-value map-options class="q-mt-md"
                            popup-content-style="color:#000;font-weight:400;scrollbar-width:thin;scrollbar-color:#303940 #ccc;"
                            @update:model-value="onDocumentChange(index)"/>
                </div>
                <div class="col-12 col-md-2" v-if="item.selected">
                  <q-input v-model="item.selected.fee_amount" filled label="Valor Taxa" :prefix="'R$ '" @update:model-value="calculateTotals"/>
                </div>
                <div class="col-auto" v-if="documentItems.length > 1">
                  <q-btn icon="delete" color="red" round dense flat @click="removeDocumentItem(index)" />
                </div>
              </div>
            </div>
          </div>

          <!-- Textareas -->
          <div class="row q-pa-md q-col-gutter-md">
            <div class="col-12 col-md-6"><q-input v-model="condition" label="Condição" filled type="textarea" /></div>
            <div class="col-12 col-md-6"><q-input v-model="budget_observation" label="Observação Geral" filled type="textarea" /></div>
          </div>

          <!-- Totais -->
          <div class="row q-pa-md q-col-gutter-md">
            <div class="col-12 col-md-4"><q-input v-model.number="total_distance" type="number" label="Distância total (km)" filled min="0" step="0.5" @update:model-value="calculateTotals"/></div>
            <div class="col-12 col-md-4"><q-input v-model="valortaxastotal" filled readonly :prefix="'R$ '" label="Valor Total das Taxas e Licenças"/></div>
            <div class="col-12 col-md-4"><q-input :model-value="distanceTotalCostFormatado" filled readonly :prefix="'R$ '" label="Custo Total de Distância"/></div>
            <div class="col-12 col-md-4">
              <q-input v-for="(m, i) in budget_machine" :key="i" :model-value="formatFranquiaMinima(m)" filled readonly :label="`Valor franquia mínima: ${m.machine_name}`" :prefix="'R$ '" class="q-mb-sm"/>
            </div>
            <div class="col-12 col-md-4"><q-input filled readonly label="Valor Total" :model-value="ValorTotalFormatado" :prefix="'R$ '"/></div>
          </div>

          <!-- Enviar -->
          <div class="q-pa-md flex justify-center items-center">
            <q-btn color="primary" unelevated size="lg" class="q-px-lg q-py-sm text-white" @click="confirmSubmission">
              <div class="row items-center no-wrap"><span class="text-subtitle1 q-mr-sm">Enviar</span><q-icon name="arrow_forward" size="22px" /></div>
            </q-btn>
          </div>
        </q-expansion-item>
      </q-list>
    </div>

    <!-- Confirma envio -->
    <q-dialog v-model="showConfirmation">
      <q-card>
        <q-card-section><div class="text-h6">Deseja enviar o orçamento?</div></q-card-section>
        <q-card-actions>
          <q-btn flat label="Sim" color="primary" @click="submitProposal" />
          <q-btn flat label="Não" color="secondary" @click="showConfirmation = false" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { ref } from 'vue'

export default {
  name: 'ProjectBudget',
  data () {
    return {
      expected_hour_end: '', expected_date_end: '', expected_hour: '',
      roles: [], loading: true, payer_name:'', cnpj:'',
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
      observation:'', budget_observation:'',
      client_name:'', client_email:'', client_phone:'', client_company_name:'',
      showConfirmation: false,          // <-- PATCH MÍNIMO ADICIONADO
      showConfirmationCancel:false,
      user_name:'', created_at:'', email:'', phone:'',
      name_machine_category:'', expected_date:'', project_name:'', project_description:'',
      project_uuid:'', need_operator:'', fixed_price:'', uuid:'',
      minimum_rental_price:0, machine_name:'', lista_operadores:'',
      modelMultiple: ref(), Fixo: ref('Fixo'), Franquia: ref('Franquia'), Colaborador: ref('Colaborador'), Categoria: ref('Categoria'),
      zip_code:'', street:'', number_street:'', complement:'', neighborhood:'', city_name:'', state_name:'',
      user_cpf:'', client_cpf:'', documentItems:[{ selected: null }], service_charge_company:[],
      ValorTotal:0, total_fee:'', valortaxastotal:0, project_stages:[], budget_proposals:[],
      company_uuid:'', budget_machine:[], budget_charge:[], price:'', price_per_hour:'', price_per_distance:'', brand:'',
      // elevação
      jib:'', articulated_load_capacity:'', articulated_maximum_lifting_height:'', compactor_type:'',
      crawler_tractor_size:'', crawler_tractor_size_type:'', lifting_load_capacity:'', lifting_maximum_lifting_height:'',
      maximum_horizontal_reach:'', maximum_nominal_lifting_capacity:'', maximum_nominal_vertical_reach_capacity:'',
      maximum_reach_without_jib:'', maximum_vertical_reach:'',
      // trailer/caminhão
      brand_trailer:'', model_trailer:'', brand_truck:'', model_truck:'',
      // escavação
      excavator_size:'', loader_size:'',
      description:'', status_name:'',
      total_distance: 0, distanceTotalCost: 0
    }
  },
  async mounted () {
    this.company_uuid = localStorage.getItem('company_uuid')
    await Promise.all([ this.get_budget_by_project_uuid(), this.get_service_charge(), this.get_roles() ])
    this.loading = false
  },
  computed: {
    ValorTotalFormatado () { return this.ValorTotal.toLocaleString('pt-BR',{minimumFractionDigits:2,maximumFractionDigits:2}) },
    distanceTotalCostFormatado () { return this.distanceTotalCost.toLocaleString('pt-BR',{minimumFractionDigits:2,maximumFractionDigits:2}) }
  },
  watch: {
    budget_machine_price_per_hour () { this.calculateTotals() },
    budget_machine: { deep:true, handler () { this.calculateTotals() } },
    total_distance () { this.calculateTotals() }
  },
  methods: {
    async get_roles () {
      await fetch(`https://fortis-api.55technology.com/v1/role/company/${this.company_uuid}`, { headers:{ token: localStorage.getItem('access_token') } })
        .then(r => { if(!r.ok) throw 0; return r.json() }).then(d => { this.roles = d.roles || [] })
        .catch(() => this.$q.notify({color:'red-5',textColor:'white',icon:'cloud_done',message:'Nenhuma categoria encontrada!'}))
    },
    addDocumentItem(){ this.documentItems.push({selected:null}) },
    removeDocumentItem(i){ this.documentItems.splice(i,1); this.calculateTotals() },
    onDocumentChange(){ this.calculateTotals() },
    parseMoney(v){ if(typeof v==='string') v=v.replace(/\./g,'').replace(',','.'); const n=parseFloat(v); return Number.isFinite(n)?n:0 },
    parseHours(v,f=10){ const n=typeof v==='string'?parseFloat(v.replace(',','.')):Number(v); return Number.isFinite(n)&&n>=0?n:f },
    parseDistance(v){ if(v==null||v==='') return 0; if(typeof v==='string') v=v.replace(',','.'); const n=parseFloat(v); return Number.isFinite(n)&&n>=0?n:0 },
    formatFranquiaMinima(m){ const total=this.parseMoney(m.budget_machine_price_per_hour||0)*this.parseHours(m.budget_machine_minimum_rental_period,10); return total.toLocaleString('pt-BR',{minimumFractionDigits:2,maximumFractionDigits:2}) },
    distanceCostForMachine(m){ return this.parseDistance(this.total_distance)*this.parseMoney(m.budget_machine_price_per_distance||0) },
    formatDistanceCost(m){ return this.distanceCostForMachine(m).toLocaleString('pt-BR',{minimumFractionDigits:2,maximumFractionDigits:2}) },
    calculateTotals(){
      const tSel=this.documentItems.reduce((a,i)=>a+this.parseMoney(i.selected?.fee_amount||0),0)
      const tGet=this.budget_charge.reduce((a,c)=>a+this.parseMoney(c.fee_amount||0),0)
      this.valortaxastotal=tSel+tGet
      const tHora=this.budget_machine.reduce((a,m)=>a+this.parseMoney(m.budget_machine_price_per_hour||0)*this.parseHours(m.budget_machine_minimum_rental_period,10),0)
      const tDist=this.budget_machine.reduce((a,m)=>a+this.distanceCostForMachine(m),0)
      this.distanceTotalCost=tDist
      this.ValorTotal=this.valortaxastotal+tHora+tDist
    },
    async get_project_by_uuid(){
      fetch(`https://fortis-api.55technology.com/v1/project/${this.project_uuid}`, { headers:{ token: localStorage.getItem('access_token') } })
        .then(r=>{ if(!r.ok) throw 0; return r.json() })
        .then(d=>{
          this.user_name=d.project.user_name; this.created_at=d.project.created_at; this.email=d.project.email; this.phone=d.project.phone;
          this.client_email=d.project.client_email; this.client_phone=d.project.client_phone; this.project_name=d.project.project_name;
          this.user_cpf=d.project.user_cpf; this.client_cpf=d.project.client_cpf; this.project_description=d.project.project_description;
          this.name_machine_category=d.project_stages.map(s=>s.name_machine_category); this.project_stages=d.project_stages;
          this.zip_code=d.project.zip_code; this.street=d.project.street; this.number_street=d.project.number_street; this.complement=d.project.complement;
          this.neighborhood=d.project.neighborhood; this.city_name=d.project.city_name; this.state_name=d.project.state_name;
          this.need_operator=d.project_stages[0].need_operator; this.fixed_price=d.project.fixed_price
        })
        .catch(()=> this.$q.notify({color:'red-5',textColor:'white',icon:'cloud_done',message:'Nenhum orçamento cadastrado!'}))
    },
    async get_service_charge(){
      await fetch(`https://fortis-api.55technology.com/v1/service/charge/company/${this.company_uuid}`, { headers:{ token: localStorage.getItem('access_token') } })
        .then(r=>{ if(!r.ok) throw 0; return r.json() }).then(d=>{ this.service_charge_company=d.service_charge_company })
        .catch(()=> this.$q.notify({color:'red-5',textColor:'white',icon:'cloud_done',message:'Nenhum Documento cadastrado!'}))
    },
    async get_budget_by_project_uuid(){
      await fetch(`https://fortis-api.55technology.com/v1/budget/${this.$route.params.budget_uuid}`, { headers:{ token: localStorage.getItem('access_token') } })
        .then(r=>{ if(!r.ok) throw 0; return r.json() })
        .then(data=>{
          this.project_name=data.budget.project_name; this.client_company_name=data.budget.client_company_name; this.client_name=data.budget.client_name;
          this.created_at=data.budget.created_at; this.status_name=data.budget.status_name; this.project_uuid=data.budget.project_uuid;
          this.uuid=this.$route.params.budget_uuid; this.expected_hour_end=data.budget.expected_hour_end; this.payer_name=data.budget.payer_name; this.cnpj=data.budget.cnpj;
          this.expected_date=data.budget.expected_date; this.expected_hour=data.budget.expected_hour; this.expected_date_end=data.budget.expected_date_end;
          this.minimum_rental_price=data.minimum_rental_price; this.total_distance=this.parseDistance(data.budget?.total_distance ?? 0);
          this.budget_machine=[]; this.budget_charge=[]; this.budget_observation=data.budget.budget_observation;

          data.budget_machine.forEach(machine=>{
            machine._posting=false; machine._deleting=false;
            machine.modelMultiple=[]; // seleção atual
            machine.lista_operadores=(machine.employees || []).map(op=>({
              _key:`${op.machine_operator_uuid || op.role_uuid || op.role_name}-${Math.random().toString(36).slice(2,8)}`,
              role_name: op.role_name,
              operator_name: op.operator_name || '',
              role_uuid: op.role_uuid || null,
              machine_operator_uuid: op.machine_operator_uuid || null
            }))
            this.budget_machine.push(machine)
          })

          data.budget_charge.forEach(c=> this.budget_charge.push({charge_name:c.charge_name, fee_amount:c.fee_amount, observation:c.observation, budget_uuid:c.budget_uuid}))
          this.calculateTotals()
          if(this.project_uuid) this.get_project_by_uuid()
        })
        .catch(e=>console.error('Error fetching budget:',e))
    },

    confirmSubmission(){ this.showConfirmation=true },
    submitProposal(){ this.showConfirmation=false; this.on_submit() },
    confirmSubmissionCancel(){ this.showConfirmationCancel=true },
    submitProposalCancel(){ this.showConfirmationCancel=false; this.cancel_date() },

    async cancel_date(){
      try{
        const r=await fetch(`https://fortis-api.55technology.com/v1/budget/cancel/${this.$route.params.budget_uuid}`,{method:'DELETE',headers:{token:localStorage.getItem('access_token')}})
        if(!r.ok) throw 0
        this.$q.notify({color:'green-5',textColor:'white',icon:'check',message:'Projeto cancelado com sucesso.'})
        this.$router.go(0)
      }catch{ this.$q.notify({color:'red-5',textColor:'white',icon:'error',message:'Erro ao cancelar o Projeto.'}) }
    },

    toCents(v){ return Math.round(this.parseMoney(v)*100) },

    async on_submit(){
      const selected_charges=this.documentItems.map(i=>i.selected).filter(Boolean)
      const listaDeUuids=this.budget_machine.map(m=>({
        budget_machine_uuid:m.budget_machine_uuid,
        budget_machine_price_per_hour:m.budget_machine_price_per_hour,
        budget_machine_price_per_distance:m.budget_machine_price_per_distance,
        budget_machine_minimum_rental_period:m.budget_machine_minimum_rental_period
      }))

      try{
        const res = await fetch('https://fortis-api.55technology.com/v1/budget/',{
          method:'PUT',
          headers:{'Content-Type':'application/json',token:localStorage.getItem('access_token')},
          body:JSON.stringify({
            budget_uuid:this.$route.params.budget_uuid,
            description:this.description,
            condition:this.condition,
            observation:this.budget_observation,
            amount:this.toCents(this.ValorTotal),
            total_fee:this.toCents(this.valortaxastotal),
            total_distance:this.parseDistance(this.total_distance),
            service_charge_list:selected_charges,
            budget_machine_uuid_list:listaDeUuids
          })
        })
        if(!res.ok) throw new Error('Erro ao cadastrar orçamento.')

        this.$q.notify({color:'green-4',textColor:'white',icon:'cloud_done',message:'Orçamento Enviado com Sucesso!'})
        const company_uuid=localStorage.getItem('company_uuid')
        this.$router.push(`/dashboard/${company_uuid}`)
      }catch(err){
        this.$q.notify({color:'red-4',textColor:'white',icon:'error',message:err.message})
      }
    },

    async post_role_operator(machine){
      if(!machine?.modelMultiple || !Array.isArray(machine.modelMultiple) || !machine.modelMultiple.length){
        this.$q.notify({color:'amber-7',textColor:'black',icon:'warning',message:'Selecione ao menos uma categoria.'}); return
      }
      if(!machine.budget_machine_uuid){
        this.$q.notify({color:'red-4',textColor:'white',icon:'error',message:'UUID da máquina não encontrado.'}); return
      }

      machine._posting=true
      const snapshot=[...(machine.lista_operadores || [])]
      if(!Array.isArray(machine.lista_operadores)) machine.lista_operadores=[]

      const novos=machine.modelMultiple.map(e=>({
        _key:`tmp-${(e.uuid||e.id||Math.random().toString(36).slice(2,6))}-${Date.now()}`,
        role_name:e.name, operator_name:'', role_uuid:e.uuid||null, machine_operator_uuid:null
      }))
      machine.lista_operadores.push(...novos)

      try{
        const res=await fetch(`https://fortis-api.55technology.com/v1/budget/machine/operator/`,{
          method:'POST',
          headers:{'Content-Type':'application/json',token:localStorage.getItem('access_token')},
          body:JSON.stringify({
            budget_machine_uuid:machine.budget_machine_uuid,
            employee_list:machine.modelMultiple.map(e=>({role_uuid:e.uuid}))
          })
        })
        if(!res.ok){ machine.lista_operadores=snapshot; throw new Error('Erro ao anexar categoria.') }

        machine.modelMultiple=[]
        this.$q.notify({color:'green-4',textColor:'white',icon:'cloud_done',message:`Categoria(s) anexada(s) à máquina ${machine.model_name || ''}`})
      }catch(err){
        this.$q.notify({color:'red-4',textColor:'white',icon:'error',message:err.message})
        machine.lista_operadores=snapshot
      }finally{
        machine._posting=false
      }
    },

    async detach_role_operator(machine, item, idxOp){
      if(!machine?.budget_machine_uuid){ this.$q.notify({color:'red-4',textColor:'white',icon:'error',message:'UUID da máquina não encontrado.'}); return }
      if(!item?.machine_operator_uuid){ this.$q.notify({color:'amber-7',textColor:'black',icon:'warning',message:'Vínculo sem machine_operator_uuid. Atualize os dados para remover.'}); return }

      const snapshot=[...machine.lista_operadores]
      machine.lista_operadores.splice(idxOp,1) ; item._removing=true

      try{
        const res=await fetch(`https://fortis-api.55technology.com/v1/budget/machine/operator/${item.machine_operator_uuid}`,{
          method:'DELETE', headers:{ token: localStorage.getItem('access_token') }
        })
        if(!res.ok){ machine.lista_operadores=snapshot; throw new Error('Erro ao desanexar categoria.') }
        this.$q.notify({color:'green-4',textColor:'white',icon:'check',message:'Categoria desanexada.'})
      }catch(e){
        this.$q.notify({color:'red-4',textColor:'white',icon:'error',message:e.message})
        machine.lista_operadores=snapshot
      }finally{
        item._removing=false
      }
    },

    async delete_budget_machine(uuid, mRef){
      try{
        mRef._deleting=true
        const r=await fetch(`https://fortis-api.55technology.com/v1/budget/machine/${uuid}`,{
          method:'DELETE', headers:{'Content-Type':'application/json', token: localStorage.getItem('access_token')}
        })
        if(!r.ok) throw new Error('Erro ao deletar máquina do orçamento.')
        this.$q.notify({color:'green-4',textColor:'white',icon:'cloud_done',message:'Máquina removida com sucesso!'})
        this.budget_machine=this.budget_machine.filter(m=>m.budget_machine_uuid!==uuid)
      }catch(err){
        this.$q.notify({color:'red-4',textColor:'white',icon:'error',message:err.message})
      }finally{
        mRef._deleting=false
      }
    },

    async getBudgetPDF(){
      try{
        const r=await fetch(`https://fortis-api.55technology.com/v1/budget/pdf/${this.$route.params.budget_uuid}`,{
          method:'GET', headers:{ token: localStorage.getItem('access_token') }
        })
        if(!r.ok) throw new Error(`Erro ao buscar caminho do PDF: ${r.statusText}`)
        const data=await r.json(); window.open(`https://fortis-api.55technology.com${data.output}`,'_blank')
      }catch(e){ console.error('Erro ao abrir PDF:',e) }
    }
  }
}
</script>
