<template>
  <q-page>
    <div class="row justify-around items-center q-pa-md q-mt-lg">
      <q-btn flat round icon="chevron_left" class="text-primary" color="secondary" size="18px"
        style="position: absolute; left: 10px" @click="$router.go(-1)" />
      <div class="text-h6 text-primary text-bold q-ml-md">
        Cadastrar Projeto
      </div>
    </div>

    <q-separator class="bg-grey" />

    <div>
      <q-skeleton v-if="loading" type="QInput" class="q-ma-md" />
      <q-select v-else v-model="name" :options="project_name_options" use-input input-debounce="300"
        label="Nome do Projeto" outlined color="primary" popup-content-style="color: #000" class="q-ma-md"
        @filter="filterProjectNames" behavior="menu" hide-selected fill-input @input-value="setInputValue" />

      <div class="flex justify-center">
        <q-skeleton v-if="loading" type="QInput" class="q-ma-md" width="100%" style="margin-top: 0" />
        <q-select v-else class="machine-style" v-model="category_uuid" :options="machine_list"
          option-label="category_name" option-value="uuid" label="Selecione a categoria" outlined
          @update:model-value="() => get_machine_category_by_group_uuid()" emit-value map-options behavior="menu"
          color="primary" popup-content-style="color: #000" style="margin-bottom: 1rem" />
      </div>

      <div class="q-pa-md" v-if="category_list.length">
        <p class="text-primary text-bold">Selecione as Máquinas:</p>
        <span class="text-primary">Selecione uma ou mais máquinas por Projeto*</span>
        <q-list bordered separator class="border">
          <q-item v-for="category in category_list" :key="category.uuid" clickable>
            <q-item-section avatar>
              <q-checkbox v-model="selected_machines" :val="category.uuid" />
            </q-item-section>
            <q-item-section class="text-primary">
              <p class="text-bold text-primary">{{ category.category_name }}</p>
              <p class="text-bold text-primary">Bobcat E26</p>
              <span>Escavadeira é um equipamento usado para escavar e mover
                materiais em grande quantidade</span>
            </q-item-section>
          </q-item>
        </q-list>
      </div>

      <q-skeleton type="text" class="q-ml-md" v-if="loading" width="200px" />
      <span v-else class="text-primary text-subtitle1 text-bold q-ml-md">Descrever metro cubico (m³)</span>
      <q-skeleton type="QInput" class="q-ma-md" v-if="loading" />
      <q-input v-else color="secondary" class="q-mx-md" outlined v-model="max_volume" label="Descrever Volume" />

      <q-skeleton v-if="loading" type="text" class="q-ml-md" width="250px" />

      <div v-else class="q-mx-md">
        <span class="text-primary text-bold">Precisa de Operador?</span>
        <q-option-group v-model="need_operator" :options="[
          { label: 'SIM', value: 1 },
          { label: 'Não', value: 0 }
        ]" type="radio" color="secondary" inline class="q-mt-md q-mb-md text-primary text-bold" />
      </div>
      <q-skeleton type="text" class="q-ml-md" v-if="loading" width="200px" />
      <span v-else class="text-primary text-bold q-ml-md text-subtitle1">Descreva o Serviço*</span>
      <div class="q-mx-md flex justify-center">
        <q-skeleton type="QInput" v-if="loading" width="100%" height="150px" />
        <q-input v-else color="secondary" v-model="description" filled type="textarea"
          style="border: 1px solid #ccc; min-width: 340px"
          placeholder="Exemplo: Demolição de parede interna com remoção de entulho" />
      </div>

      <div v-if="loading" class="q-pa-md">
        <q-skeleton type="text" width="200px" class="q-mt-sm" />
        <q-skeleton type="QInput" width="100%" class="q-mt-sm" />
        <q-skeleton type="QInput" width="100%" class="q-mt-sm" />
        <q-skeleton type="QInput" width="100%" class="q-mt-sm" />
        <q-skeleton type="QInput" width="100%" class="q-mt-sm" />
        <q-skeleton type="QInput" width="100%" class="q-mt-sm" />
        <q-skeleton type="QInput" width="100%" class="q-mt-sm" />
        <q-skeleton type="QInput" width="100%" class="q-mt-sm" />

        <div class="flex justify-between q-mt-sm">
          <q-skeleton type="QBtn" width="100px" />
          <q-skeleton type="QBtn" width="100px" />
        </div>
      </div>

      <div v-else class="q-pa-md">
        <p class="text-primary text-bold text-subtitle1" style="margin: 0">
          Localização do serviço
        </p>
        <q-input color="secondary" class="q-mt-sm" outlined v-model="zip_code" label="CEP" @blur="get_address_by_cep" />
        <q-input color="secondary" class="q-mt-sm" outlined v-model="street" label="Rua" />
        <q-input color="secondary" class="q-mt-sm" outlined v-model="number_street" label="Número" />
        <q-input color="secondary" class="q-mt-sm" outlined v-model="complement" label="Logradouro" />
        <q-input color="secondary" class="q-mt-sm" outlined v-model="neighborhood" label="Bairro" />
        <q-input color="secondary" class="q-mt-sm" outlined v-model="city_name" label="Cidade" />
        <q-input color="secondary" class="q-mt-sm" outlined v-model="state_name" label="UF" />
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
      loading: true,
      loadingMachines: false,
      expandedItems: {},
      inputValue: '',
      project_name_options: [],
      all_project_names: [],
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
    async get_project_by_logged() {
      this.loading = true;
      try {
        const response = await fetch(
          'https://fortis-api.55technology.com/v1/project/logged/',
          {
            headers: { token: localStorage.getItem('access_token') },
          }
        );
        const data = await response.json();

        // Garante que project existe e é um array
        this.all_project_names = (data.project || [])
          .map((project) => project.project_name || '')
          .filter((name) => name); // Remove valores falsy
      } catch (error) {
        console.error('Error fetching projects:', error);
        this.all_project_names = [];
      }
    },

    filterProjectNames(val, update) {
      if (!val) {
        update(() => {
          this.project_name_options = this.all_project_names || [];
        });
        return;
      }

      update(() => {
        const needle = (val || '').toString().toLowerCase();
        this.project_name_options = (this.all_project_names || []).filter((v) =>
          (v || '').toString().toLowerCase().includes(needle)
        );
      });
    },

    setInputValue(val) {
      this.inputValue = val;
      if (val !== '') {
        this.name = val;
      }
    },

    async get_all_machine_category_group() {
      this.loadingMachines = true;
      await fetch(
        `https://fortis-api.55technology.com/v1/category/group/project/${this.$route.params.project_category_uuid}`,
        {
          method: 'GET',
          headers: {
            token: localStorage.getItem('access_token'),
          },
        }
      )
        .then((response) => response.json())
        .then((data) => {
          this.machine_list = data.machine_category_group.map((category) => ({
            category_name: category.category_name,
            uuid: category.uuid,
          }));
        })
        .catch(() => {
          this.$q.notify({
            color: 'red-5',
            textColor: 'white',
            icon: 'error',
            message: 'Erro ao carregar categorias.',
          });
        })
        .finally(() => {
          this.loadingMachines = false;
        });
    },

    get_machine_category_by_group_uuid() {
      if (!this.category_uuid) return;

      this.loadingMachines = true;
      fetch(
        `https://fortis-api.55technology.com/v1/machine/category/group/${this.category_uuid}`,
        {
          method: 'GET',
          headers: { token: localStorage.getItem('access_token') },
        }
      )
        .then((response) => response.json())
        .then((data) => {
          this.category_list = data.machine_category || [];
        })
        .catch(() => {
          this.$q.notify({
            color: 'red-5',
            textColor: 'white',
            icon: 'error',
            message: 'Erro ao carregar categorias.',
          });
        })
        .finally(() => {
          this.loadingMachines = false;
        });
    },

    async get_address_by_cep() {
      if (this.zip_code.length !== 8) {
        this.$q.notify({
          color: 'red-5',
          textColor: 'white',
          icon: 'error',
          message: 'CEP inválido!',
        });
        return;
      }

      try {
        const response = await fetch(
          `https://viacep.com.br/ws/${this.zip_code}/json/`
        );
        const data = await response.json();

        if (data.erro) {
          this.$q.notify({
            color: 'red-5',
            textColor: 'white',
            icon: 'error',
            message: 'CEP não encontrado!',
          });
          return;
        }

        this.street = data.logradouro;
        this.neighborhood = data.bairro;
        this.city_name = data.localidade;
        this.state_name = data.uf;
        this.number_street = data.complemento;
      } catch (error) {
        this.$q.notify({
          color: 'red-5',
          textColor: 'white',
          icon: 'error',
          message: 'Erro ao buscar endereço!',
        });
      }
    },

    on_submit() {
      fetch(`https://fortis-api.55technology.com/v1/project/`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          token: localStorage.getItem('access_token'),
        },
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
          this.$q.notify({
            color: 'green-4',
            textColor: 'white',
            icon: 'cloud_done',
            message: 'Dados cadastrados com sucesso.',
          });
          if (data.project_uuid)
            this.$router.push(`/user/project/recomend/${data.project_uuid}`);
        })
        .catch(() => {
          this.$q.notify({
            color: 'red-4',
            textColor: 'white',
            icon: 'error',
            message: 'Erro ao cadastrar projeto.',
          });
        });
    },
  },
  mounted() {
    Promise.all([
      this.get_all_machine_category_group(),
      this.get_project_by_logged(),
    ]).finally(() => {
      this.loading = false;
    });
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
</style>
