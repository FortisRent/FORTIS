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

      <div v-for="(selection, index) in machine_selections" :key="selection.id">
        <div class="flex justify-center">
          <q-skeleton v-if="loading" type="QInput" class="q-ma-md" width="100%" style="margin-top: 0" />
          <q-select v-else class="machine-style" v-model="selection.category_uuid" :options="machine_category_list"
            option-label="category_name" option-value="uuid" label="Selecione a categoria" outlined @update:model-value="
              () => get_machine_category_by_group_uuid(index)
            " emit-value map-options behavior="menu" color="primary" popup-content-style="color: #000" />
        </div>

        <div class="q-pa-md" v-if="selection.category_list.length">
          <span class="text-primary text-bold text-subtitle1">Selecione uma ou mais máquinas por Projeto*</span>
          <q-list bordered separator>
            <q-expansion-item v-for="category in selection.category_list" :key="category.uuid"
              v-model="expandedItems[category.uuid]" class="reset-styles" style="padding: 0.5rem;"
              expand-icon-class="hidden">
              <!-- Cabeçalho do item (sempre visível) -->
              <template v-slot:header>
                <q-item-section avatar>
                  <q-checkbox v-model="selection.selected_machines" :val="category.uuid" color="secondary"
                    @update:model-value="(val) => handleCheckboxChange(val, category.uuid)" />
                </q-item-section>

                <q-item-section>
                  <div class="row items-center">
                    <q-img :src="getMachineImage(category.category_name)" width="60px" class="q-mr-md" />
                    <span class="text-h6 text-bold text-primary">{{
                      category.category_name
                    }}</span>
                  </div>
                </q-item-section>

                <q-item-section side>
                  <q-icon :name="expandedItems[category.uuid]
                    ? 'expand_less'
                    : 'expand_more'
                    " />
                </q-item-section>
              </template>

              <!-- Conteúdo expandido -->
              <q-card>
                <q-card-section>
                  <!-- TAMANHO ESCAVADEIRA -->
                  <div v-if="category.category_name === 'Escavadeira'" class="q-mt-md">
                    <p class="text-bold text-primary text-subtitle1" style="margin: 0">
                      Tamanho
                    </p>
                    <q-select v-model="selection.excavator_size" :options="optionsEscavadeira"
                      label="Tamanho da Escavadeira" outlined color="secondary" class="q-mt-sm"
                      popup-content-style="color: #000" />
                  </div>

                  <!-- TAMANHO RETROESCAVADEIRA -->
                  <div v-if="category.category_name === 'Retroescavadeira'" class="q-mt-md">
                    <p class="text-bold text-primary text-subtitle1" style="margin: 0">
                      Tamanho
                    </p>
                    <q-select v-model="selection.backhoe_type" :options="optionsRetroescavadeira"
                      label="Tamanho da Retroescavadeira" outlined color="secondary" class="q-mt-sm"
                      popup-content-style="color: #000" />
                  </div>

                  <!-- DESCRIÇÃO -->
                  <div class="text-bold text-primary q-mt-md">
                    {{ getMachineDescription(category.category_name) }}
                  </div>

                  <!-- OPERADOR -->
                  <div class="q-mt-md">
                    <span class="text-primary text-bold">Precisa de Operador?</span>
                    <q-option-group v-model="selection.need_operator_map[category.uuid]" :options="[
                      { label: 'SIM', value: 1 },
                      { label: 'Não', value: 0 }
                    ]" type="radio" color="secondary" inline class="q-mt-md q-mb-md text-primary text-bold" />
                  </div>
                </q-card-section>
              </q-card>
            </q-expansion-item>
          </q-list>
        </div>
      </div>

      <div class="flex justify-center q-mb-md">
        <q-skeleton type="QBtn" class="q-mt-md" v-if="loading" style="border-radius: 20px; margin-top: 0"
          width="300px" />
        <q-btn v-else rounded color="secondary" icon="add" class="q-mt-md" size="md" label="Quero outra tipo máquina"
          @click="addMachineSelection" />
      </div>

      <q-skeleton type="text" class="q-ml-md" v-if="loading" width="200px" />
      <span v-else class="text-primary text-subtitle1 text-bold q-ml-md">Descrever metro cubico (m³)</span>
      <q-skeleton type="QInput" class="q-ma-md" v-if="loading" />
      <q-input v-else color="secondary" class="q-mx-md" outlined v-model="max_volume" label="Descrever Volume" />

      <div class="q-ma-md"></div>
      <q-skeleton type="text" class="q-ml-md" v-if="loading" width="200px" />
      <span v-else class="text-primary text-bold q-ml-md text-subtitle1">Descreva o Serviço*</span>
      <div class="q-mx-md flex justify-center">
        <q-skeleton type="QInput" v-if="loading" width="100%" height="150px" />
        <q-input v-else color="secondary" v-model="description" filled type="textarea"
          style="border: 1px solid #ccc; border-radius: 5px; min-width: 340px"
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
        <q-input color="secondary" class="q-mt-sm" outlined v-model="street" label="Logradouro" />
        <q-input color="secondary" class="q-mt-sm" outlined v-model="number_street" label="Número" />
        <q-input color="secondary" class="q-mt-sm" outlined v-model="complement" label="Complemento" />
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
import escavadeiraImg from '../../assets/escavadeira.png';
import retroescavadeiraImg from '../../assets/retroescavadeira.png';
import paCarregadeiraImg from '../../assets/pa-carregadeira.png';
import motoniveladoraImg from '../../assets/motoniveladora.png';

export default {
  data() {
    return {
      loading: true,
      loadingMachines: false,
      expandedItems: {},
      excavator_size: '',
      optionsEscavadeira: [
        'Escavadeira Pequena > Profundidade de Escavação Máximo > 5,5 metros',
        ' Escavadeira Média > Profundidade de Escavação Máximo > 6,7 metros',
        'Escavadeira Grande > Profundidade de Escavação Máximo > 7,4 metros',
      ],
      backhoe_type: '',
      optionsRetroescavadeira: ['Convencional 4x2', 'Traçada 4x4'],
      loader_size: '',
      optionsPaCarregadeira: [
        'Carregadeira Compacta > Cubagem da Pá Máximo > de 0,75m3 a 2,00m3',
        'Carregadeira Pequena > Cubagem da Pá Máximo > de 2,00m3 a 4,00m3',
        'Carregadeira Média > Cubagem da Pá Máximo > de 4,00m3 a 6,00m3',
        'Carregadeira Grande > Cubagem da Pá Máximo > de 6,00m3 a 10,00m3',
      ],

      machine_selections: [
        {
          id: 1,
          category_uuid: null,
          category_list: [],
          selected_machines: [],
          excavator_size: '',
          backhoe_type: '',
          loader_size: '',
          need_operator_map: {},
        },
      ],
      next_machine_id: 2,

      name: null,
      description: null,
      category_uuid: null,
      selected_machines: [],
      machine_category_list: [],
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
      need_operator_map: {},
      inputValue: '',
      project_name_options: [],
      all_project_names: [],
    };
  },
  computed: {
    isEscavadeira() {
      const selectedCategory = this.machine_category_list.find(
        (machine) => machine.uuid === this.category_uuid
      );
      return (
        selectedCategory && selectedCategory.category_name === 'Escavadeira'
      );
    },

    isRetroescavadeira() {
      const selectedCategory = this.machine_category_list.find(
        (machine) => machine.uuid === this.category_uuid
      );
      return (
        selectedCategory &&
        selectedCategory.category_name === 'Retroescavadeira'
      );
    },
    isPaCarregadeira() {
      const selectedCategory = this.machine_category_list.find(
        (machine) => machine.uuid === this.category_uuid
      );
      return (
        selectedCategory && selectedCategory.category_name === 'Pá Carregadeira'
      );
    },
    isMotoNiveladora() {
      const selectedCategory = this.machine_category_list.find(
        (machine) => machine.uuid === this.category_uuid
      );
      return (
        selectedCategory && selectedCategory.category_name === 'Motoniveladora'
      );
    },
  },
  methods: {
    handleCheckboxChange(selectedValues, uuid) {
      this.expandedItems[uuid] = selectedValues.includes(uuid);
      const selection = this.machine_selections.find(sel =>
        sel.category_list.some(categoria => categoria.uuid === uuid)
      );
      if (selection && selectedValues.includes(uuid)) {
        selection.need_operator_map = {
          ...selection.need_operator_map,
          [uuid]: 1
        };
      }
    },
    getMachineImage(machineName) {
      const images = {
        Escavadeira: escavadeiraImg,
        Retroescavadeira: retroescavadeiraImg,
        'Pá Carregadeira': paCarregadeiraImg,
        Motoniveladora: motoniveladoraImg,
      };
      return images[machineName] || '';
    },

    getMachineDescription(machineName) {
      const descriptions = {
        Escavadeira:
          'Escavadeira é um equipamento usado para escavar e mover materiais em grande quantidade',
        Retroescavadeira:
          'Uma retroescavadeira é uma máquina pesada que combina as funções de uma escavadeira e de uma pá carregadeira',
        'Pá Carregadeira':
          'Uma pá carregadeira é uma máquina pesada usada para carregar, escavar, nivelar e movimentar materiais',
        Motoniveladora:
          'Uma motoniveladora é uma máquina pesada que serve para nivelar terrenos, deslocar materiais e preparar o solo para pavimentação',
      };
      return descriptions[machineName] || '';
    },

    async get_project_by_logged() {
      this.loading = true;
      try {
        const response = await fetch(
          'http://localhost:5510/v1/project/logged/',
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
      try {
        const response = await fetch(
          `http://localhost:5510/v1/category/group/project/${this.$route.params.project_category_uuid}`,
          {
            method: 'GET',
            headers: {
              token: localStorage.getItem('access_token'),
            },
          }
        );
        const data = await response.json();

        this.machine_category_list = data.machine_category_group.map(
          (category) => ({
            category_name: category.category_name,
            uuid: category.uuid,
          })
        );
      } catch (error) {
        this.$q.notify({
          color: 'red-5',
          textColor: 'white',
          icon: 'error',
          message: 'Erro ao carregar categorias.',
        });
      } finally {
        this.loadingMachines = false;
      }
    },
    get_machine_category_by_group_uuid(index) {
      const category_uuid = this.machine_selections[index].category_uuid;
      if (!category_uuid) return;

      this.loadingMachines = true;
      fetch(
        `http://localhost:5510/v1/machine/category/group/${category_uuid}`,
        {
          method: 'GET',
          headers: { token: localStorage.getItem('access_token') },
        }
      )
        .then((response) => response.json())
        .then((data) => {
          this.machine_selections[index].category_list =
            data.machine_category || [];
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
    addMachineSelection() {
      const last = this.machine_selections[this.machine_selections.length - 1];

      if (last.selected_machines.length === 0) {
        this.$q.notify({
          color: 'warning',
          position: 'top',
          message: 'Selecione pelo menos uma máquina antes de adicionar outra.',
          icon: 'warning',
        });
        return;
      }
      this.machine_selections.push({
        id: this.next_machine_id++,
        category_uuid: null,
        category_list: [],
        selected_machines: [],
        excavator_size: '',
        backhoe_type: '',
        loader_size: '',
        need_operator_map: {},
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
      const machine_list = this.machine_selections.flatMap((selection) => {
        return selection.selected_machines.map((uuid) => {
          const category = selection.category_list.find(
            (cat) => cat.uuid === uuid
          );
          const parameters = {};

          if (category.category_name === 'Escavadeira') {
            parameters.excavator_size = selection.excavator_size;
          } else if (category.category_name === 'Retroescavadeira') {
            parameters.backhoe_type = selection.backhoe_type;
          } else if (category.category_name === 'Pá Carregadeira') {
            parameters.loader_size = selection.loader_size;
          }

          return {
            machine_category_uuid: uuid,
            need_operator: selection.need_operator_map[uuid] ?? 0,
            parameters,
          };
        });
      });
      fetch('http://localhost:5510/v1/project/', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          token: localStorage.getItem('access_token'),
        },
        body: JSON.stringify({
          name: this.name,
          description: this.description,
          max_volume: this.max_volume,
          zip_code: this.zip_code,
          street: this.street,
          number_street: this.number_street,
          complement: this.complement,
          neighborhood: this.neighborhood,
          city_name: this.city_name,
          state_name: this.state_name,
          machine_list,

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
.reset-styles {
  padding: 0;
  margin: 0;
  border: none;
}

.machine-style {
  min-width: 340px;
  height: 50px;
  background-color: white;
  border-radius: 3px;
}

.machine-style .q-field__control {
  height: 50px;
}

.q-expansion-item__container .q-item {
  min-height: 80px;
}

.q-expansion-item__content .q-card {
  box-shadow: none;
  border-top: 1px solid #eee;
}
</style>
