<template>
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

</template>
<script>
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
  name: 'OmieComponent',
  data() {
    return {
      financeRows: [],
      machineRows: [],
      chargeRows: [],
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
      this.newCobranca.codigo_cliente_fornecedor =
        newValue?.codigo_cliente_omie || '';
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
    dateIsAfterToday(date) {
      const today = new Date();
      const [year, month, day] = date.split('/').map(Number);
      const selectedDate = new Date(year, month - 1, day);
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
      if ((event.key === 'Enter' || event.key === ' ') && email) {
        if (
          !this.newCobranca.emails.includes(email) &&
          this.validateEmail(email)
        ) {
          this.newCobranca.emails.push(email);
        }
        this.emailCobranca = '';
      }
    },
    validateEmail(email) {
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
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(this.newClient),
          }
        );
        if (!response.ok) {
          const message =
            response.status === 409
              ? 'Cliente já cadastrado.'
              : 'Error ao cadastrar. Tente novamente mais tarde.';
          this.$q.notify({
            color: response.status === 409 ? 'orange-5' : 'red-5',
            textColor: 'white',
            icon: 'warning',
            message,
          });
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
          this.notifyError('Selecione um cliente para a cobrança!');
          return;
        }

        if (!this.newCobranca.data_vencimento) {
          this.notifyError('Selecione uma data para a cobrança!');
          return;
        }

        if (this.newCobranca.valor_documento <= 0) {
          this.notifyError('Valor do documento deve ser maior que zero!');
          return;
        }

        const response = await fetch(
          'http://localhost:5510/v1/omie/cobranca/new/',
          {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(this.newCobranca),
          }
        );

        if (!response.ok) {
          this.notifyError('Error ao criar cobrança! Tente novamente mais tarde.');
          return;
        }

        this.$q.notify({
          color: 'green-5',
          textColor: 'white',
          icon: 'check_circle',
          message: 'Cobrança cadastrada com sucesso.',
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
          this.notifyError('Selecione um cliente para o boleto!');
          return;
        }

        if (!this.newCobranca.data_vencimento) {
          this.notifyError('Selecione uma data para o boleto!');
          return;
        }

        if (this.newCobranca.valor_documento <= 0) {
          this.notifyError('Valor do documento deve ser maior que zero!');
          return;
        }

        const response = await fetch(
          'http://localhost:5510/v1/omie/boleto/new/',
          {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(this.newCobranca),
          }
        );

        if (!response.ok) {
          this.notifyError('Error ao criar boleto! Tente novamente mais tarde.');
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
    notifyError(message) {
      this.$q.notify({
        color: 'red-5',
        textColor: 'white',
        icon: 'warning',
        message,
      });
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

        this.financeRows = data.operator.map((op, index) => ({
          id: index + 1,
          nome: op.operator_name,
          horario: `${op.check_in} até ${op.check_out}`,
          valorHora: `R$ ${op.hourly_price || '0,00'}`,
          franquia: `${op.minimum_rental_period}h`,
          valorKm: `R$ ${op.distance_amount || '0,00'}`,
          deslocamento: '0',
          duracao: `${op.minimum_rental_period}h`,
          intervalos: [],
        }));

        this.machineRows = data.budget_machine.map((ma) => ({
          ...ma,
          duracao: parseInt(ma.minimum_rental_period) || 0,
          amount: `R$ ${ma.price_per_hour || '0,00'}`,
          valorKm: `R$ ${ma.price_per_distance || '0,00'}`,
          deslocamento: '0',
        }));

        this.chargeRows = data.budget_service_charge;
      } catch (error) {
        console.error(error);
        this.notifyError('Erro ao buscar dados financeiros.');
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
