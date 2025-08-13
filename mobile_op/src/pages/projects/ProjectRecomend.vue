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
                Solicitar um orçamentos
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
                        <q-item-label class="text-primary">Nome do cliente: {{ user_name }}</q-item-label>
                    </q-item-section>
                </q-item>
                <q-item>
                    <q-item-section>
                        <q-item-label class="text-primary">Data inicial do Projeto: {{ created_at }}</q-item-label>
                    </q-item-section>
                </q-item>
                <q-item>
                    <q-item-section>
                        <q-item-label class="text-primary">E-mail: {{ email }}</q-item-label>
                    </q-item-section>
                </q-item>
                <q-item>
                    <q-item-section>
                        <q-item-label class="text-primary">Telefone: {{ phone }}</q-item-label>
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
        <!-- Lista de Equipamentos -->
        <p class="text-primary text-bold text-center q-pa-md text-h6">Escolha abaixo as locadoras que possuem o equipamentos que você precisa:</p>
        <div class="q-pa-md" style="max-width: 500px;">
            <q-list bordered separator style="border-color: secondary;">
                <q-item
                    v-for="(machine, index) in machine_list"
                    :key="index"
                    clickable
                    v-ripple
                    @click="selectMachine(machine)"
                >
                    <q-item-section avatar>
                        <q-avatar>
                            <img src="https://cdn.quasar.dev/img/boy-avatar.png">
                        </q-avatar>
                    </q-item-section>
                    <q-item-section>
                        
                        <q-item-label class="text-bold text-primary">Locador: {{ machine.company_name }}</q-item-label>
                        <q-item-label class="text-bold text-primary">Categoria: {{ machine.category_name }}</q-item-label>
                        <q-item-label v-if="isEscavadeira(machine)" class="text-bold text-primary">
                        {{ machine.parameters?.excavator_size }}
                        </q-item-label>
                        <q-item-label v-if="isPaCarregadeira(machine)" class="text-bold text-primary">
                        {{ machine.parameters?.loader_size }}
                        </q-item-label>
                        <q-item-label v-if="isRetroescavadeira(machine)" class="text-bold text-primary">
                        {{ machine.parameters?.backhoe_type }}
                        </q-item-label>
                        <q-item-label v-if="isMotoNiveladora(machine)" class="text-bold text-primary">
                        {{ machine.parameters?.compactor_type }}
                        </q-item-label>

                        <q-item-label class="text-caption text-primary">Marca: {{ machine.brand }}</q-item-label>
                        <q-item-label class="text-caption text-primary">Ano: {{ machine.year_manufacture }}</q-item-label>

                    </q-item-section>
                    <q-item-section side>
                        <q-checkbox :model-value="selectedMachines.includes(machine)" @update:model-value="toggleMachine(machine)" />
                    </q-item-section>
                </q-item>
            </q-list>
        </div>

        <div class="flex justify-center">
            <q-btn class="q-ma-sm flex justify-center" :label="machine_list.length ? 'Solicitar Orçamento' : 'Máquina Indisponível'" color="secondary" @click="on_submit" />
        </div>
    </q-page>
</template>

<script>
export default {
    name: 'ProjectRecomend',
    data() {
        return {
            compactor_type:'',
            loader_size:'',
            excavator_size:'',
            machine_list: [],
            backhoe_type:'',

            selectedMachines: [],

            selectedMachine: null,
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
        };
    },
    mounted() {
        this.get_project_by_uuid();
        this.get_machine_by_params();
    },
    computed: {
        hasDetailedInfo() {
            return this.count || this.weight || this.width || this.length || this.height || this.lifting_height || this.radius || this.clearance || this.indentation || this.obstacle_height;
        },
    },
    methods: {
        isEscavadeira(machine) {
        return machine.category_name === "Escavadeira";
        },
        isRetroescavadeira(machine) {
            return machine.category_name === "Retroescavadeira";
        },
        isMotoNiveladora(machine) {
            return machine.category_name === "Motoniveladora";
        },
        isPaCarregadeira(machine) {
            return machine.category_name === "Pá Carregadeira";
        },
        selectMachine(machine) {
            if (this.selectedMachine === machine) {
                this.selectedMachine = null;
            } else {
                this.selectedMachine = machine;
            }
        },
        updateSelection(machine) {
            const index = this.selectedMachines.findIndex(m => m.uuid === machine.uuid);
            if (index === -1) {
                this.selectedMachines.push(machine);
            } else {
                this.selectedMachines.splice(index, 1);
            }
        },


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

        async get_machine_by_params() {
        try {
            const response = await fetch(`http://localhost:5510/v1/machine/params/${this.$route.params.project_uuid}`, {
            headers: { 'token': localStorage.getItem('access_token') }
            });
            const data = await response.json();

            const machines = data.machines_by_project.flatMap(entry => entry.machines);
            this.machine_list = machines;

            this.excavator_size = data.machines_by_project[0].machines[0].parameters.excavator_size;
            this.loader_size = data.machines_by_project[0].machines[0].parameters.loader_size;
            this.backhoe_type = data.machines_by_project[0].machines[0].parameters.backhoe_type;
            this.compactor_type = data.machines_by_project[0].machines[0].parameters.compactor_type;




            this.name_machine_category = data.machines_by_project[0].machines[0].category_name;

        } catch (error) {
            console.error('Error fetching machines:', error);
            this.$q.notify({
            color: 'red-5',
            textColor: 'white',
            icon: 'cloud_done',
            message: 'Erro ao carregar a lista de equipamentos!'
            });
        }
        },
        toggleMachine(machine) {
            const index = this.selectedMachines.findIndex(m => m.uuid === machine.uuid);
            if (index > -1) {
                this.selectedMachines.splice(index, 1); 
            } else {
                this.selectedMachines.push(machine); 
            }
            },

            on_submit() {
                if (!this.selectedMachines.length) {
                    this.$q.notify({
                    color: 'red-5',
                    textColor: 'white',
                    icon: 'warning',
                    message: 'Selecione pelo menos uma máquina.'
                    });
                    return;
                }

                const machine_list = this.selectedMachines.map(machine => ({
                    machine_uuid: machine.uuid
                }));

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
                    message: 'solicitaçao de orçamento enviado com sucesso!',
                    });

                    this.$router.push(`/user/service`);
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

    }
};
</script>
