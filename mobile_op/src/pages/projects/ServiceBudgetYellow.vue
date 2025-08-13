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

      <div v-for="(selection, index) in machine_selections" :key="selection.id">
        <div class="flex justify-center">
          <select
            class="machine-style"
            v-model="selection.category_uuid"
            @change="() => get_machine_category_by_group_uuid(index)"
          >
            <option
              v-for="machine in machine_category_list"
              :key="machine.uuid"
              :value="machine.uuid"
            >
              {{ machine.category_name }}
            </option>
          </select>
        </div>

        <div class="q-pa-md" v-if="selection.category_list.length">
          <p class="text-primary text-bold">Selecione as Máquinas:</p>
          <span class="text-primary">Selecione uma ou mais máquinas por Projeto*</span>
          <q-list bordered separator class="border">
            <q-item
              v-for="category in selection.category_list"
              :key="category.uuid"
              clickable
            >
              <q-item-section avatar>
                <q-checkbox
                  v-model="selection.selected_machines"
                  :val="category.uuid"
                />
              </q-item-section>
              <q-item-section class="text-primary">
                <p class="text-bold text-h5 text-primary">
                  {{ category.category_name }}
                </p>
                <div class="flex justify-center">
                  <q-img
                    src="../../assets/escavadeirawhats.jpeg"
                    width="150px"
                  />
                </div>

                <!-- TAMANHO ESCAVADEIRA -->
                <div v-if="category.category_name === 'Escavadeira'">
                  <p class="text-bold text-primary q-mt-md">Tamanho</p>
                  <q-select
                    v-model="selection.excavator_size"
                    :options="optionsEscavadeira"
                    label="Tamanho da Escavadeira"
                    outlined
                    color="secondary"
                    class="q-mt-sm"
                    style="width: 100%; z-index: 9999;"
                    input-class="text-primary"
                    popup-content-class="text-primary"
                  />
                </div>

                <!-- TAMANHO RETROESCAVADEIRA -->
                <div v-if="category.category_name === 'Retroescavadeira'">
                  <p class="text-bold text-primary q-mt-md">Tamanho</p>
                  <q-select
                    v-model="selection.backhoe_type"
                    :options="optionsRetroescavadeira"
                    label="Tamanho da Retroescavadeira"
                    outlined
                    color="secondary"
                    class="q-mt-sm"
                    style="width: 100%; z-index: 9999;"
                    input-class="text-primary"
                    popup-content-class="text-primary"
                  />
                </div>

                <!-- TAMANHO PÁ CARREGADEIRA -->
                <div v-if="category.category_name === 'Pá Carregadeira'">
                  <p class="text-bold text-primary q-mt-md">Tamanho</p>
                  <q-select
                    v-model="selection.loader_size"
                    :options="optionsPaCarregadeira"
                    label="Tamanho da Pá"
                    outlined
                    color="secondary"
                    class="q-mt-sm"
                    style="width: 100%; z-index: 9999;"
                    input-class="text-primary"
                    popup-content-class="text-primary"
                  />
                </div>

                <!-- DESCRIÇÃO -->
                <div class="text-bold text-primary q-mt-md">
                  <span v-if="category.category_name === 'Escavadeira'">Escavadeira é um equipamento usado para escavar e mover materiais em grande quantidade</span>
                  <span v-if="category.category_name === 'Retroescavadeira'">Uma retroescavadeira é uma máquina pesada que combina as funções de uma escavadeira e de uma pá carregadeira</span>
                  <span v-if="category.category_name === 'Pá Carregadeira'">Uma pá carregadeira é uma máquina pesada usada para carregar, escavar, nivelar e movimentar materiais</span>
                  <span v-if="category.category_name === 'Motoniveladora'">Uma motoniveladora é uma máquina pesada que serve para nivelar terrenos, deslocar materiais e preparar o solo para pavimentação</span>
                </div>

                <!-- OPERADOR -->
                <div class="q-ma-md">
                  <span class="text-primary text-bold">Precisa de Operador?</span>
                  <div>
                  <q-checkbox
                    v-model="selection.need_operator_map[category.uuid]"
                    :true-value="1"
                    :false-value="0"
                    label="SIM"
                    color="secondary"
                    class="q-mt-md q-mb-md text-primary text-bold"
                    keep-color
                  />
                  <q-checkbox
                    v-model="selection.need_operator_map[category.uuid]"
                    :true-value="0"
                    :false-value="1"
                    label="Não"
                    color="secondary"
                    class="q-mt-md q-mb-md text-primary text-bold"
                    keep-color
                  />
                </div>
                </div>
              </q-item-section>
            </q-item>
          </q-list>
        </div>
      </div>

    <div class="flex justify-center q-mb-md">
      <q-btn
        rounded
        color="secondary"
        icon="add"
        class="q-mt-md"
        size="md"
        label="Quero outra tipo máquina"
        @click="addMachineSelection"
      />
    </div>
      <q-input color="secondary" class="q-ma-md" outlined v-model="max_volume" label="Descrever Volume" />
      <span class="text-primary q-ml-md ">Descrever metro cubico (m³)</span>
      
        <div class="q-ma-md">
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
export default {
  data() {
    return {
      excavator_size:'',
      optionsEscavadeira: ['Escavadeira Pequena > Profundidade de Escavação Máximo > 5,5 metros', ' Escavadeira Média > Profundidade de Escavação Máximo > 6,7 metros', 'Escavadeira Grande > Profundidade de Escavação Máximo > 7,4 metros'],
      backhoe_type:'',
      optionsRetroescavadeira: ['Convencional 4x2', 'Traçada 4x4'],
      loader_size:'',
      optionsPaCarregadeira: ['Carregadeira Compacta > Cubagem da Pá Máximo > de 0,75m3 a 2,00m3', ' Carregadeira Pequena > Cubagem da Pá Máximo > de 2,00m3 a 4,00m3', 'Carregadeira Média > Cubagem da Pá Máximo > de 4,00m3 a 6,00m3','Carregadeira Grande > Cubagem da Pá Máximo > de 6,00m3 a 10,00m3'],
      
      machine_selections: [
    {
      id: 1,
      category_uuid: null,
      category_list: [],
      selected_machines: [],
      excavator_size: '',
      backhoe_type: '',
      loader_size: '',
      need_operator_map: {}
      }
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
    };
  },
  computed:{
    isEscavadeira() {
    const selectedCategory = this.machine_category_list.find(machine => machine.uuid === this.category_uuid);
    return selectedCategory && selectedCategory.category_name === "Escavadeira";
    },

    isRetroescavadeira() {
    const selectedCategory = this.machine_category_list.find(machine => machine.uuid === this.category_uuid);
    return selectedCategory && selectedCategory.category_name === "Retroescavadeira";
    },
    isPaCarregadeira() {
    const selectedCategory = this.machine_category_list.find(machine => machine.uuid === this.category_uuid);
    return selectedCategory && selectedCategory.category_name === "Pá Carregadeira";
    },
    isMotoNiveladora() {
    const selectedCategory = this.machine_category_list.find(machine => machine.uuid === this.category_uuid);
    return selectedCategory && selectedCategory.category_name === "Motoniveladora";
    },
  },
  methods: {
    get_all_machine_category_group() {
      this.loading = true;
      fetch(`http://localhost:5510/v1/category/group/project/${this.$route.params.project_category_uuid}`, {
        method: "GET",
        headers: {
          token: localStorage.getItem("access_token"),
        },
      })
        .then((response) => response.json())
        .then((data) => {
          this.machine_category_list = data.machine_category_group.map(category => ({
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
    get_machine_category_by_group_uuid(index) {
      const category_uuid = this.machine_selections[index].category_uuid;
      if (!category_uuid) return;

      this.loading = true;
      fetch(`http://localhost:5510/v1/machine/category/group/${category_uuid}`, {
        method: "GET",
        headers: { token: localStorage.getItem("access_token") },
      })
        .then((response) => response.json())
        .then((data) => {
          this.machine_selections[index].category_list = data.machine_category || [];
        })
        .catch(() => {
          this.$q.notify({ color: "red-5", textColor: "white", icon: "error", message: "Erro ao carregar categorias." });
        })
        .finally(() => {
          this.loading = false;
        });
    },
    addMachineSelection() {
      this.machine_selections.push({
        id: this.next_machine_id++,
        category_uuid: null,
        category_list: [],
        selected_machines: [],
        excavator_size: '',
        backhoe_type: '',
        loader_size: '',
        need_operator_map: {}
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
      const machine_list = this.machine_selections.flatMap((selection) => {
        return selection.selected_machines.map((uuid) => {
          const category = selection.category_list.find(cat => cat.uuid === uuid);
          const parameters = {};

          if (category.category_name === "Escavadeira") {
            parameters.excavator_size = selection.excavator_size;
          } else if (category.category_name === "Retroescavadeira") {
            parameters.backhoe_type = selection.backhoe_type;
          } else if (category.category_name === "Pá Carregadeira") {
            parameters.loader_size = selection.loader_size;
          }

          return {
            machine_category_uuid: uuid,
            need_operator: selection.need_operator_map[uuid] ?? 0,
            parameters
          };
        });
      });
      fetch(`http://localhost:5510/v1/project/`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          token: localStorage.getItem('access_token')
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
          machine_list
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
