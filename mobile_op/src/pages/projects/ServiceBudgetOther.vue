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
        Cadastrar Projeto
      </div>
    </div>

    <q-separator class="bg-grey" />

    <div>
      <q-input color="secondary" class="q-ma-md" outlined v-model="name" label="Nome do Projeto" />

      <div class="flex justify-center">
        <select class="machine-style" v-model="category_uuid" @change="get_machine_category_by_group_uuid">
          <option v-for="machine in machine_list" :key="machine.uuid" :value="machine.uuid">
            {{ machine.category_name }}
          </option>
        </select>
      </div>

      <div class="q-pa-md" v-if="category_list.length">
        <p class="text-primary text-bold">Selecione as Máquinas:</p>
        <span class="text-primary">Selecione uma ou mais máquinas por Projeto*</span>
        <q-list bordered separator class="border">
          <q-item v-for="category in category_list" :key="category.uuid" clickable>
            <q-item-section avatar>
              <q-checkbox 
                v-model="selected_machines" 
                :val="category.uuid" 
              />
            </q-item-section>
            <q-item-section class="text-primary">
              <p class="text-bold text-primary">{{ category.category_name }}</p>
              <p class="text-bold text-primary"> Bobcat E26 </p>
              <span>Escavadeira é um equipamento usado para escavar e mover materiais em grande quantidade</span>
            </q-item-section>
          </q-item>
        </q-list>
      </div>

      <q-input color="secondary" class="q-ma-md" outlined v-model="max_volume" label="Descrever Volume" />
      <span class="text-primary q-ml-md ">Descrever metro cubico (m³)</span>
      <div class="q-ma-md">
          <span class="text-primary text-bold">Precisa de Operador?</span>
          <q-checkbox 
            v-model="need_operator"
            :true-value="1"
            :false-value="0"
            label="SIM"
            color="secondary"
            class="q-mt-md q-mb-md text-primary text-bold"
            keep-color
          />
          <q-checkbox 
            v-model="need_operator"
            :true-value="0"
            :false-value="1"
            label="Não"
            color="secondary"
            class="q-mt-md q-mb-md text-primary text-bold"
            keep-color
          />
        </div>
      <span class="text-primary q-ml-md text-bold">Descreva o Serviço*</span>
      <div class="q-pa-md flex justify-center">
        <q-input
          color="secondary"
          v-model="description"
          filled
          type="textarea"
          style="border: 1px solid secondary; min-width: 340px;"
        />
      </div>

      <div class="q-pa-md">
        <p class="text-primary text-bold text-h6">Localização do serviço</p>
        <q-input color="secondary" class="q-mt-sm" outlined v-model="zip_code" label="CEP" @blur="get_address_by_cep" />
        <q-input color="secondary" class="q-mt-sm" outlined v-model="street" label="Rua" />
        <q-input color="secondary" class="q-mt-sm" outlined v-model="number_street" label="Número" />
        <q-input color="secondary" class="q-mt-sm" outlined v-model="complement" label="Logradouro" />
        <q-input color="secondary" class="q-mt-sm" outlined v-model="neighborhood" label="Bairro" />
        <q-input color="secondary" class="q-mt-sm" outlined v-model="city_name" label="Cidade" />
        <q-input color="secondary" class="q-mt-sm" outlined v-model="state_name" label="UF" />


        <div class="q-pa-md">
          <!-- <p class="text-primary text-bold text-h6">Data de previsão</p>
          <div class="q-mb-lg flex justify-center">
            <q-date v-model="expected_date" color="secondary" />
          </div> -->
        </div>
        <div class="flex justify-between q-mt-sm">
          <q-btn @click="$router.go(-1)" label="Cancelar" class="text-primary" />
          <q-btn label="Próximo" color="secondary" class="text-primary" @click="on_submit" />
        </div>
      </div>
    </div>
  </q-page>
</template>

<script>
export default {
  data() {
    return {
      name: null,
      description: null,
      expected_date: null,
      category_uuid: null,
      selected_machines: [], 
      machine_list: [],
      category_list: [],
      max_volume: null,
      zip_code: '',
      street: '',
      number_street: '',
      complement: '',
      neighborhood: '',
      city_name: null,
      state_name: null,
      need_operator: 0,
    };
  },
  methods: {
    get_all_machine_category_group() {
      this.loading = true;
      fetch(`https://fortis-api.55technology.com/v1/category/group/project/${this.$route.params.project_category_uuid}`, {
        method: "GET",
        headers: {
          token: localStorage.getItem("access_token"),
        },
      })
        .then((response) => response.json())
        .then((data) => {
          this.machine_list = data.machine_category_group.map(category => ({
            category_name: category.category_name,
            uuid: category.uuid
          }));
        })
        .catch(() => {
          this.$q.notify({ color: "red-5", textColor: "white", icon: "error", message: "Erro ao carregar categorias." });
        })
        .finally(() => {
          this.loading = false;
        });
    },

    get_machine_category_by_group_uuid() {
      if (!this.category_uuid) return;

      this.loading = true;
      fetch(`https://fortis-api.55technology.com/v1/machine/category/group/${this.category_uuid}`, {
        method: "GET",
        headers: { token: localStorage.getItem("access_token") },
      })
        .then((response) => response.json())
        .then((data) => {
          this.category_list = data.machine_category || [];
        })
        .catch(() => {
          this.$q.notify({ color: "red-5", textColor: "white", icon: "error", message: "Erro ao carregar categorias." });
        })
        .finally(() => {
          this.loading = false;
        });
    },

    async get_address_by_cep() {
      if (this.zip_code.length !== 8) {
        this.$q.notify({ color: "red-5", textColor: "white", icon: "error", message: "CEP inválido!" });
        return;
      }

      try {
        const response = await fetch(`https://viacep.com.br/ws/${this.zip_code}/json/`);
        const data = await response.json();

        if (data.erro) {
          this.$q.notify({ color: "red-5", textColor: "white", icon: "error", message: "CEP não encontrado!" });
          return;
        }

        this.street = data.logradouro;
        this.neighborhood = data.bairro;
        this.city_name = data.localidade;
        this.state_name = data.uf;
        this.number_street = data.complemento;
      } catch (error) {
        this.$q.notify({ color: "red-5", textColor: "white", icon: "error", message: "Erro ao buscar endereço!" });
      }
    },

    on_submit() {
      fetch(`https://fortis-api.55technology.com/v1/project/`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', token: localStorage.getItem('access_token') },
        body: JSON.stringify({
          name: this.name,
          machine_category_uuid: this.selected_machines, 
          description: this.description,
          expected_date: this.expected_date,
          max_volume: this.max_volume,
          zip_code: this.zip_code,
          street: this.street,
          number_street: this.number_street,
          complement: this.complement,
          neighborhood: this.neighborhood,
          city_name: this.city_name,
          state_name: this.state_name,
          need_operator: this.need_operator,
        }),
      })
        .then((response) => response.json())
        .then((data) => {
          this.$q.notify({ color: 'green-4', textColor: 'white', icon: 'cloud_done', message: 'Dados cadastrados com sucesso.' });
          if (data.project_uuid) this.$router.push(`/user/project/recomend/${data.project_uuid}`);
        })
        .catch(() => {
          this.$q.notify({ color: 'red-4', textColor: 'white', icon: 'error', message: 'Erro ao cadastrar projeto.' });
        });
    }
  },
  mounted() {
    this.get_all_machine_category_group();
  },
};
</script>

<style scoped>
.machine-style {
  min-width: 340px;
  height: 50px;
  background-color: white;
  border-radius: 3px;
}

.border {
  border-color: secondary;
}
</style>
