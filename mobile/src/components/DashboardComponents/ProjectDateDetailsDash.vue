<template>
  <q-page>
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
        Agendamento
      </div>
    </div>

    <q-separator class="bg-grey" />

    <div class="q-pa-md">
      <p class="text-h6 text-primary text-bold text-center" style="margin: 0">
        Responsável pelo pagamento do projeto
      </p>

      <div class="q-pa-md">
        <q-option-group
          v-model="state_payment_responsible"
          :options="[
            { label: 'Sim', value: 'sim' },
            { label: 'Não', value: 'nao' }
          ]"
          type="radio"
          color="primary"
          class="q-mt-sm flex justify-center text-black"
        />

        <div v-if="state_payment_responsible === 'nao'" class="q-mt-md">
          <q-input
            color="secondary"
            class="q-mt-sm"
            outlined
            v-model="payer_name"
            label="Nome do Responsável / Empresa Responsável"
          />
          <q-input
            color="secondary"
            class="q-mt-sm"
            outlined
            v-model="cnpj"
            label="CNPJ ou CPF"
          />
        </div>
      </div>

      <p class="text-primary text-bold text-h6 text-center q-pa-md">
        Escolha o intervalo de datas para visualizar a disponibilidade
      </p>

      <div class="q-pa-md">
        <!-- Seleção de data (um dia ou intervalo) -->
        <div class="q-mb-lg flex justify-center">
          <q-date
            v-model="dateRange"
            range
            emit-immediately
            color="secondary"
            mask="YYYY-MM-DD"
          />
        </div>

        <!-- Horários: início e fim -->
        <div class="q-mb-lg flex justify-center q-gutter-xl">
          <div class="column items-center">
            <div class="text-caption q-mb-xs text-primary">Hora inicial</div>
            <q-time
              v-model="hour_start"
              color="secondary"
              mask="HH:mm"
              format24h
              class="text-primary"
            />
          </div>
          <div class="column items-center">
            <div class="text-caption q-mb-xs text-primary">Hora final</div>
            <q-time
              v-model="hour_end"
              color="secondary"
              mask="HH:mm"
              format24h
              class="text-primary"
            />
          </div>
        </div>

        <!-- Badge com um dia ou intervalo -->
        <div v-if="normalized.from && hour_start && hour_end" class="text-center q-mb-md">
          <q-badge color="teal" class="q-pa-md">
            Agendamento:
            <template v-if="normalized.to && normalized.to !== normalized.from">
              De {{ normalized.from }} {{ hour_start }} até {{ normalized.to }} {{ hour_end }}
            </template>
            <template v-else>
              Dia {{ normalized.from }} das {{ hour_start }} às {{ hour_end }}
            </template>
          </q-badge>
        </div>

        <!-- Lista de máquinas disponíveis -->
        <div v-if="availableMachines.length > 0" class="q-mt-md">
          <p class="text-primary text-bold text-center">Máquinas disponíveis:</p>
          <q-list bordered separator>
            <q-item v-for="machine in availableMachines" :key="machine.machine_id">
              <q-item-section>
                <div class="text-primary">
                  <strong>Empresa:</strong> {{ machine.company_name }} <br />
                  <strong>Categoria:</strong> {{ machine.category_name }} <br />
                  <strong>Máquina:</strong> {{ machine.machine_name }} <br />
                  <strong>Status:</strong> {{ machine.status }}
                </div>
              </q-item-section>
            </q-item>
          </q-list>
          <div class="flex justify-center q-pa-md">
            <q-btn
              label="Confirmar Agendamento"
              color="green"
              class="text-white"
              @click="confirmBooking"
            />
          </div>
        </div>

        <div class="flex justify-between q-mt-sm">
          <q-btn @click="$router.go(-1)" label="Cancelar" class="text-primary" />
          <q-btn
            label="Consultar Disponibilidade"
            color="secondary"
            class="text-primary"
            @click="on_submit"
          />
        </div>
      </div>
    </div>
  </q-page>
</template>

<script>
export default {
  name: 'SchedulePage',
  data() {
    return {
      payer_name: '',
      cnpj: '',
      state_payment_responsible: null,

      // QDate pode retornar string/array/obj; normalizamos em computed
      dateRange: { from: null, to: null },

      // NOVOS campos de horário
      hour_start: null,
      hour_end: null,

      availableMachines: [],
      budget_proposal_uuid: this.$route.params.budget_proposal_uuid,
    };
  },
  computed: {
    // Normalizador central -> sempre retorna { from, to } coerente
    normalized() {
      return this.normalizeRange(this.dateRange);
    },
  },
  methods: {
    // --- Utilidades de data/tempo ---
    normalizeRange(range) {
      const ensureOrder = (a, b) => {
        if (!a && b) return { from: b, to: b };
        if (a && !b) return { from: a, to: a };
        if (!a && !b) return { from: null, to: null };
        return (b < a) ? { from: a, to: a } : { from: a, to: b };
      };

      if (!range) return { from: null, to: null };

      if (typeof range === 'string') {
        return { from: range, to: range };
      }

      if (Array.isArray(range)) {
        if (range.length === 0) return { from: null, to: null };
        if (range.length === 1) return { from: range[0], to: range[0] };
        return ensureOrder(range[0], range[1]);
      }

      const a = range.from || null;
      const b = range.to || null;
      return ensureOrder(a, b);
    },

    timeToMinutes(hhmm) {
      if (!hhmm) return null;
      const [h, m] = hhmm.split(':').map(Number);
      return h * 60 + m;
    },

    isEndAfterStart(start, end) {
      const s = this.timeToMinutes(start);
      const e = this.timeToMinutes(end);
      if (s == null || e == null) return false;
      return e > s;
    },

    // --- Requisições ---
    async checkAvailability() {
      try {
        const { from, to } = this.normalized;
        const start = this.hour_start;
        const end = this.hour_end;

        if (!from || !start || !end) {
          throw new Error('Selecione a data (ou intervalo) e os dois horários.');
        }

        // Se for o mesmo dia, exigir fim > início
        if ((!to || to === from) && !this.isEndAfterStart(start, end)) {
          throw new Error('No mesmo dia, a hora final deve ser maior que a inicial.');
        }

        // Mantemos o contrato do backend: usa hour_date como hora inicial.
        const response = await fetch('https://fortis-api.55technology.com/v1/budget/available/', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            token: localStorage.getItem('access_token'),
          },
          body: JSON.stringify({
            budget_uuid: this.$route.params.budget_uuid,
            expected_date: from,
            expected_date_end: to,
            hour_date: start, // compat: backend atual usa este campo
          }),
        });

        if (!response.ok) {
          throw new Error('Erro ao consultar disponibilidade.');
        }

        const data = await response.json();
        this.availableMachines = data.available_machines || [];
        this.$q.notify({
          color: 'green-4',
          textColor: 'white',
          icon: 'cloud_done',
          message: 'Máquinas Disponíveis',
        });
      } catch (error) {
        this.$q.notify({
          color: 'red-4',
          textColor: 'white',
          icon: 'error',
          message: error.message,
        });
      }
    },

    async confirmBooking() {
      try {
        const { from, to } = this.normalized;
        const start = this.hour_start;
        const end = this.hour_end;

        if (!from || !to || !start || !end) {
          throw new Error('Selecione a data (ou intervalo) e os horários antes de confirmar.');
        }

        if ((to === from) && !this.isEndAfterStart(start, end)) {
          throw new Error('No mesmo dia, a hora final deve ser maior que a inicial.');
        }

        const response = await fetch(
          `https://fortis-api.55technology.com/v1/budget/accept/${this.$route.params.budget_uuid}`,
          {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
              token: localStorage.getItem('access_token'),
            },
            body: JSON.stringify({
              budget_uuid: this.$route.params.budget_uuid,
              expected_date: `${from} ${start}`,
              expected_date_end: `${to} ${end}`,
              cnpj: this.cnpj,
              payer_name: this.payer_name,
            }),
          }
        );

        if (!response.ok) {
          throw new Error('Erro ao confirmar o agendamento.');
        }

        this.$q.notify({
          color: 'green-4',
          textColor: 'white',
          icon: 'cloud_done',
          message: 'Agendamento Concluído.',
        });

        this.$router.go(-1);
      } catch (error) {
        this.$q.notify({
          color: 'red-4',
          textColor: 'white',
          icon: 'error',
          message: error.message,
        });
      }
    },

    on_submit() {
      this.checkAvailability();
    },
  },
};
</script>
