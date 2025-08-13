<template>
    <q-page class="bg-white">
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
                Anexar máquinas cadastradas
            </div>
        </div>
        <q-separator class="bg-secondary"/>
        <!-- Introdução -->
        <div class="q-pa-md" style="min-width: 300px;">
            <q-list bordered separator style="border-color: secondary;" class="text-primary">
                <q-item>
                    <q-item-section>
                        <q-item-label overline class="text-primary"><strong>Introdução</strong></q-item-label>
                        <q-item-label caption class="text-primary">Conclua sua solicitação de orçamento escolhendo
                            a empresa e a máquina da sua escolha.</q-item-label>
                    </q-item-section>
                </q-item>
            </q-list>
        </div>
        <!-- Informações Pessoais -->
        <div class="q-pa-md" style="min-width: 300px;">
            <q-list bordered separator style="border-color: secondary;" class="text-primary">
                <q-item>
                    <q-item-section>
                        <q-item-label overline class="text-primary"><strong>Informações Pessoais</strong></q-item-label>
                    </q-item-section>
                </q-item>
                <q-item>
                    <q-item-section>
                        <q-item-label class="text-primary">Nome: {{ user_name || client_name }}</q-item-label>
                    </q-item-section>
                </q-item>
                <q-item>
                    <q-item-section>
                        <q-item-label class="text-primary">Data inicial do Projeto: {{ created_at }}</q-item-label>
                    </q-item-section>
                </q-item>
                <q-item>
                    <q-item-section>
                        <q-item-label class="text-primary">E-mail: {{ email || client_email }}</q-item-label>
                    </q-item-section>
                </q-item>
                <q-item>
                    <q-item-section>
                        <q-item-label class="text-primary">Telefone: {{ phone || client_phone }}</q-item-label>
                    </q-item-section>
                </q-item>
            </q-list>
        </div>
        <!-- Informações do Projeto -->
        <div class="q-pa-md" style="min-width: 300px;">
            <q-list bordered separator style="border-color: secondary;" class="text-primary">
                <q-item>
                    <q-item-section>
                        <q-item-label overline class="text-primary"><strong>Informações do Projeto</strong></q-item-label>
                    </q-item-section>
                </q-item>
                <q-item>
                    <q-item-section>
                        <q-item-label class="text-primary">Nome do projeto: {{ project_name }}</q-item-label>
                    </q-item-section>
                </q-item>
                <q-item>
                    <q-item-section>
                        <q-item-label class="text-primary">Descrição: {{ project_description }}</q-item-label>
                    </q-item-section>
                </q-item>
            </q-list>
        </div>
        <!-- Informações Detalhadas -->
        <div v-if="hasDetailedInfo" class="q-pa-md" style="min-width: 300px;">
            <q-list bordered separator style="border-color: secondary;" class="text-primary">
                <q-item>
                    <q-item-section>
                        <q-item-label overline class="text-primary"><strong>Informações Detalhadas</strong></q-item-label>
                    </q-item-section>
                </q-item>
                <q-item>
                    <q-item-section>
                        <q-item-label class="text-primary">Quantidade: {{ count }}</q-item-label>
                    </q-item-section>
                </q-item>
                <q-item>
                    <q-item-section>
                        <q-item-label class="text-primary">Peso (kg): {{ weight }}</q-item-label>
                    </q-item-section>
                </q-item>
                <q-item>
                    <q-item-section>
                        <q-item-label class="text-primary">Largura (m): {{ width }}</q-item-label>
                    </q-item-section>
                </q-item>
                <q-item>
                    <q-item-section>
                        <q-item-label class="text-primary">Comprimento: {{ length }}</q-item-label>
                    </q-item-section>
                </q-item>
                <q-item>
                    <q-item-section>
                        <q-item-label class="text-primary">Altura: {{ height }}</q-item-label>
                    </q-item-section>
                </q-item>
                <q-item>
                    <q-item-section>
                        <q-item-label class="text-primary">E-Altura de Elevação (m): {{ lifting_height }}</q-item-label>
                    </q-item-section>
                </q-item>
                <q-item>
                    <q-item-section>
                        <q-item-label class="text-primary">R-Raio: {{ radius }}</q-item-label>
                    </q-item-section>
                </q-item>
                <q-item>
                    <q-item-section>
                        <q-item-label class="text-primary">A-Afastamento do Guindaste (m): {{ clearance }}</q-item-label>
                    </q-item-section>
                </q-item>
                <q-item>
                    <q-item-section>
                        <q-item-label class="text-primary">D-Recuo (m): {{ indentation }}</q-item-label>
                    </q-item-section>
                </q-item>
                <q-item>
                    <q-item-section>
                        <q-item-label class="text-primary">B-Altura do Obstáculo (m): {{ obstacle_height }}</q-item-label>
                    </q-item-section>
                </q-item>
            </q-list>
        </div>
        <!-- Endereço do Projeto -->
        <div class="q-pa-md" style="min-width: 300px;">
            <q-list bordered separator style="border-color: secondary;" class="text-primary">
                <q-item>
                    <q-item-section>
                        <q-item-label overline class="text-primary"><strong>Endereço do Projeto</strong></q-item-label>
                    </q-item-section>
                </q-item>
                <q-item>
                    <q-item-section>
                        <q-item-label class="text-primary">Cep: {{ zip_code }}</q-item-label>
                    </q-item-section>
                </q-item>
                <q-item>
                    <q-item-section>
                        <q-item-label class="text-primary">Rua: {{ street }}</q-item-label>
                    </q-item-section>
                </q-item>
                <q-item>
                    <q-item-section>
                        <q-item-label class="text-primary">Número: {{ number_street }}</q-item-label>
                    </q-item-section>
                </q-item>
                <q-item>
                    <q-item-section>
                        <q-item-label class="text-primary">Complemento: {{ complement }}</q-item-label>
                    </q-item-section>
                </q-item>
                <q-item>
                    <q-item-section>
                        <q-item-label class="text-primary">Bairro: {{ neighborhood }}</q-item-label>
                    </q-item-section>
                </q-item>
                <q-item>
                    <q-item-section>
                        <q-item-label class="text-primary">Cidade: {{ city_name }}</q-item-label>
                    </q-item-section>
                </q-item>
                <q-item>
                    <q-item-section>
                        <q-item-label class="text-primary">Estado: {{ state_name }}</q-item-label>
                    </q-item-section>
                </q-item>
            </q-list>
        </div>
        <div class="text-h6 text-primary text-bold q-ml-md">
          Anexar máquinas cadastradas
                </div>
                <q-separator class="bg-secondary" />

                <div class="q-pa-md">
                <!-- Select de Máquina -->
                <q-select
                outlined
                v-model="selectedMachine"
                :options="machineOptions"
                option-label="label"
                label="Selecione a Máquina"
                emit-value
                map-options
                dense
                />

                <!-- Exibir dados selecionados -->
                <div v-if="selectedMachineData" class="q-mt-md text-black">
                <div><strong>Categoria:</strong> {{ selectedMachineData.category_name }}</div>
                <div><strong>Marca:</strong> {{ selectedMachineData.brand }}</div>
                <div><strong>Nome:</strong> {{ selectedMachineData.name }}</div>
                <div><strong>Parâmetros:</strong> {{ selectedMachineData.parameters }}</div>
                <div><strong>Preço por hora:</strong> {{ selectedMachineData.price_per_hour }}</div>
                <div><strong>Preço horario especial:</strong> {{ selectedMachineData.special_hour_fee }}</div>
                </div>

                </div>
        <div class="flex justify-center">
            <q-btn class="q-ma-sm flex justify-center" label="Solicitar Orçamento" color="secondary" @click="on_submit" />
        </div>
    </q-page>
</template>

<script>
export default {
    name: 'ProjectRecomend',
    data() {
        return {
            client_email:'',
            client_phone: '',
            compactor_type:'',
            loader_size:'',
            excavator_size:'',
            machine_list: [],
            backhoe_type:'',
            client_name:'',
            selectedMachines: [],

            user_name: '',
            created_at: '',
            email: '',
            phone: '',
            project_name: '',
            project_description: '',
            name_machine_category: '',
            max_volume: '',
            count: '',
            weight: '',
            width: '',
            length: '',
            height: '',
            lifting_height: '',
            radius: '',
            clearance: '',
            indentation: '',
            obstacle_height: '',
            zip_code: '',
            street: '',
            number_street: '',
            complement: '',
            neighborhood: '',
            city_name: '',
            state_name: '',

            machineOptions: [],
            selectedMachine: null,
            selectedMachineData: null,
            company_uuid: '',
            uuid: '',
        };
    },
    mounted() {
        this.get_project_by_uuid();
        this.get_machine_company();
    },
    methods: {
        async get_project_by_uuid() {
            fetch(`http://localhost:5510/v1/project/${this.$route.params.project_uuid}`, {
                headers: { 'token': localStorage.getItem('access_token') }
            })
            .then(response => response.json())
            .then(data => {
                this.user_name = data.project.user_name;
                this.created_at = data.project.created_at;
                this.email = data.project.email;
                this.phone = data.project.phone;
                this.project_name = data.project.project_name;
                this.project_description = data.project.project_description;
                this.name_machine_category = data.project.name_machine_category;
                this.max_volume = data.project.max_volume;

                this.count = data.project.count;
                this.weight = data.project.weight;
                this.length = data.project.length;
                this.width = data.project.width;
                this.height = data.project.height;
                this.lifting_height = data.project.lifting_height;
                this.indentation = data.project.indentation;
                this.clearance = data.project.clearance;
                this.radius = data.project.radius;

                this.zip_code = data.project.zip_code;
                this.street = data.project.street;
                this.number_street = data.project.number_street;
                this.complement = data.project.complement;
                this.neighborhood = data.project.neighborhood;
                this.city_name = data.project.city_name;
                this.state_name = data.project.state_name;
                this.client_name = data.project.client_name;
                this.client_email = data.project.client_email;
                this.client_phone = data.project.client_phone;
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                this.$q.notify({
                    color: 'red-5',
                    textColor: 'white',
                    icon: 'cloud_done',
                    message: 'Nenhum orçamento cadastrado!'
                });
            });
        },
          async get_machine_company() {
            const company_uuid = localStorage.getItem('company_uuid');

            fetch(`http://localhost:5510/v1/machine/company/${company_uuid}`, {
                headers: { token: localStorage.getItem('access_token') },
            })
                .then((response) => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
                })
                .then((data) => {
                // Monta opções para o q-select
                this.machineOptions = data.machine.map((item) => ({
                    label: `${item.category_name} - ${item.brand}`,
                    value: item.uuid,
                    data: item, // guardar info completa para exibir depois
                }));
                })
                .catch((error) => {
                console.error('Erro ao buscar máquinas:', error);
                this.$q.notify({
                    color: 'red-5',
                    textColor: 'white',
                    icon: 'error',
                    message: 'Erro ao buscar máquinas!',
                });
                });
            },

      on_submit() {
        if (!this.selectedMachine) {
          this.$q.notify({
            color: 'red-5',
            textColor: 'white',
            icon: 'warning',
            message: 'Selecione pelo menos uma máquina.'
          });
          return;
        }

        // Encontra os dados da máquina selecionada
        const selectedMachineItem = this.machineOptions.find(item => item.value === this.selectedMachine);

        if (!selectedMachineItem) {
          this.$q.notify({
            color: 'red-5',
            textColor: 'white',
            icon: 'error',
            message: 'Máquina selecionada inválida.'
          });
          return;
        }

        const machine_list = [
          {
            machine_uuid: this.selectedMachine,
            company_id: selectedMachineItem.data.company_id
          }
        ];

        fetch(`http://localhost:5510/v1/budget/`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'token': localStorage.getItem('access_token'),
          },
          body: JSON.stringify({
            project_uuid: this.$route.params.project_uuid,
            machine_list
          }),
        })
          .then(response => {
            if (!response.ok) {
              throw new Error('Erro ao enviar.');
            }

            this.$q.notify({
              color: 'green-4',
              textColor: 'white',
              icon: 'cloud_done',
              message: 'Solicitação de orçamento enviada com sucesso!',
            });

            const company_uuid = localStorage.getItem('company_uuid');
            this.$router.push(`/dashboard/${company_uuid}`);
          })
          .catch((error) => {
            this.$q.notify({
              color: 'red-4',
              textColor: 'white',
              icon: 'error',
              message: error.message,
            });
          });
      }


    },
            watch: {
           selectedMachine(newVal) {
             if (newVal) {
               const selected = this.machineOptions.find((item) => item.value === newVal);
               this.selectedMachineData = selected ? selected.data : null;
             } else {
               this.selectedMachineData = null;
             }
           },
        }
};
</script>
