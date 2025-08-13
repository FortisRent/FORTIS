<template>
  <q-page class="q-pa-md bg-white">
    <div class="text-h5 text-primary q-mb-md">Cadastro de Adicionais</div>

    <!-- Formulário -->
    <q-form @submit.prevent="handleSubmit" class="q-gutter-md q-mb-xl">
      <q-input v-model="form.name" label="Nome do adicional" outlined />

      <q-select
        v-model="form.tipo"
        label="Tipo de adicional"
        :options="tiposAdicional"
        emit-value
        map-options
        outlined
      />

      <!-- Tipo: Semana -->
      <template v-if="form.tipo === 'semana'">
        <q-select
          v-model="form.week_days"
          label="Dias da semana"
          :options="diasSemana"
          multiple
          emit-value
          map-options
          outlined
        />
        <div class="row q-col-gutter-md">
          <q-input v-model="form.start_time" label="Hora início" type="time" outlined />
          <q-input v-model="form.end_time" label="Hora fim" type="time" outlined />
        </div>
      </template>

      <!-- Tipo: Feriado -->
      <template v-else-if="form.tipo === 'feriado'">
        <q-input v-model="form.holiday_date" label="Data do feriado" outlined readonly>
          <template #append>
            <q-icon name="event" class="cursor-pointer">
              <q-popup-proxy cover>
                <q-date v-model="form.holiday_date" mask="YYYY-MM-DD" />
              </q-popup-proxy>
            </q-icon>
          </template>
        </q-input>

        <div class="row q-col-gutter-md">
          <q-input v-model="form.start_time" label="Hora início" type="time" outlined />
          <q-input v-model="form.end_time" label="Hora fim" type="time" outlined />
        </div>
      </template>

      <!-- Tipo: Período -->
      <template v-else-if="form.tipo === 'periodo'">
        <div class="row q-col-gutter-md">
          <q-input v-model="form.start_date" label="Data início" outlined readonly>
            <template #append>
              <q-icon name="event" class="cursor-pointer">
                <q-popup-proxy cover>
                  <q-date v-model="form.start_date" mask="YYYY-MM-DD" />
                </q-popup-proxy>
              </q-icon>
            </template>
          </q-input>

          <q-input v-model="form.end_date" label="Data fim" outlined readonly>
            <template #append>
              <q-icon name="event" class="cursor-pointer">
                <q-popup-proxy cover>
                  <q-date v-model="form.end_date" mask="YYYY-MM-DD" />
                </q-popup-proxy>
              </q-icon>
            </template>
          </q-input>
        </div>

        <div class="row q-col-gutter-md">
          <q-input v-model="form.start_time" label="Hora início" type="time" outlined />
          <q-input v-model="form.end_time" label="Hora fim" type="time" outlined />
        </div>
      </template>

      <q-input
        v-model.number="form.rate"
        label="Taxa (%)"
        type="number"
        min="0"
        max="100"
        suffix="%"
        outlined
      />

      <q-btn label="Salvar adicional" type="submit" color="primary" />
    </q-form>

    <!-- Tabela -->
    <div class="text-h6 text-primary q-mb-sm">Adicionais cadastrados</div>
    <q-table
      flat bordered
      :rows="adicionais"
      :columns="columns"
      row-key="uuid"
      dense
    >
      <template v-slot:body-cell-week_days="props">
        <q-td>{{ formatarDias(props.row.week_days) }}</q-td>
      </template>

      <template v-slot:body-cell-acoes="props">
        <q-td>
          <q-btn icon="delete" flat dense color="red" @click="removerAdicional(props.row.uuid)" />
        </q-td>
      </template>
    </q-table>
    <adicional-vinculo :adicionais="adicionais" />

  </q-page>
</template>

<script>
import AdicionalVinculo from '../DashboardComponents/Components/AdicionalVinculo.vue'

export default {
  components: {
    AdicionalVinculo
  },
  data () {
    return {
      adicionais: [],
      isLoading: false,
      form: {
        name: '',
        tipo: 'semana',
        week_days: [],
        holiday_date: null,
        start_date: null,
        end_date: null,
        start_time: '',
        end_time: '',
        rate: null
      },
      tiposAdicional: [
        { label: 'Por dia da semana', value: 'semana' },
        { label: 'Por feriado', value: 'feriado' },
        { label: 'Por período específico', value: 'periodo' }
      ],
      diasSemana: [
        { label: 'Domingo', value: 0 },
        { label: 'Segunda', value: 1 },
        { label: 'Terça', value: 2 },
        { label: 'Quarta', value: 3 },
        { label: 'Quinta', value: 4 },
        { label: 'Sexta', value: 5 },
        { label: 'Sábado', value: 6 }
      ],
      columns: [
        { name: 'name', label: 'Nome', field: 'name', align: 'left' },
        { name: 'week_days', label: 'Dias da semana', field: 'week_days', align: 'left' },
        { name: 'rate', label: 'Taxa (%)', field: 'rate', align: 'left' },
        { name: 'acoes', label: 'Ações', field: 'acoes', align: 'right' }
      ]
    }
  },

  mounted () {
    this.carregarAdicionais()
  },

  methods: {
    formatarDias (diasString) {
      if (!diasString) return '-'
      const dias = diasString.split(',').map(Number)
      const mapa = {
        0: 'Dom',
        1: 'Seg',
        2: 'Ter',
        3: 'Qua',
        4: 'Qui',
        5: 'Sex',
        6: 'Sáb'
      }
      return dias.map(d => mapa[d]).join(', ')
    },

    async carregarAdicionais () {
      try {
        const response = await fetch(
          `http://localhost:5510/v1/additional/type/company/${this.$route.params.company_uuid}`,
          {
            headers: { token: localStorage.getItem('access_token') }
          }
        )

        if (!response.ok) throw new Error('Erro na requisição')

        const data = await response.json()
        this.adicionais = data.additional_type_company
      } catch (error) {
        console.error('Erro ao buscar adicionais:', error)
        this.$q.notify({
          type: 'negative',
          message: 'Erro ao carregar adicionais.'
        })
      }
    },

    async handleSubmit () {
      const payload = {
        company_uuid: this.$route.params.company_uuid,
        name: this.form.name,
        rate: this.form.rate,
        week_days: this.form.tipo === 'semana' ? this.form.week_days.join(',') : null,
        holiday_date: this.form.tipo === 'feriado' ? this.form.holiday_date : null,
        start_date: this.form.tipo === 'periodo' ? this.form.start_date : null,
        end_date: this.form.tipo === 'periodo' ? this.form.end_date : null,
        start_time: this.form.start_time,
        end_time: this.form.end_time
      }

      try {
        const response = await fetch(`http://localhost:5510/v1/additional/type/`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            token: localStorage.getItem('access_token')
          },
          body: JSON.stringify(payload)
        })

        if (!response.ok) throw new Error('Erro ao salvar adicional')

        this.$q.notify({ type: 'positive', message: 'Adicional cadastrado com sucesso!' })
        this.resetForm()
        this.carregarAdicionais()
      } catch (error) {
        console.error(error)
        this.$q.notify({ type: 'negative', message: 'Erro ao salvar adicional.' })
      }
    },

    async removerAdicional (uuid) {
      try {
        const response = await fetch(`http://localhost:5510/v1/additional/type/${uuid}`, {
          method: 'DELETE',
          headers: {
            token: localStorage.getItem('access_token')
          }
        })

        if (!response.ok) throw new Error('Erro ao excluir adicional')

        this.$q.notify({ type: 'positive', message: 'Adicional removido com sucesso!' })
        this.carregarAdicionais()
      } catch (error) {
        console.error(error)
        this.$q.notify({ type: 'negative', message: 'Erro ao excluir adicional.' })
      }
    },

    resetForm () {
      this.form = {
        name: '',
        tipo: 'semana',
        week_days: [],
        holiday_date: null,
        start_date: null,
        end_date: null,
        start_time: '',
        end_time: '',
        rate: null
      }
    }
  }
}
</script>
