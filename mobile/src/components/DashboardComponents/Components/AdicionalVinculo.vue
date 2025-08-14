<template>
  <div class="q-mt-xl">
    <div class="text-h6 text-primary q-mb-sm">Vincular adicionais</div>

    <q-form @submit.prevent="vincularAdicionais" class="q-gutter-md">

      <!-- Select de adicional -->
      <q-select
        v-model="selectedAdicional"
        :options="adicionais"
        option-label="name"
        option-value="uuid"
        label="Selecione o adicional"
        outlined
        dense
        emit-value
        map-options
      />

      <!-- Multiselect de máquinas -->
      <q-select
        v-model="selectedMaquinas"
        :options="maquinas"
        option-label="machine_name"
        option-value="machine_uuid"
        label="Máquinas"
        outlined
        dense
        multiple
        emit-value
        map-options
      />

      <!-- Multiselect de operadores -->
      <q-select
        v-model="selectedOperadores"
        :options="operadores"
        option-label="employee_name"
        option-value="operator_uuid"
        label="Colaboradores"
        outlined
        dense
        multiple
        emit-value
        map-options
      />

      <q-btn label="Vincular" type="submit" color="primary" :disable="!selectedAdicional" />
    </q-form>

    <!-- Chips de máquinas -->
    <div class="q-mt-lg">
      <div class="text-subtitle2 text-bold text-primary q-mb-sm">Máquinas</div>
      <div class="q-gutter-sm">
        <template v-for="machine in maquinas" :key="machine.machine_uuid">
          <q-chip
            v-for="add in machine.additionals"
            :key="add.machine_additional_uuid"
            :label="`${machine.machine_name} - ${add.name}`"
            color="primary"
            text-color="white"
            removable
            @remove="removerAdicionalMaquina(add.machine_additional_uuid)"
          />
        </template>
      </div>
    </div>

    <!-- Chips de operadores -->
    <div class="q-mt-lg">
      <div class="text-subtitle2 text-bold text-primary q-mb-sm">Colaboradores</div>
      <div class="q-gutter-sm">
        <template v-for="op in operadores" :key="op.operator_uuid">
          <q-chip
            v-for="add in op.additionals"
            :key="add.operator_additional_uuid"
            :label="`${op.employee_name} - ${add.name}`"
            color="secondary"
            text-color="white"
            removable
            @remove="removerAdicionalOperador(add.operator_additional_uuid)"
          />
        </template>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdicionalVinculo',
  props: {
    adicionais: {
      type: Array,
      required: true
    }
  },

  data () {
    return {
      selectedAdicional: null,
      selectedMaquinas: [],
      selectedOperadores: [],
      maquinas: [],
      operadores: []
    }
  },

  mounted () {
    this.carregarMaquinas()
    this.carregarOperadores()
  },

  methods: {
    async carregarMaquinas () {
      try {
        const response = await fetch(`https://fortis-api.55technology.com/v1/machine/additional/company/${this.$route.params.company_uuid}`, {
          headers: { token: localStorage.getItem('access_token') }
        })

        if (!response.ok) throw new Error('Erro ao buscar máquinas')
        const data = await response.json()
        this.maquinas = data.machines || []
      } catch (error) {
        console.error(error)
        this.$q.notify({ type: 'negative', message: 'Erro ao carregar máquinas' })
      }
    },

    async carregarOperadores () {
      try {
        const response = await fetch(`https://fortis-api.55technology.com/v1/operator/additional/company/${this.$route.params.company_uuid}`, {
          headers: { token: localStorage.getItem('access_token') }
        })

        if (!response.ok) throw new Error('Erro ao buscar operadores')
        const data = await response.json()
        this.operadores = data.operators || []
      } catch (error) {
        console.error(error)
        this.$q.notify({ type: 'negative', message: 'Erro ao carregar operadores' })
      }
    },

    async vincularAdicionais () {
      const adicionalUuid = this.selectedAdicional
      const token = localStorage.getItem('access_token')

      // Vincular máquinas
      for (const machineUuid of this.selectedMaquinas) {
        try {
          const response = await fetch('https://fortis-api.55technology.com/v1/machine/additional/', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              token
            },
            body: JSON.stringify({
              machine_uuid: machineUuid,
              additional_uuid: adicionalUuid
            })
          })

          if (!response.ok) throw new Error('Erro ao vincular máquina')
        } catch (error) {
          console.error(error)
          this.$q.notify({ type: 'negative', message: 'Erro ao vincular adicional à máquina.' })
        }
      }

      // Vincular operadores
      for (const operadorUuid of this.selectedOperadores) {
        try {
          const response = await fetch('https://fortis-api.55technology.com/v1/operator/additional/', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              token
            },
            body: JSON.stringify({
              operator_uuid: operadorUuid,
              additional_uuid: adicionalUuid
            })
          })

          if (!response.ok) throw new Error('Erro ao vincular operador')
        } catch (error) {
          console.error(error)
          this.$q.notify({ type: 'negative', message: 'Erro ao vincular adicional ao operador.' })
        }
      }

      this.$q.notify({ type: 'positive', message: 'Vínculos realizados com sucesso!' })
      this.selectedAdicional = null
      this.selectedMaquinas = []
      this.selectedOperadores = []

      // Recarrega após vínculo
      this.carregarMaquinas()
      this.carregarOperadores()
    },

    async removerAdicionalMaquina (machine_additional_uuid) {
      try {
        const response = await fetch(`https://fortis-api.55technology.com/v1/machine/additional/${machine_additional_uuid}`, {
          method: 'DELETE',
          headers: { token: localStorage.getItem('access_token') }
        })

        if (!response.ok) throw new Error('Erro ao remover adicional da máquina')

        this.$q.notify({ type: 'positive', message: 'Adicional removido da máquina.' })
        this.carregarMaquinas()
      } catch (error) {
        console.error(error)
        this.$q.notify({ type: 'negative', message: 'Erro ao remover adicional da máquina.' })
      }
    },

    async removerAdicionalOperador (operator_additional_uuid) {
      try {
        const response = await fetch(`https://fortis-api.55technology.com/v1/operator/additional/${operator_additional_uuid}`, {
          method: 'DELETE',
          headers: { token: localStorage.getItem('access_token') }
        })

        if (!response.ok) throw new Error('Erro ao remover adicional do operador')

        this.$q.notify({ type: 'positive', message: 'Adicional removido do operador.' })
        this.carregarOperadores()
      } catch (error) {
        console.error(error)
        this.$q.notify({ type: 'negative', message: 'Erro ao remover adicional do operador.' })
      }
    }
  }
}
</script>
