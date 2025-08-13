<template>
  <q-page class="q-pa-md bg-white">
    <!--    <OmieComponent />-->
    <div class="text-h4 text-bold text-primary q-mb-md">Financeiro</div>

    <!-- Operadores -->
    <div class="text-h6 text-bold q-mb-sm text-primary">Operadores</div>
    <q-table
      :rows="financeRows"
      :columns="columns"
      row-key="id"
      flat
      dense
      class="q-mb-md"
    >
      <template v-slot:body-cell-dataHora="props">
        <q-td>
          <div class="row items-center q-gutter-sm">
            <div>
              <div class="text-caption">
                {{ props.row.dataInicio }} {{ props.row.horaInicio }}
                <span v-if="props.row.dataFim"
                >até {{ props.row.dataFim }} {{ props.row.horaFim }}</span
                >
              </div>
            </div>
            <q-btn
              dense
              round
              icon="event"
              size="sm"
              @click="openDataHoraPopup(props.row)"
            />
          </div>

          <q-popup-proxy
            v-model="props.row.showDataHora"
            transition-show="scale"
            transition-hide="scale"
          >
            <div class="q-pa-md bg-white">
              <q-date
                v-model="props.row.dataRange"
                range
                mask="DD/MM/YYYY"
                color="primary"
                locale="pt-br"
              />
              <div class="row q-mt-sm q-gutter-sm">
                <q-time
                  v-model="props.row.horaInicio"
                  format24h
                  mask="HH:mm"
                  label="Hora Início"
                  color="primary"
                />
                <q-time
                  v-model="props.row.horaFim"
                  format24h
                  mask="HH:mm"
                  label="Hora Fim"
                  color="primary"
                />
              </div>
              <div class="row justify-end q-mt-sm">
                <q-btn flat label="Fechar" v-close-popup />
              </div>
            </div>
          </q-popup-proxy>
        </q-td>
      </template>
    </q-table>

    <!-- Máquinas -->
    <div class="text-h6 text-bold q-mb-sm text-primary">
      Máquinas no orçamento
    </div>
    <q-table
      :rows="machineRows"
      :columns="machineColumns"
      row-key="budget_machine_uuid"
      flat
      dense
      class="q-mb-md"
    >
      <template v-slot:body-cell-actions="props">
        <q-td>
          <q-btn dense flat icon="add" @click="addMachineDuration(props.row)" />
          <q-btn
            dense
            flat
            icon="add_circle_outline"
            class="q-ml-xs"
            @click="addMachineDistance(props.row)"
          />
        </q-td>
      </template>
    </q-table>

    <!-- Encargos -->
    <div class="text-h6 text-bold q-mb-sm text-primary">Encargos</div>
    <q-table
      :rows="chargeRows"
      :columns="chargeColumns"
      row-key="charge_name"
      flat
      dense
      class="q-mb-md"
    />

    <!-- Valores adicionais -->
    <div class="row q-col-gutter-md q-mb-md">
      <div class="col">
        <q-input v-model="acrescimo" label="Acréscimo (R$)" outlined />
        <q-input
          v-model="motivoAcrescimo"
          label="Motivo do acréscimo"
          outlined
          class="q-mt-sm"
        />
      </div>
      <div class="col">
        <q-input v-model="desconto" label="Desconto (R$)" outlined />
        <q-input
          v-model="motivoDesconto"
          label="Motivo do desconto"
          outlined
          class="q-mt-sm"
        />
      </div>
    </div>

    <div class="text-h6 text-right q-mb-md">
      Total: <strong>R$ {{ total }}</strong>
    </div>

    <!-- Dados de cobrança -->
    <div class="q-mb-md">
      <div class="text-primary flex column">
        <div class="flex items-center justify-between">
          <span class="text-subtitle1">Criar novo cliente</span>
          <q-btn
            style="border-radius: 50%; padding: 6px; margin: 0"
            @click="isFormVisible = !isFormVisible"
          >
            <q-icon name="add" size="24px" />
          </q-btn>
        </div>

        <form
          style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem"
          class="q-mt-sm"
          v-if="isFormVisible"
          @submit.prevent="handleCreateNewClient"
        >
          <q-input
            v-model="newClient.razao_social"
            label="Razão Social"
            outlined
            required
          />
          <q-input
            v-model="newClient.nome_fantasia"
            label="Nome Fantasia"
            outlined
            required
          />
          <q-input
            v-model="newClient.cnpj_cpf"
            label="CNPJ/CPF"
            outlined
            :model-value="formattedCnpjCpf"
            @update:model-value="onInputCnpjCpf"
            maxlength="18"
            required
          />
          <q-input
            v-model="newClient.cep"
            label="CEP"
            outlined
            mask="#####-###"
            @update:model-value="onCep"
            required
          />
          <q-input
            v-model="newClient.numero"
            label="Número do Endereço"
            outlined
            required
          />
          <q-input
            v-model="newClient.complemento"
            label="Complemento para o Número do Endereço"
            outlined
          />
          <q-input v-model="newClient.email" label="Email" outlined required />
          <div class="flex column justify-center">
            <div class="flex q-gutter-sm">
              <input
                type="checkbox"
                id="optante_simples_nacional"
                :checked="newClient.optante_simples_nacional === 'S'"
                @change="toggleField('optante_simples_nacional')"
              />
              <label for="optante_simples_nacional"
              >Indica se o Cliente / Fornecedor é Optante do Simples
                Nacional</label
              >
            </div>

            <div class="flex q-gutter-sm">
              <input
                type="checkbox"
                id="contribuinte"
                :checked="newClient.contribuinte === 'S'"
                @change="toggleField('contribuinte')"
              />
              <label for="contribuinte"
              >Indica se o cliente é contribuinte</label
              >
            </div>
          </div>
          <q-btn
            style="max-width: 200px"
            class="bg-primary text-white"
            type="submit"
            :disable="this.isLoadingNewClient"
            :loading="this.isLoadingNewClient"
          >Criar Cliente</q-btn
          >
        </form>
      </div>

      <q-select
        v-if="!loadingClientesOmie"
        v-model="clientePagador"
        label="Cliente pagador"
        outlined
        behavior="menu"
        use-input
        fill-input
        input-debounce="300"
        :options="filteredClientesOmie"
        @filter="filterClientesOmie"
        option-label="razao_social"
        option-value="codigo_cliente_integracao"
        @update:model-value="onClienteSelecionado"
        hide-selected
        color="primary"
        popup-content-style="color: #000"
        class="q-mt-sm"
      >
        <template v-slot:option="scope">
          <q-item v-bind="scope.itemProps">
            <q-item-section>
              <q-item-label class="text-subtitle1">{{
                  scope.opt.razao_social
                }}</q-item-label>
              <q-item-label caption>{{ scope.opt.cnpj_cpf }}</q-item-label>
            </q-item-section>
          </q-item>
        </template>

        <!-- ✅ Exibe o item selecionado no campo -->
        <template v-slot:selected-item="scope">
          <q-item>
            <q-item-section>
              <q-item-label class="text-subtitle1">{{
                  scope.opt.razao_social
                }}</q-item-label>
            </q-item-section>
          </q-item>
        </template>
      </q-select>
      <q-input
        v-model="newCobranca.data_vencimento"
        label="Data de Faturamento"
        outlined
        class="q-mt-sm"
        readonly
        mask="##/##/####"
      >
        <q-popup-proxy transition-show="scale" transition-hide="scale">
          <q-date
            v-model="newCobranca.data_vencimento"
            mask="DD/MM/YYYY"
            text-color="white"
            color="primary"
            dark
            locale="pt-br"
            :options="dateIsAfterToday"
          >
            <div class="row items-center justify-end">
              <q-btn v-close-popup label="Fechar" color="white" flat />
            </div>
          </q-date>
        </q-popup-proxy>
      </q-input>

      <div class="q-mt-md">
        <!-- Label com ícone de dica -->
        <div class="row items-center q-gutter-sm">
          <div class="text-subtitle1 text-primary">
            Email para receber cobrança
          </div>
          <q-icon
            name="info"
            size="20px"
            color="primary"
            class="cursor-pointer"
            :title="'Depois de digitar o email, pressione a tecla Enter para adicionar a lista de emails.'"
          />
        </div>

        <!-- Campo de input -->
        <q-input
          v-model="emailCobranca"
          label="Email para cobrança"
          outlined
          class="q-mt-sm"
          @keyup="handleKeyUp"
        />

        <!-- Lista de emails -->
        <div class="q-mt-sm">
          <q-list
            bordered
            class="bg-grey-1 rounded-borders flex"
            v-if="newCobranca.emails.length > 0"
          >
            <q-item
              v-for="(email, index) in newCobranca.emails"
              :key="index"
              class="q-px-md"
            >
              <q-item-section class="text-primary text-bold">{{
                  email
                }}</q-item-section>
              <q-item-section side>
                <q-btn
                  flat
                  round
                  icon="close"
                  size="sm"
                  color="negative"
                  @click="removeEmail(email)"
                />
              </q-item-section>
            </q-item>
          </q-list>
        </div>
      </div>
    </div>

    <div class="row justify-start q-gutter-sm">
      <q-btn
        label="Criar Cobrança"
        color="primary"
        @click="handleCreateNewCobranca"
        :disable="this.isLoadingNewCobranca || this.isLoadingNewBoleto"
        :loading="this.isLoadingNewCobranca"
      />
      <q-btn
        label="Criar Boleto"
        color="primary"
        @click="handleCreateNewBoleto"
        :disable="this.isLoadingNewBoleto || this.isLoadingNewCobranca"
        :loading="this.isLoadingNewBoleto"
      />
    </div>
  </q-page>
</template>

<script>
import OmieComponent from 'components/DashboardComponents/Components/Omie.vue';
function formatCnpjCpf(value) {
  const digits = value.replace(/\D/g, '');

  if (digits.length <= 11) {
    return digits
      .replace(/(\d{3})(\d)/, '$1.$2')
      .replace(/(\d{3})(\d)/, '$1.$2')
      .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
  } else {
    return digits
      .replace(/^(\d{2})(\d)/, '$1.$2')
      .replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3')
      .replace(/\.(\d{3})(\d)/, '.$1/$2')
      .replace(/(\d{4})(\d)/, '$1-$2');
  }
}
export default {
  name: 'FinanceiroPage',
  components: { OmieComponent },
  data() {
    return {
      components: {
        OmieComponent,
      },
      financeRows: [],
      machineRows: [],
      chargeRows: [],
      columns: [
        { name: 'nome', label: 'Nome', field: 'nome', align: 'left' },
        {
          name: 'dataHora',
          label: 'Data e Hora',
          field: 'dataHora',
          align: 'center',
        },
        {
          name: 'valorHora',
          label: 'Valor por hora',
          field: 'valorHora',
          align: 'center',
        },
        {
          name: 'franquia',
          label: 'Franquia Mínima',
          field: 'franquia',
          align: 'center',
        },
        {
          name: 'valorKm',
          label: 'Valor por km',
          field: 'valorKm',
          align: 'center',
        },
        {
          name: 'deslocamento',
          label: 'Deslocamento',
          field: 'deslocamento',
          align: 'center',
        },
        {
          name: 'duracao',
          label: 'Duração',
          field: 'duracao',
          align: 'center',
        },
        { name: 'actions', label: '', field: 'actions', align: 'center' },
      ],
      machineColumns: [
        {
          name: 'machine_name',
          label: 'Máquina',
          field: 'machine_name',
          align: 'left',
        },
        {
          name: 'category_name',
          label: 'Categoria',
          field: 'category_name',
          align: 'left',
        },
        { name: 'brand', label: 'Marca', field: 'brand', align: 'center' },
        {
          name: 'license_plate',
          label: 'Placa',
          field: 'license_plate',
          align: 'center',
        },
        {
          name: 'amount',
          label: 'Valor hora',
          field: 'amount',
          align: 'center',
        },
        {
          name: 'valorKm',
          label: 'Valor por km',
          field: 'valorKm',
          align: 'center',
        },
        {
          name: 'deslocamento',
          label: 'Deslocamento',
          field: 'deslocamento',
          align: 'center',
        },
        {
          name: 'duracao',
          label: 'Duração',
          field: 'duracao',
          align: 'center',
        },
        { name: 'actions', label: '', field: 'actions', align: 'center' },
      ],
      chargeColumns: [
        {
          name: 'charge_name',
          label: 'Encargo',
          field: 'charge_name',
          align: 'left',
        },
        {
          name: 'fee_amount',
          label: 'Valor (R$)',
          field: 'fee_amount',
          align: 'center',
        },
      ],
      acrescimo: '',
      motivoAcrescimo: '',
      desconto: '',
      motivoDesconto: '',
      newClient: {
        razao_social: '',
        nome_fantasia: '',
        cnpj_cpf: '',
        cep: '',
        numero: '',
        complemento: '',
        email: '',
        optante_simples_nacional: 'N',
        contribuinte: 'N',
      },
      isLoadingNewClient: false,
      isFormVisible: false,
      clientePagador: null,
      clientesOmie: [],
      filteredClientesOmie: [],
      loadingClientesOmie: false,
      emailCobranca: '',
      showListEmails: false,
      newCobranca: {
        data_vencimento: '',
        codigo_cliente_fornecedor: '',
        emails: [],
        description: 'Testando endpoint',
        valor_documento: 0,
      },
      isLoadingNewBoleto: false,
      isLoadingNewCobranca: false,
    };
  },
  watch: {
    clientePagador(newValue) {
      if (newValue) {
        this.newCobranca.codigo_cliente_fornecedor =
          newValue.codigo_cliente_omie;
      } else {
        this.newCobranca.codigo_cliente_fornecedor = '';
      }
    },
    total(newValue) {
      this.newCobranca.valor_documento = Number(newValue);
    },
  },
  computed: {
    formattedCnpjCpf() {
      return formatCnpjCpf(this.newClient.cnpj_cpf);
    },
    total() {
      const parseValor = (val) => {
        if (!val) return 0;
        if (typeof val === 'number') return val;
        return Number(val.toString().replace(/\./g, '').replace(',', '.')) || 0;
      };

      const operadorBase = this.financeRows.reduce((sum, op) => {
        const valor = parseValor(op.valorHora.replace('R$ ', ''));
        const horas = parseInt(op.duracao) || 0;
        const valorKm = parseValor(op.valorKm.replace('R$ ', ''));
        const km = parseInt(op.deslocamento) || 0;
        return sum + valor * horas + valorKm * km;
      }, 0);

      const maquinasTotal = this.machineRows.reduce((sum, ma) => {
        const valorHora = parseValor(ma.price_per_hour);
        const duracao = parseInt(ma.duracao) || 0;
        const valorKm = parseValor(ma.price_per_distance);
        const km = parseInt(ma.deslocamento) || 0;
        return sum + valorHora * duracao + valorKm * km;
      }, 0);

      const encargosTotal = this.chargeRows.reduce((sum, ch) => {
        return sum + parseValor(ch.fee_amount);
      }, 0);

      const acres = parseValor(this.acrescimo);
      const desc = parseValor(this.desconto);

      return (
        operadorBase +
        maquinasTotal +
        encargosTotal +
        acres -
        desc
      ).toFixed(2);
    },
  },
  methods: {
    openDataHoraPopup(row) {
      if (!row.dataRange) row.dataRange = { from: '', to: '' };
      this.$set(row, 'showDataHora', true);
    },
    dateIsAfterToday(date) {
      const today = new Date();
      const [year, month, day] = date.split('/').map(Number);

      const selectedDate = new Date(year, month - 1, day);

      // Zera horas para comparar só a data
      today.setHours(0, 0, 0, 0);

      return selectedDate >= today;
    },
    onInputCnpjCpf(val) {
      this.newClient.cnpj_cpf = val.replace(/\D/g, '');
    },
    onCep(val) {
      this.newClient.cep = val.replace(/\D/g, '');
    },
    toggleField(field) {
      this.newClient[field] = this.newClient[field] === 'S' ? 'N' : 'S';
    },
    handleKeyUp(event) {
      const email = this.emailCobranca?.trim();

      // Verifica se a tecla foi Enter ou Espaço
      if ((event.key === 'Enter' || event.key === ' ') && email) {
        // Verifica se já não está na lista e se tem "@" e "."
        if (
          !this.newCobranca.emails.includes(email) &&
          this.validateEmail(email)
        ) {
          this.newCobranca.emails.push(email);
        }

        // Limpa o campo
        this.emailCobranca = '';
      }
    },
    validateEmail(email) {
      // Validação básica de email
      const re = /\S+@\S+\.\S+/;
      return re.test(email);
    },
    removeEmail(email) {
      this.newCobranca.emails = this.newCobranca.emails.filter(
        (e) => e !== email
      );
    },
    onClienteSelecionado(cliente) {
      this.clientePagador = cliente;
    },
    filterClientesOmie(val, update) {
      if (val === '') {
        update(() => {
          this.filteredClientesOmie = this.clientesOmie;
        });
        return;
      }

      update(() => {
        const filtro = val.toLowerCase();
        this.filteredClientesOmie = this.clientesOmie.filter((cliente) =>
          cliente.razao_social.toLowerCase().includes(filtro)
        );
      });
    },

    async fetchClientsOmie() {
      try {
        this.loadingClientesOmie = true;
        const response = await fetch(
          'http://localhost:5510/v1/omie/client/list/'
        );

        if (!response.ok) throw new Error('Erro ao buscar clientes Omie');

        const data = await response.json();
        this.clientesOmie = data.clients;
        this.filteredClientesOmie = data.clients;
      } catch (error) {
        console.error(error);
        this.$q.notify({
          color: 'red-5',
          textColor: 'white',
          icon: 'error',
          message: 'Erro ao buscar clientes Omie.',
        });
      } finally {
        this.loadingClientesOmie = false;
      }
    },
    async handleCreateNewClient() {
      try {
        this.isLoadingNewClient = true;
        const response = await fetch(
          'http://localhost:5510/v1/omie/client/new/',
          {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify(this.newClient),
          }
        );
        if (!response.ok) {
          if (response.status === 409) {
            this.$q.notify({
              color: 'orange-5',
              textColor: 'white',
              icon: 'warning',
              message: 'Cliente já cadastrado.',
            });
          } else {
            this.$q.notify({
              color: 'red-5',
              textColor: 'white',
              icon: 'warning',
              message: 'Error ao cadastrar. Tente novamente mais tarde.',
            });
          }
        }

        const result = await response.json();
        const createdClient = result.created_client;

        const newClient = {
          ...this.newClient,
          codigo_cliente_integracao: createdClient.codigo_cliente_integracao,
          codigo_cliente_omie: createdClient.codigo_cliente_omie,
        };

        this.clientesOmie.push(newClient);

        this.newClient = {};
        this.isFormVisible = false;

        this.$q.notify({
          color: 'green-5',
          textColor: 'white',
          icon: 'check_circle',
          message: 'Cliente cadastrado com sucesso.',
        });
      } catch (error) {
        console.error(error);
      } finally {
        this.isLoadingNewClient = false;
      }
    },
    async handleCreateNewCobranca() {
      try {
        this.isLoadingNewCobranca = true;

        if (!this.newCobranca.codigo_cliente_fornecedor) {
          this.$q.notify({
            color: 'red-5',
            textColor: 'white',
            icon: 'warning',
            message: 'Selecione um cliente para a cobrança!',
          });
          return;
        }

        if (!this.newCobranca.data_vencimento) {
          this.$q.notify({
            color: 'red-5',
            textColor: 'white',
            icon: 'warning',
            message: 'Selecione uma data para a cobrança!',
          });
          return;
        }

        if (this.newCobranca.valor_documento <= 0) {
          this.$q.notify({
            color: 'red-5',
            textColor: 'white',
            icon: 'warning',
            message: 'Valor do documento deve ser maior que zero!',
          });
          return;
        }

        const response = await fetch(
          'http://localhost:5510/v1/omie/cobranca/new/',
          {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify(this.newCobranca),
          }
        );

        if (!response.ok) {
          this.$q.notify({
            color: 'red-5',
            textColor: 'white',
            icon: 'warning',
            message: 'Error ao criar cobrança! Tente novamente mais tarde.',
          });

          return;
        }

        this.$q.notify({
          color: 'green-5',
          textColor: 'white',
          icon: 'check_circle',
          message: 'Cobrança cadastrado com sucesso.',
        });
      } catch (error) {
        console.error(error);
      } finally {
        this.isLoadingNewCobranca = false;
      }
    },
    async handleCreateNewBoleto() {
      try {
        this.isLoadingNewBoleto = true;

        if (!this.newCobranca.codigo_cliente_fornecedor) {
          this.$q.notify({
            color: 'red-5',
            textColor: 'white',
            icon: 'warning',
            message: 'Selecione um cliente para o boleto!',
          });
          return;
        }

        if (!this.newCobranca.data_vencimento) {
          this.$q.notify({
            color: 'red-5',
            textColor: 'white',
            icon: 'warning',
            message: 'Selecione uma data para o boleto!',
          });
          return;
        }

        if (this.newCobranca.valor_documento <= 0) {
          this.$q.notify({
            color: 'red-5',
            textColor: 'white',
            icon: 'warning',
            message: 'Valor do documento deve ser maior que zero!',
          });
          return;
        }

        const response = await fetch(
          'http://localhost:5510/v1/omie/boleto/new/',
          {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify(this.newCobranca),
          }
        );

        if (!response.ok) {
          this.$q.notify({
            color: 'red-5',
            textColor: 'white',
            icon: 'warning',
            message: 'Error ao criar boleto! Tente novamente mais tarde.',
          });

          return;
        }

        this.$q.notify({
          color: 'green-5',
          textColor: 'white',
          icon: 'check_circle',
          message: 'Boleto cadastrado com sucesso.',
        });
      } catch (error) {
        console.error(error);
      } finally {
        this.isLoadingNewBoleto = false;
      }
    },
    async fetchFinanceData() {
      try {
        const segments = this.$route.path.split('/');
        const projectUuid = segments[segments.length - 1];

        const response = await fetch(
          `http://localhost:5510/v1/budget/project/financial/${projectUuid}`,
          {
            headers: { token: localStorage.getItem('access_token') },
          }
        );

        if (!response.ok) throw new Error('Erro ao buscar dados financeiros');

        const data = await response.json();

        // Operadores
        this.financeRows = data.operator.map((op, index) => ({
          id: index + 1,
          nome: op.operator_name,
          horario: `${op.check_in} até ${op.check_out}`,
          valorHora: op.hourly_price ? `R$ ${op.hourly_price}` : 'R$ 0,00',
          franquia: `${op.minimum_rental_period}h`,
          valorKm: op.distance_amount ? `R$ ${op.distance_amount}` : 'R$ 0,00',
          deslocamento: '0',
          duracao: `${op.minimum_rental_period}h`,
          intervalos: [],
        }));

        // Máquinas
        this.machineRows = data.budget_machine.map((ma) => ({
          ...ma,
          duracao: parseInt(ma.minimum_rental_period) || 0,
          amount: `R$ ${ma.price_per_hour || '0,00'}`,
          valorKm: `R$ ${ma.price_per_distance || '0,00'}`,
          deslocamento: '0',
        }));

        // Encargos
        this.chargeRows = data.budget_service_charge;
      } catch (error) {
        console.error(error);
        this.$q.notify({
          color: 'red-5',
          textColor: 'white',
          icon: 'error',
          message: 'Erro ao buscar dados financeiros.',
        });
      }
    },
  },
  mounted() {
    this.fetchFinanceData();
    this.fetchClientsOmie();
  },
};
</script>

<style lang="css" scoped>
.reset-styles {
  padding: 0;
  margin: 0;
}
</style>
