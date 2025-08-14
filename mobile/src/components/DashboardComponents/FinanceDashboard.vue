<template>
  <q-page class="q-pa-md bg-white">
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
      <template v-slot:body="props">
        <q-tr :props="props">
          <q-td>{{ props.row.nome }}</q-td>
          <q-td>
            <div class="row items-center q-gutter-sm">
              <div>
                <div class="text-caption">
                  {{ props.row.dataInicio }} {{ props.row.horaInicio }}
                  <span v-if="props.row.dataFim && props.row.dataFim !== props.row.dataInicio">
                    até {{ props.row.dataFim }} {{ props.row.horaFim }} </span>
                  <span v-else-if="props.row.horaFim">
                    até {{ props.row.horaFim }} </span>
                </div>
              </div>
              <q-btn
                dense
                round
                icon="event"
                size="sm"
                @click="openDataHoraPopup(props.row)"
              />
              <q-popup-proxy
                v-model="props.row.showDataHora"
                transition-show="scale"
                transition-hide="scale"
                @hide="onDataHoraClose(props.row)"
              >
                <div class="q-pa-md bg-white" style="min-width: 280px;">
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
            </div>
          </q-td>
          <q-td>{{ props.row.valorHora }}</q-td>
          <q-td>{{ props.row.valorKm }}</q-td>

          <!-- Deslocamento -->
          <q-td style="max-width: 100px;">
            <q-input
              v-model.number="props.row.deslocamento"
              type="number"
              dense
              borderless
              min="0"
              :step="0.5"
              class="q-pa-none"
              input-class="no-spinner"
              style="width: 80px;"
            >
              <template v-slot:prepend>
                <q-btn flat dense icon="remove" size="sm"
                       @click="props.row.deslocamento = Math.max(0, (parseFloat(props.row.deslocamento) - 0.5).toFixed(1))" />
              </template>
              <template v-slot:append>
                <q-btn flat dense icon="add" size="sm"
                       @click="props.row.deslocamento = (parseFloat(props.row.deslocamento) + 0.5).toFixed(1)" />
              </template>
            </q-input>
          </q-td>

          <!-- Duração -->
          <q-td>{{ props.row.duracao }}</q-td>

          <!-- Adicionais (R$) -->
          <q-td>{{ props.row.extrasDisplay || 'R$ 0,00' }}</q-td>

          <q-td align="center">
            <q-btn
              dense
              round
              icon="add"
              size="sm"
              color="primary"
              @click="props.row.expandInterval = !props.row.expandInterval"
            />
          </q-td>
        </q-tr>

        <!-- Linha extra: Intervalo + Extras Aplicados -->
        <q-tr v-show="props.row.expandInterval">
          <q-td colspan="9">
            <div class="q-pa-sm bg-grey-2 rounded-borders">

              <!-- Intervalo -->
              <div class="row items-center q-gutter-sm">
                <q-btn
                  dense
                  round
                  icon="event"
                  size="sm"
                  color="secondary"
                  @click="openExtraDataHoraPopup(props.row)"
                />
                <span class="text-bold">Intervalo</span>
                <div class="q-ml-sm text-caption" v-if="props.row.extraData">
                  {{ props.row.extraData }}
                  <span v-if="props.row.extraHoraInicio">
                    {{ props.row.extraHoraInicio }}
                  </span>
                  <span v-if="props.row.extraHoraFim">
                    até {{ props.row.extraHoraFim }}
                  </span>
                </div>
              </div>

              <q-popup-proxy
                v-model="props.row.showExtraDataHora"
                transition-show="scale"
                transition-hide="scale"
                @hide="onExtraDataHoraClose(props.row)"
              >
                <div class="q-pa-md bg-white" style="min-width: 280px;">
                  <q-date
                    v-model="props.row.extraData"
                    mask="DD/MM/YYYY"
                    color="primary"
                    label="Data do intervalo"
                  />
                  <div class="row q-mt-sm q-gutter-sm">
                    <q-time
                      v-model="props.row.extraHoraInicio"
                      format24h
                      mask="HH:mm"
                      label="Hora Início"
                      color="primary"
                    />
                    <q-time
                      v-model="props.row.extraHoraFim"
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

              <!-- Extras aplicados -->
              <div class="q-mt-sm">
                <div class="text-bold text-primary q-mb-xs">
                  <q-icon name="percent" class="q-mr-xs" /> Adicionais aplicados
                </div>

                <div v-if="props.row.extrasSegments && props.row.extrasSegments.length" class="row q-gutter-sm">
                  <q-chip
                    v-for="seg in props.row.extrasSegments"
                    :key="seg._key"
                    color="primary"
                    text-color="white"
                    :icon="isExcluded(seg, 'operator', props.row) ? 'block' : 'info'"
                    :label="`${seg.name} • ${seg.displayMinutes} • ${seg.displayValue}`"
                    removable
                    @remove="toggleExcludeExtra(props.row, seg, 'operator')"
                  />
                </div>
                <div v-else class="text-caption">Sem adicionais aplicados (com base no turno).</div>
              </div>
            </div>
          </q-td>
        </q-tr>
      </template>
    </q-table>

    <!-- Máquinas -->
    <div class="text-h6 text-bold q-mb-sm text-primary">Máquinas no orçamento</div>
    <q-table
      :rows="machineRows"
      :columns="machineColumns"
      row-key="budget_machine_uuid"
      flat
      dense
      class="q-mb-md"
    >
      <template v-slot:body="props">
        <q-tr :props="props">
          <q-td>{{ props.row.machine_name }}</q-td>
          <q-td>{{ props.row.category_name }}</q-td>
          <q-td>
            <div class="row items-center q-gutter-sm">
              <div>
                <div class="text-caption">
                  {{ props.row.dataInicio }} {{ props.row.horaInicio }}
                  <span v-if="props.row.dataFim && props.row.dataFim !== props.row.dataInicio">
                    até {{ props.row.dataFim }} {{ props.row.horaFim }}</span>
                  <span v-else-if="props.row.horaFim">
                    até {{ props.row.horaFim }}
                  </span>
                </div>
              </div>
              <q-btn
                dense
                round
                icon="event"
                size="sm"
                @click="openDataHoraPopup(props.row)"
              />
              <q-popup-proxy
                v-model="props.row.showDataHora"
                transition-show="scale"
                transition-hide="scale"
                @hide="onDataHoraClose(props.row)"
              >
                <div class="q-pa-md bg-white" style="min-width: 280px;">
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
            </div>
          </q-td>
          <q-td>{{ props.row.brand }}</q-td>
          <q-td>{{ props.row.license_plate }}</q-td>
          <q-td>{{ props.row.amount }}</q-td>
          <q-td>{{ props.row.valorKm }}</q-td>

          <q-td style="max-width: 100px;">
            <q-input
              v-model.number="props.row.deslocamento"
              type="number"
              dense
              borderless
              min="0"
              :step="0.5"
              class="q-pa-none"
              input-class="no-spinner"
              style="width: 80px;"
            >
              <template v-slot:prepend>
                <q-btn
                  flat
                  dense
                  icon="remove"
                  size="sm"
                  @click="props.row.deslocamento = Math.max(0, (parseFloat(props.row.deslocamento) - 0.5).toFixed(1))"
                />
              </template>
              <template v-slot:append>
                <q-btn
                  flat
                  dense
                  icon="add"
                  size="sm"
                  @click="props.row.deslocamento = (parseFloat(props.row.deslocamento) + 0.5).toFixed(1)"
                />
              </template>
            </q-input>
          </q-td>

          <q-td>{{ props.row.duracao }}</q-td>
          <q-td>{{ props.row.extrasDisplay || 'R$ 0,00' }}</q-td>

          <q-td align="center">
            <q-btn
              dense
              round
              icon="add"
              size="sm"
              color="primary"
              @click="props.row.expandInterval = !props.row.expandInterval"
            />
          </q-td>
        </q-tr>

        <!-- Linha extra: Intervalo + Extras Aplicados -->
        <q-tr v-show="props.row.expandInterval">
          <q-td colspan="10">
            <div class="q-pa-sm bg-grey-2 rounded-borders">
              <div class="row items-center q-gutter-sm">
                <q-btn
                  dense
                  round
                  icon="event"
                  size="sm"
                  color="secondary"
                  @click="openExtraDataHoraPopup(props.row)"
                />
                <span class="text-bold">Intervalo</span>
                <div class="q-ml-sm text-caption" v-if="props.row.extraData">
                  {{ props.row.extraData }}
                  <span v-if="props.row.extraHoraInicio">
                    {{ props.row.extraHoraInicio }}
                  </span>
                  <span v-if="props.row.extraHoraFim">
                    até {{ props.row.extraHoraFim }}
                  </span>
                </div>
              </div>

              <q-popup-proxy
                v-model="props.row.showExtraDataHora"
                transition-show="scale"
                transition-hide="scale"
                @hide="onExtraDataHoraClose(props.row)"
              >
                <div class="q-pa-md bg-white" style="min-width: 280px;">
                  <q-date
                    v-model="props.row.extraData"
                    mask="DD/MM/YYYY"
                    color="primary"
                    label="Data do intervalo"
                  />
                  <div class="row q-mt-sm q-gutter-sm">
                    <q-time
                      v-model="props.row.extraHoraInicio"
                      format24h
                      mask="HH:mm"
                      label="Hora Início"
                      color="primary"
                    />
                    <q-time
                      v-model="props.row.extraHoraFim"
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

              <!-- Extras aplicados -->
              <div class="q-mt-sm">
                <div class="text-bold text-primary q-mb-xs">
                  <q-icon name="percent" class="q-mr-xs" /> Adicionais aplicados
                </div>

                <div v-if="props.row.extrasSegments && props.row.extrasSegments.length" class="row q-gutter-sm">
                  <q-chip
                    v-for="seg in props.row.extrasSegments"
                    :key="seg._key"
                    color="primary"
                    text-color="white"
                    :icon="isExcluded(seg, 'machine', props.row) ? 'block' : 'info'"
                    :label="`${seg.name} • ${seg.displayMinutes} • ${seg.displayValue}`"
                    removable
                    @remove="toggleExcludeExtra(props.row, seg, 'machine')"
                  />
                </div>
                <div v-else class="text-caption">Sem adicionais aplicados neste intervalo.</div>
              </div>
            </div>
          </q-td>
        </q-tr>
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
        <q-input v-model="extra" label="Acréscimo (R$)" outlined />
        <q-input
          v-model="extra_description"
          label="Motivo do acréscimo"
          outlined
          class="q-mt-sm"
        />
      </div>
      <div class="col">
        <q-input v-model="discount" label="Desconto (R$)" outlined />
        <q-input
          v-model="discount_description"
          label="Motivo do discount"
          outlined
          class="q-mt-sm"
        />
      </div>
    </div>

    <div class="text-h6 text-right q-mb-md">
      Total: <strong>R$ {{ total }}</strong>
    </div>
    <div class="row justify-end">
      <q-btn
        label="Salvar Pagamento"
        color="primary"
        icon="save"
        class="q-mt-md"
        @click="enviarPagamento"
      />
    </div>

    <OmieComponent />
  </q-page>
</template>

<script>
import OmieComponent from 'components/DashboardComponents/Components/Omie.vue';

export default {
  name: 'FinanceiroPage',
  components: { OmieComponent },
  data() {
    return {
      budgetUuid: '',
      financeRows: [],
      machineRows: [],
      chargeRows: [],
      // <- sem underscore para passar no eslint
      operatorsRaw: [],
      machinesRaw: [],
      extra: '',
      extra_description: '',
      discount: '',
      discount_description: '',
      columns: [
        { name: 'nome', label: 'Nome', field: 'nome', align: 'left' },
        { name: 'dataHora', label: 'Data e Hora', field: 'dataHora', align: 'center' },
        { name: 'valorHora', label: 'Valor por hora', field: 'valorHora', align: 'center' },
        { name: 'valorKm', label: 'Valor por km', field: 'valorKm', align: 'center' },
        { name: 'deslocamento', label: 'Deslocamento', field: 'deslocamento', align: 'center' },
        { name: 'duracao', label: 'Duração', field: 'duracao', align: 'center' },
        { name: 'extras', label: 'Adicionais (R$)', field: 'extras', align: 'center' },
        { name: 'actions', label: '', field: 'actions', align: 'center' }
      ],
      machineColumns: [
        { name: 'machine_name', label: 'Máquina', field: 'machine_name', align: 'left' },
        { name: 'category_name', label: 'Categoria', field: 'category_name', align: 'left' },
        { name: 'dataHora', label: 'Data e Hora', field: 'dataHora', align: 'center' },
        { name: 'brand', label: 'Marca', field: 'brand', align: 'center' },
        { name: 'license_plate', label: 'Placa', field: 'license_plate', align: 'center' },
        { name: 'amount', label: 'Valor hora', field: 'amount', align: 'center' },
        { name: 'valorKm', label: 'Valor por km', field: 'valorKm', align: 'center' },
        { name: 'deslocamento', label: 'Deslocamento', field: 'deslocamento', align: 'center' },
        { name: 'duracao', label: 'Duração', field: 'duracao', align: 'center' },
        { name: 'extras', label: 'Adicionais (R$)', field: 'extras', align: 'center' },
        { name: 'actions', label: '', field: 'actions', align: 'center' }
      ],
      chargeColumns: [
        { name: 'charge_name', label: 'Encargo', field: 'charge_name', align: 'left' },
        { name: 'fee_amount', label: 'Valor (R$)', field: 'fee_amount', align: 'center' }
      ]
    };
  },
  computed: {
    total() {
      const parseValor = (val) => {
        if (!val) return 0;
        if (typeof val === 'number') return val;
        return Number(val.toString().replace(/\./g, '').replace(',', '.')) || 0;
      };

      const operadorBase = this.financeRows.reduce((sum, op) => {
        const valor = parseValor((op.valorHora || 'R$ 0,00').replace('R$ ', ''));
        const horas = parseFloat(op.duracao) || 0;
        const valorKm = parseValor((op.valorKm || 'R$ 0,00').replace('R$ ', ''));
        const km = parseFloat(op.deslocamento) || 0;
        return sum + valor * horas + valorKm * km;
      }, 0);

      const maquinasTotal = this.machineRows.reduce((sum, ma) => {
        const valorHora = parseValor(String(ma.budget_machine_price_per_hour || '0').replace('R$ ', ''));
        const duracao = parseFloat(ma.duracao) || 0;
        const valorKm = parseValor(String(ma.budget_machine_price_per_distance || '0').replace('R$ ', ''));
        const km = parseFloat(ma.deslocamento) || 0;
        return sum + valorHora * duracao + valorKm * km;
      }, 0);

      const encargosTotal = this.chargeRows.reduce((sum, ch) => {
        return sum + parseValor(ch.fee_amount);
      }, 0);

      const acres = parseValor(this.extra);
      const desc = parseValor(this.discount);

      const extrasOperadores = this.financeRows.reduce((s, r) => s + (Number(r.extras) || 0), 0);
      const extrasMaquinas   = this.machineRows.reduce((s, r) => s + (Number(r.extras) || 0), 0);

      return (operadorBase + maquinasTotal + encargosTotal + acres - desc + extrasOperadores + extrasMaquinas).toFixed(2);
    }
  },
  methods: {
    // ===== Helpers de data/hora e sobreposição =====
    parseBrDateTime(dtStr) {
      if (!dtStr) return null;
      const [datePart, timePart] = dtStr.split(' ');
      const [dd, mm, yyyy] = (datePart || '').split('/').map(Number);
      if (!yyyy) return null;
      const [HH, MM] = (timePart || '00:00').split(':').map(Number);
      return new Date(yyyy, (mm || 1) - 1, dd || 1, HH || 0, MM || 0, 0, 0);
    },
    timeToMinutes(hhmm) {
      if (!hhmm) return null;
      const [h, m] = hhmm.split(':').map(Number);
      return h * 60 + (m || 0);
    },
    combineDateAndTime(baseDate, hhmm) {
      const mins = this.timeToMinutes(hhmm);
      if (mins == null) return null;
      const d = new Date(baseDate.getFullYear(), baseDate.getMonth(), baseDate.getDate());
      d.setHours(Math.floor(mins / 60), mins % 60, 0, 0);
      return d;
    },
    clampOverlap(startA, endA, startB, endB) {
      const start = new Date(Math.max(startA.getTime(), startB.getTime()));
      const end = new Date(Math.min(endA.getTime(), endB.getTime()));
      const minutes = Math.max(0, (end - start) / 60000);
      return { minutes, start, end };
    },
    isDateInAdditionalWindow(dayDate, add) {
      if (add.holiday_date) {
        const [y, m, d] = add.holiday_date.split('-').map(Number);
        const hd = new Date(y, (m || 1) - 1, d || 1);
        return (
          dayDate.getFullYear() === hd.getFullYear() &&
          dayDate.getMonth() === hd.getMonth() &&
          dayDate.getDate() === hd.getDate()
        );
      }
      if (add.start_date || add.end_date) {
        const s = add.start_date
          ? new Date(...add.start_date.split('-').map(Number).map((v,i)=> i===1? v-1 : v))
          : new Date(dayDate.getFullYear(), dayDate.getMonth(), dayDate.getDate());
        const e = add.end_date
          ? new Date(...add.end_date.split('-').map(Number).map((v,i)=> i===1? v-1 : v))
          : new Date(dayDate.getFullYear(), dayDate.getMonth(), dayDate.getDate());
        e.setHours(23,59,59,999);
        return dayDate >= s && dayDate <= e;
      }
      if (add.week_days) {
        const set = new Set(add.week_days.split(',').map(s => Number(s.trim())));
        return set.has(dayDate.getDay()); // 0..6
      }
      return true;
    },
    evaluateAdditionalsForShift(shiftStartStr, shiftEndStr, additionals) {
      const start = this.parseBrDateTime(shiftStartStr);
      const end   = this.parseBrDateTime(shiftEndStr);
      if (!start || !end || end <= start) return { segments: [], minutes: 0 };

      const segments = [];

      // percorre dia a dia do turno (máx. 31 dias por segurança)
      let dayStart = new Date(start.getFullYear(), start.getMonth(), start.getDate());
      for (let i = 0; i < 31 && dayStart <= end; i++) {
        for (const add of (additionals || [])) {
          // valida se o adicional vale nesse dia (semana/feriado/período)
          if (!this.isDateInAdditionalWindow(dayStart, add)) continue;

          const aStart = this.combineDateAndTime(dayStart, add.start_time || '00:00');
          let   aEnd   = this.combineDateAndTime(dayStart, add.end_time   || '23:59');

          // janela que cruza meia-noite (ex.: 22:00→05:00)
          if (aStart && aEnd && aEnd <= aStart) aEnd = new Date(aEnd.getTime() + 24*60*60000);

          // sobreposição entre turno e a janela deste dia
          const ov = this.clampOverlap(start, end, aStart || start, aEnd || end);
          if (ov.minutes > 0) {
            segments.push({
              name: add.name,
              rate: Number(add.rate) || 0,
              minutes: ov.minutes,
              from: ov.start,
              to: ov.end,
              machine_additional_uuid: add.machine_additional_uuid || null,
              operator_additional_uuid: add.operator_additional_uuid || null
            });
          }
        }
        // próximo dia (meia-noite do dia seguinte)
        dayStart = new Date(dayStart.getFullYear(), dayStart.getMonth(), dayStart.getDate() + 1);
      }

      // ordenar por início (só visual)
      segments.sort((a, b) => a.from - b.from);
      const minutes = Math.round(segments.reduce((acc, s) => acc + s.minutes, 0));
      return { segments, minutes };
    },
    formatBRL(n) {
      const v = Number(n || 0);
      return `R$ ${v.toFixed(2).replace('.', ',')}`;
    },

    // ===== Cálculo de duração =====
    calcularDuracao(row) {
      const parseDataHora = (data, hora) => {
        if (!data || !hora) return null;
        const [dia, mes, ano] = data.split('/');
        const [h, m] = hora.split(':');
        return new Date(`${ano}-${mes}-${dia}T${h}:${m}:00`);
      };

      if (!row.dataInicio || !row.horaInicio || !row.dataFim || !row.horaFim) {
        row.duracao = '0h';
        row._duracaoMin = 0;
        return;
      }

      const inicio = parseDataHora(row.dataInicio, row.horaInicio);
      const fim    = parseDataHora(row.dataFim, row.horaFim);
      if (!inicio || !fim || fim <= inicio) {
        row.duracao = '0h';
        row._duracaoMin = 0;
        return;
      }

      let diffMs = fim - inicio;

      if (row.extraData && row.extraHoraInicio && row.extraHoraFim) {
        const bStart = parseDataHora(row.extraData, row.extraHoraInicio);
        const bEnd   = parseDataHora(row.extraData, row.extraHoraFim);
        if (bStart && bEnd && bEnd > bStart) diffMs -= (bEnd - bStart);
      }

      const mins = Math.max(0, Math.round(diffMs / 60000));
      row._duracaoMin = mins;
      row.duracao = `${(mins/60).toFixed(2)}h`;
    },

    // ===== Regras de extras, exclusão e recomputação =====
    isExcluded(seg, type, row) {
      const arr = type === 'operator' ? (row.excludedOperatorExtras || []) : (row.excludedMachineExtras || []);
      const key = seg.operator_additional_uuid || seg.machine_additional_uuid || `${seg.name}-${seg.rate}-${seg.minutes}`;
      return arr.includes(key);
    },
    toggleExcludeExtra(row, seg, type) {
      const key = seg.operator_additional_uuid || seg.machine_additional_uuid || `${seg.name}-${seg.rate}-${seg.minutes}`;
      const field = type === 'operator' ? 'excludedOperatorExtras' : 'excludedMachineExtras';
      if (!row[field]) this.$set(row, field, []);
      const idx = row[field].indexOf(key);
      if (idx >= 0) row[field].splice(idx, 1);
      else row[field].push(key);

      if (type === 'operator') {
        const raw = this.operatorsRaw.find(o => o.budget_machine_operator_uuid === row.budget_machine_operator_uuid) || {};
        this.calcularExtrasOperador(row, raw);
      } else {
        const raw = this.machinesRaw.find(m => m.budget_machine_uuid === row.budget_machine_uuid) || {};
        this.calcularExtrasMaquina(row, raw);
      }
    },

    calcularExtrasOperador(row, rawOp) {
      const startStr = row.dataInicio && row.horaInicio ? `${row.dataInicio} ${row.horaInicio}` : null;
      const endStr   = row.dataFim   && row.horaFim    ? `${row.dataFim} ${row.horaFim}`       : null;
      if (!startStr || !endStr) {
        row.extras = 0; row.extrasDisplay = 'R$ 0,00'; row.extrasSegments = [];
        return;
      }

      const additionals = (rawOp && rawOp.additionals) || [];
      const { segments } = this.evaluateAdditionalsForShift(startStr, endStr, additionals);
      const valorHora = Number((row.valorHora || 'R$ 0,00').replace(/[R$\s]/g,'').replace(/\./g, '').replace(',', '.')) || 0;

      const excluded = row.excludedOperatorExtras || [];
      let total = 0;
      row.extrasSegments = segments.map((s, i) => {
        const key = s.operator_additional_uuid || `${s.name}-${s.rate}-${s.minutes}-op-${i}`;
        const value = valorHora * (s.rate / 100) * (s.minutes / 60);
        const displayMinutes = `${(s.minutes/60).toFixed(2)}h`;
        const displayValue = this.formatBRL(value);
        if (!excluded.includes(key)) total += value;
        return { ...s, _key: key, displayMinutes, displayValue };
      });

      row.extras = total;
      row.extrasDisplay = this.formatBRL(total);
    },

    calcularExtrasMaquina(row, rawMa) {
      const startStr = row.dataInicio && row.horaInicio ? `${row.dataInicio} ${row.horaInicio}` : null;
      const endStr   = row.dataFim   && row.horaFim    ? `${row.dataFim} ${row.horaFim}`       : null;
      if (!startStr || !endStr) {
        row.extras = 0; row.extrasDisplay = 'R$ 0,00'; row.extrasSegments = [];
        return;
      }

      const additionals = (rawMa && rawMa.additionals) || [];
      const { segments } = this.evaluateAdditionalsForShift(startStr, endStr, additionals);
      const valorHora = Number(String(rawMa?.budget_machine_price_per_hour || '0').replace(/\./g,'').replace(',', '.')) || 0;

      const excluded = row.excludedMachineExtras || [];
      let total = 0;
      row.extrasSegments = segments.map((s, i) => {
        const key = s.machine_additional_uuid || `${s.name}-${s.rate}-${s.minutes}-ma-${i}`;
        const value = valorHora * (s.rate / 100) * (s.minutes / 60);
        const displayMinutes = `${(s.minutes/60).toFixed(2)}h`;
        const displayValue = this.formatBRL(value);
        if (!excluded.includes(key)) total += value;
        return { ...s, _key: key, displayMinutes, displayValue };
      });

      row.extras = total;
      row.extrasDisplay = this.formatBRL(total);
    },

    // ===== Popups =====
    openDataHoraPopup(row) {
      if (!row.dataRange) {
        this.$set(row, 'dataRange', { from: '', to: '' });
      }
      if (row.dataInicio && row.dataFim) {
        row.dataRange.from = row.dataInicio;
        row.dataRange.to = row.dataFim;
      } else if (row.dataInicio && !row.dataFim) {
        row.dataRange.from = row.dataInicio;
        row.dataRange.to = '';
      } else {
        row.dataRange.from = '';
        row.dataRange.to = '';
      }
      this.$set(row, 'showDataHora', true);
    },
    onDataHoraClose(row) {
      const range = row.dataRange;

      if (typeof range === 'string') {
        row.dataInicio = range;
        row.dataFim = range;
      } else if (typeof range === 'object' && range !== null) {
        row.dataInicio = range.from || '';
        row.dataFim = range.to || range.from || '';
      }

      if (!row.horaInicio) row.horaInicio = '00:00';
      if (!row.horaFim) row.horaFim = '';

      this.calcularDuracao(row);

      if (row.budget_machine_operator_uuid) {
        const raw = this.operatorsRaw.find(o => o.budget_machine_operator_uuid === row.budget_machine_operator_uuid) || {};
        this.calcularExtrasOperador(row, raw);
      } else if (row.budget_machine_uuid) {
        const raw = this.machinesRaw.find(m => m.budget_machine_uuid === row.budget_machine_uuid) || {};
        this.calcularExtrasMaquina(row, raw);
      }
    },
    openExtraDataHoraPopup(row) {
      this.$set(row, 'showExtraDataHora', true);
    },
    onExtraDataHoraClose(row) {
      this.calcularDuracao(row);
      if (row.budget_machine_operator_uuid) {
        const raw = this.operatorsRaw.find(o => o.budget_machine_operator_uuid === row.budget_machine_operator_uuid) || {};
        this.calcularExtrasOperador(row, raw);
      } else if (row.budget_machine_uuid) {
        const raw = this.machinesRaw.find(m => m.budget_machine_uuid === row.budget_machine_uuid) || {};
        this.calcularExtrasMaquina(row, raw);
      }
    },

    // ===== Envio =====
    enviarPagamento() {
      const parseDateTime = (data, hora) => {
        if (!data || !hora) return null;
        const [dia, mes, ano] = data.split('/');
        return `${ano}-${mes}-${dia} ${hora}:00`;
      };

      const payload = {
        budget_uuid: this.budgetUuid,
        discount: this.discount ? parseFloat(this.discount.replace(',', '.')) * 100 : 0,
        extra: this.extra ? parseFloat(this.extra.replace(',', '.')) * 100 : 0,
        extra_description: this.extra_description || '',
        discount_description: this.discount_description || '',
        total_distance: Number(String(this.machineRows[0]?.deslocamento || 0).replace(',', '.')),
        budget_machine_list: this.machineRows.map(machine => ({
          budget_machine_uuid: machine.budget_machine_uuid,
          machine_start: parseDateTime(machine.dataInicio, machine.horaInicio),
          machine_end: parseDateTime(machine.dataFim, machine.horaFim),
          machine_break_start: parseDateTime(machine.extraData, machine.extraHoraInicio),
          machine_break_end: parseDateTime(machine.extraData, machine.extraHoraFim)
        })),
        operator_list: this.financeRows.map(op => ({
          budget_machine_operator_uuid: op.budget_machine_operator_uuid,
          operator_start: parseDateTime(op.dataInicio, op.horaInicio),
          operator_end: parseDateTime(op.dataFim, op.horaFim),
          operator_break_start: parseDateTime(op.extraData, op.extraHoraInicio),
          operator_break_end: parseDateTime(op.extraData, op.extraHoraFim)
        }))
      };

      fetch('https://fortis-api.55technology.com/v1/budget/payment/', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', token: localStorage.getItem('access_token') },
        body: JSON.stringify(payload)
      })
        .then(r => { if (!r.ok) throw new Error('Erro ao enviar pagamento'); return r.json(); })
        .then(() => this.$q.notify({ color: 'positive', message: 'Pagamento enviado com sucesso!' }))
        .catch(err => {
          console.error(err);
          this.$q.notify({ color: 'negative', message: 'Erro ao enviar pagamento.' });
        });
    },
    async fetchFinanceData() {
      try {
        const segments = this.$route.path.split('/');
        const projectUuid = segments[segments.length - 1];

        const response = await fetch(
          `https://fortis-api.55technology.com/v1/budget/project/financial/${projectUuid}`,
          { headers: { token: localStorage.getItem('access_token') } }
        );
        if (!response.ok) throw new Error('Erro ao buscar dados financeiros');

        const data = await response.json();
        this.budgetUuid = data.budget.budget_uuid;
        this.extra = data.budget.extra;
        this.extra_description = data.budget.extra_description;
        this.discount = data.budget.discount;
        this.discount_description = data.budget.discount_description;

        this.operatorsRaw = data.operator || [];
        this.machinesRaw  = data.budget_machine || [];

        this.financeRows = (data.operator || []).map((op, index) => {
          const [dataInicio, horaInicio] = (op.operator_start || '').split(' ');
          const [dataFim, horaFim] = (op.operator_end || '').split(' ');
          const [extraData1, extraHoraInicio] = (op.operator_break_start || '').split(' ');
          const [, extraHoraFim] = (op.operator_break_end || '').split(' ');

          const row = {
            id: index + 1,
            nome: op.operator_name,
            valorHora: op.hourly_price ? `R$ ${op.hourly_price}` : 'R$ 0,00',
            valorKm: op.distance_amount ? `R$ ${op.distance_amount}` : 'R$ 0,00',
            budget_machine_operator_uuid: op.budget_machine_operator_uuid,
            deslocamento: Number(String(data.budget.total_distance || '0').replace(',', '.')),
            duracao: '0',
            dataInicio: dataInicio ? dataInicio.split('-').reverse().join('/') : '',
            dataFim: dataFim ? dataFim.split('-').reverse().join('/') : '',
            horaInicio: horaInicio || '00:00',
            horaFim: horaFim || '',
            dataRange: { from: '', to: '' },
            showDataHora: false,
            expandInterval: false,
            extraData: extraData1 ? extraData1.split('-').reverse().join('/') : '',
            extraHoraInicio: extraHoraInicio || '',
            extraHoraFim: extraHoraFim || '',
            showExtraDataHora: false,
            extras: 0,
            extrasDisplay: 'R$ 0,00',
            extrasSegments: [],
            excludedOperatorExtras: [] // <- sem underscore
          };

          this.calcularDuracao(row);
          this.calcularExtrasOperador(row, op);

          return row;
        });

        this.machineRows = (data.budget_machine || []).map((ma, index) => {
          const [dataInicio, horaInicio] = (ma.machine_start || '').split(' ');
          const [dataFim, horaFim] = (ma.machine_end || '').split(' ');
          const [extraData1, extraHoraInicio] = (ma.machine_break_start || '').split(' ');
          const [, extraHoraFim] = (ma.machine_break_end || '').split(' ');

          const row = {
            ...ma,
            id: index + 1,
            duracao: '0',
            budget_machine_uuid: ma.budget_machine_uuid,
            amount: `R$ ${ma.budget_machine_price_per_hour || '0,00'}`,
            valorKm: `R$ ${ma.budget_machine_price_per_distance || '0,00'}`,
            deslocamento: Number(String(data.budget.total_distance || '0').replace(',', '.')),
            dataInicio: dataInicio ? dataInicio.split('-').reverse().join('/') : '',
            dataFim: dataFim ? dataFim.split('-').reverse().join('/') : '',
            horaInicio: horaInicio || '00:00',
            horaFim: horaFim || '',
            dataRange: { from: '', to: '' },
            showDataHora: false,
            expandInterval: false,
            extraData: extraData1 ? extraData1.split('-').reverse().join('/') : '',
            extraHoraInicio: extraHoraInicio || '',
            extraHoraFim: extraHoraFim || '',
            showExtraDataHora: false,
            extras: 0,
            extrasDisplay: 'R$ 0,00',
            extrasSegments: [],
            excludedMachineExtras: [] // <- sem underscore
          };

          this.calcularDuracao(row);
          this.calcularExtrasMaquina(row, ma);

          return row;
        });

        this.chargeRows = data.budget_service_charge;
      } catch (error) {
        console.error(error);
        this.$q.notify({
          color: 'red-5',
          textColor: 'white',
          icon: 'error',
          message: 'Erro ao buscar dados financeiros.'
        });
      }
    }
  },
  mounted() {
    this.fetchFinanceData();
  }
};
</script>

<style>
.no-spinner::-webkit-outer-spin-button,
.no-spinner::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
.no-spinner {
  -moz-appearance: textfield;
}
</style>
