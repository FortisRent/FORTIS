<template>
  <q-page class="bg-white q-pa-md">
    <!-- Header -->
    <div class="row items-center q-mb-sm">
      <q-btn flat round icon="chevron_left" class="text-primary" @click="$router.go(-1)" />
      <div class="text-h6 text-primary text-bold q-ml-sm">Ordem de Serviço</div>
    </div>
    <q-separator class="q-mb-md" />

    <q-form @submit.prevent="salvar" class="q-gutter-md">
      <!-- CONTRATANTE -->
      <q-card flat bordered>
        <q-card-section class="text-primary text-subtitle2 text-bold">CONTRATANTE</q-card-section>
        <q-separator />
        <q-card-section class="q-gutter-sm">
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-6">
              <q-input v-model="form.contratante.nome" label="Nome" outlined dense :rules="[req]" />
            </div>
            <div class="col-12 col-md-6">
              <q-input v-model="form.contratante.cnpjCpf" label="CNPJ/CPF" outlined dense
                       mask="##.###.###/####-##|###.###.###-##" :rules="[req]" />
            </div>

            <div class="col-12 col-md-8">
              <q-input v-model="form.contratante.endereco" label="Endereço" outlined dense />
            </div>
            <div class="col-6 col-md-2">
              <q-input v-model="form.contratante.cep" label="CEP" outlined dense mask="#####-###" />
            </div>
            <div class="col-6 col-md-2">
              <q-input v-model="form.contratante.estado" label="UF" outlined dense maxlength="2" />
            </div>

            <div class="col-6 col-md-4">
              <q-input v-model="form.contratante.municipio" label="Município" outlined dense />
            </div>
            <div class="col-6 col-md-4">
              <q-input v-model="form.contratante.fone" label="Fone" outlined dense
                       mask="(##) #####-####|(##) ####-####" />
            </div>
            <div class="col-6 col-md-2">
              <q-input v-model="form.contratante.contato" label="Contato" outlined dense />
            </div>
            <div class="col-6 col-md-2">
              <q-input v-model="form.contratante.email" label="E-mail" type="email" outlined dense />
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- DESCRIÇÃO -->
      <q-card flat bordered>
        <q-card-section class="text-primary text-subtitle2 text-bold">
          DESCRIÇÃO DO SERVIÇO / RECOMENDAÇÕES
        </q-card-section>
        <q-separator />
        <q-card-section>
          <q-input v-model="form.descricao" type="textarea" outlined dense autogrow />
        </q-card-section>
      </q-card>

      <!-- LOCAL DO SERVIÇO -->
      <q-card flat bordered>
        <q-card-section class="text-primary text-subtitle2 text-bold">LOCAL DO SERVIÇO</q-card-section>
        <q-separator />
        <q-card-section class="q-gutter-sm">
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-4">
              <q-input v-model="form.local.contatos" label="Contatos" outlined dense />
            </div>
            <div class="col-12 col-md-4">
              <q-input v-model="form.local.telefones" label="Telefones" outlined dense />
            </div>
            <div class="col-12 col-md-4">
              <q-input v-model="form.local.endereco" label="Endereço" outlined dense />
            </div>

            <div class="col-6 col-md-3">
              <q-input v-model="form.local.bairro" label="Bairro" outlined dense />
            </div>
            <div class="col-6 col-md-3">
              <q-input v-model="form.local.municipio" label="Município" outlined dense />
            </div>
            <div class="col-6 col-md-2">
              <q-input v-model="form.local.estado" label="UF" outlined dense maxlength="2" />
            </div>
            <div class="col-6 col-md-4">
              <q-input v-model="form.local.referencia" label="Ponto de Referência" outlined dense />
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- OPERADORES (mobile em cartões) -->
      <q-card flat bordered>
        <q-card-section class="row items-center">
          <div class="text-primary text-subtitle2 text-bold">COMPOSIÇÃO — OPERADORES</div>
          <q-space />
          <q-btn dense icon="add" color="primary" label="Adicionar" @click="adicionarLinha('operadores')" />
        </q-card-section>
        <q-separator />

        <q-card-section class="q-gutter-md">
          <q-slide-transition v-for="(row, idx) in form.operadores" :key="row.id">
            <q-card bordered flat class="q-pa-md os-card">
              <div class="row items-center q-mb-sm">
                <div class="text-subtitle2">Operador #{{ idx + 1 }}</div>
                <q-space />
                <q-btn dense round flat icon="delete" color="negative" @click="removerLinha('operadores', row.id)" />
              </div>

              <div class="row q-col-gutter-md">
                <div class="col-12 col-sm-4">
                  <q-input v-model="row.data" label="Data" outlined dense mask="##/##/####" />
                </div>
                <div class="col-12 col-sm-8">
                  <q-input v-model="row.equipamento" label="Operador" outlined dense />
                </div>

                <div class="col-6">
                  <q-input v-model="row.horaSaida" label="Saída" outlined dense mask="##:##"
                           @update:model-value="recalcular(row)" />
                </div>
                <div class="col-6">
                  <q-input v-model="row.horaRetorno" label="Retorno" outlined dense mask="##:##"
                           @update:model-value="recalcular(row)" />
                </div>

                <div class="col-6">
                  <q-input v-model="row.intervaloSaida" label="Início do intervalo" outlined dense mask="##:##"
                           @update:model-value="recalcular(row)" />
                </div>
                <div class="col-6">
                  <q-input v-model="row.intervaloRetorno" label="Fim do intervalo" outlined dense mask="##:##"
                           @update:model-value="recalcular(row)" />
                </div>

                <div class="col-6">
                  <q-input v-model.number="row.precoHora" label="Preço/Hora" outlined dense type="number" step="0.01"
                           prefix="R$ " @update:model-value="recalcular(row)" />
                </div>
                <div class="col-6">
                  <q-input :model-value="formatHoras(row.horas)" label="Horas" outlined dense readonly />
                </div>

                <div class="col-12">
                  <div class="row items-center">
                    <div class="text-caption text-grey-8">Valor</div>
                    <q-space />
                    <div class="text-subtitle2 text-bold">{{ money(row.valor) }}</div>
                  </div>
                </div>
              </div>
            </q-card>
          </q-slide-transition>

          <div class="row justify-end q-mt-sm">
            <div class="text-subtitle2 q-mr-sm">Total Operadores:</div>
            <div class="text-subtitle2 text-bold">{{ money(total('operadores')) }}</div>
          </div>
        </q-card-section>
      </q-card>

      <!-- MÁQUINAS (mobile em cartões) -->
      <q-card flat bordered>
        <q-card-section class="row items-center">
          <div class="text-primary text-subtitle2 text-bold">COMPOSIÇÃO — MÁQUINAS</div>
          <q-space />
          <q-btn dense icon="add" color="primary" label="Adicionar" @click="adicionarLinha('maquinas')" />
        </q-card-section>
        <q-separator />

        <q-card-section class="q-gutter-md">
          <q-slide-transition v-for="(row, idx) in form.maquinas" :key="row.id">
            <q-card bordered flat class="q-pa-md os-card">
              <div class="row items-center q-mb-sm">
                <div class="text-subtitle2">Máquina #{{ idx + 1 }}</div>
                <q-space />
                <q-btn dense round flat icon="delete" color="negative" @click="removerLinha('maquinas', row.id)" />
              </div>

              <div class="row q-col-gutter-md">
                <div class="col-12 col-sm-4">
                  <q-input v-model="row.data" label="Data" outlined dense mask="##/##/####" />
                </div>
                <div class="col-12 col-sm-8">
                  <q-input v-model="row.equipamento" label="Máquina" outlined dense />
                </div>

                <div class="col-6">
                  <q-input v-model="row.horaSaida" label="Saída" outlined dense mask="##:##"
                           @update:model-value="recalcular(row)" />
                </div>
                <div class="col-6">
                  <q-input v-model="row.horaRetorno" label="Retorno" outlined dense mask="##:##"
                           @update:model-value="recalcular(row)" />
                </div>

                <div class="col-6">
                  <q-input v-model="row.intervaloSaida" label="Início do intervalo" outlined dense mask="##:##"
                           @update:model-value="recalcular(row)" />
                </div>
                <div class="col-6">
                  <q-input v-model="row.intervaloRetorno" label="Fim do intervalo" outlined dense mask="##:##"
                           @update:model-value="recalcular(row)" />
                </div>

                <div class="col-6">
                  <q-input v-model.number="row.precoHora" label="Preço/Hora" outlined dense type="number" step="0.01"
                           prefix="R$ " @update:model-value="recalcular(row)" />
                </div>
                <div class="col-6">
                  <q-input :model-value="formatHoras(row.horas)" label="Horas" outlined dense readonly />
                </div>

                <div class="col-12">
                  <div class="row items-center">
                    <div class="text-caption text-grey-8">Valor</div>
                    <q-space />
                    <div class="text-subtitle2 text-bold">{{ money(row.valor) }}</div>
                  </div>
                </div>
              </div>
            </q-card>
          </q-slide-transition>

          <div class="row justify-end q-mt-sm">
            <div class="text-subtitle2 q-mr-sm">Total Máquinas:</div>
            <div class="text-subtitle2 text-bold">{{ money(total('maquinas')) }}</div>
          </div>
        </q-card-section>
      </q-card>

      <!-- CLÁUSULAS -->
      <q-card flat bordered>
        <q-card-section class="text-primary text-subtitle2 text-bold">CLÁUSULAS CONTRATUAIS</q-card-section>
        <q-separator />
        <q-card-section class="q-gutter-sm">
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-4">
              <q-input v-model.number="form.clausulas.minimoHoras" type="number" min="0" step="0.5"
                       label="Mínimo de horas" outlined dense />
            </div>
            <div class="col-12 col-md-4">
              <q-input v-model.number="form.clausulas.prazoPagamentoDias" type="number" min="0"
                       label="Prazo p/ pagamento (dias)" outlined dense />
            </div>
            <div class="col-12 col-md-4">
              <q-input v-model="form.clausulas.formaPgto" label="Forma de pagamento" outlined dense />
            </div>
            <div class="col-12">
              <q-input v-model="form.clausulas.obs" type="textarea" outlined dense autogrow
                       placeholder="Observações adicionais (opcional)" />
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- ASSINATURAS -->
      <q-card flat bordered>
        <q-card-section class="text-primary text-subtitle2 text-bold">ASSINATURAS</q-card-section>
        <q-separator />
        <q-card-section class="q-gutter-sm">
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-4">
              <q-input v-model="form.assinaturas.localData" label="Local e Data" outlined dense
                       placeholder="Ex.: São José, 15/08/2025" />
            </div>
            <div class="col-12 col-md-4">
              <q-input v-model="form.assinaturas.associado" label="Associado" outlined dense />
            </div>
            <div class="col-12 col-md-4">
              <q-input v-model="form.assinaturas.contratante" label="Contratante" outlined dense />
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- RODAPÉ -->
      <div class="row justify-end items-center q-gutter-sm q-mt-md">
        <div class="text-subtitle2 q-mr-sm">Total Geral:</div>
        <div class="text-subtitle2 text-bold">{{ money(totalGeral) }}</div>
        <q-btn flat label="Limpar" icon="restart_alt" color="grey-8" @click="resetar()" />
        <q-btn color="primary" icon="save" label="Salvar" @click="salvar" />
      </div>
    </q-form>
  </q-page>
</template>

<script>
export default {
  name: 'OrdemServicoFormMobile',
  data () {
    return {
      form: {
        contratante: {
          nome: '',
          endereco: '',
          municipio: '',
          estado: '',
          cep: '',
          fone: '',
          cnpjCpf: '',
          contato: '',
          email: ''
        },
        descricao: '',
        local: {
          contatos: '',
          telefones: '',
          endereco: '',
          bairro: '',
          municipio: '',
          estado: '',
          referencia: ''
        },
        operadores: [ this.novoItem() ],
        maquinas: [ this.novoItem() ],
        clausulas: {
          minimoHoras: null,
          prazoPagamentoDias: null,
          formaPgto: '',
          obs: ''
        },
        assinaturas: {
          localData: '',
          associado: '',
          contratante: ''
        }
      },
      req: v => !!v || 'Obrigatório'
    }
  },
  computed: {
    totalGeral () {
      return this.total('operadores') + this.total('maquinas')
    }
  },
  methods: {
    novoItem () {
      return {
        id: crypto.randomUUID ? crypto.randomUUID() : String(Date.now() + Math.random()),
        data: '',
        equipamento: '',
        horaSaida: '',
        horaRetorno: '',
        intervaloSaida: '',   // HH:MM
        intervaloRetorno: '', // HH:MM
        horas: 0,
        precoHora: null,
        valor: 0
      }
    },
    adicionarLinha (tipo) {
      this.form[tipo].push(this.novoItem())
    },
    removerLinha (tipo, id) {
      this.form[tipo] = this.form[tipo].filter(i => i.id !== id)
    },
    parseHHMM (hhmm) {
      const [h = 0, m = 0] = String(hhmm || '00:00').split(':').map(n => parseInt(n || 0))
      return (h * 60) + m
    },
    diffMin (ini, fim) {
      // aceita rollover (fim < ini -> +24h)
      let a = this.parseHHMM(ini)
      let b = this.parseHHMM(fim)
      if (b < a) b += 24 * 60
      return Math.max(0, b - a)
    },
    horasEntre (saida, retorno, intIni, intFim) {
      const total = this.diffMin(saida, retorno)
      const pausa = (intIni && intFim) ? this.diffMin(intIni, intFim) : 0
      const efetivo = Math.max(0, total - pausa)
      return +(efetivo / 60).toFixed(2)
    },
    recalcular (row) {
      row.horas = this.horasEntre(row.horaSaida, row.horaRetorno, row.intervaloSaida, row.intervaloRetorno)
      row.valor = +(Number(row.precoHora || 0) * Number(row.horas || 0)).toFixed(2)
    },
    formatHoras (v) {
      return Number(v || 0).toFixed(2).replace('.', ',')
    },
    money (v) {
      return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(v || 0))
    },
    total (tipo) {
      return this.form[tipo].reduce((s, r) => s + Number(r.valor || 0), 0)
    },
    resetar () {
      // mantém dados do contratante/local se quiser; aqui resetamos só as composições
      this.form.operadores = [ this.novoItem() ]
      this.form.maquinas = [ this.novoItem() ]
    },
    async salvar () {
      const payload = {
        ...this.form,
        total: this.totalGeral
      }
      try {
        // ajuste a URL para seu backend:
        // const res = await fetch('http://localhost:5510/v1/os', {
        //   method: 'POST',
        //   headers: {
        //     'Content-Type': 'application/json',
        //     token: localStorage.getItem('access_token')
        //   },
        //   body: JSON.stringify(payload)
        // })
        // const data = await res.json()
        this.$q.notify({ type: 'positive', message: 'Ordem de Serviço salva (mock)!' })
        console.log('payload OS:', payload)
      } catch (e) {
        this.$q.notify({ type: 'negative', message: 'Erro ao salvar OS' })
        console.error(e)
      }
    }
  }
}
</script>

<style scoped>
.q-card + .q-card { margin-top: 12px; }
.os-card {
  border: 1px solid rgba(0,0,0,0.06);
  border-radius: 12px;
}
</style>
