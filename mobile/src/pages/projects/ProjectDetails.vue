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
				Detalhes do Projeto
			</div>
		</div>

        <q-separator class="bg-secondary"/>

        <!-- Introdução -->
        <div class="q-pa-md" style="min-width: 300px;">
            <q-list bordered separator style="border-color: secondary;" class="text-primary">
                <q-item>
                    <q-item-section>
                        <q-item-label overline class="text-primary"><strong>Detalhes</strong></q-item-label>
                        <q-item-label caption class="text-primary">Informações detalhadas do projeto que você criou</q-item-label>
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
                        <q-item-label class="text-primary">Nome do Cliente: {{ user_name }}</q-item-label>
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
                        <q-item-label class="text-primary">Data de Expectativa: {{ expected_date }}</q-item-label>
                    </q-item-section>
                </q-item>
                <q-item>
                    <q-item-section>
                        <q-item-label class="text-primary">Nome do Projeto: {{ project_name }}</q-item-label>
                    </q-item-section>
                </q-item>
                <q-item>
                    <q-item-section>
                        <q-item-label class="text-primary">Descrição: {{ project_description }}</q-item-label>
                    </q-item-section>
                </q-item>
<!--                <q-item>-->
<!--                    <q-item-section>-->
<!--                        <q-item-label class="text-primary">Categoria: {{ name_machine_category }}</q-item-label>-->
<!--                    </q-item-section>-->
<!--                </q-item>-->
            </q-list>
        </div>

        <!-- Informações Detalhadas -->
<!--        <div class="q-pa-md" style="min-width: 300px;">-->
<!--            <q-list bordered separator style="border-color: secondary;" class="text-primary">-->
<!--                <q-item>-->
<!--                    <q-item-section>-->
<!--                        <q-item-label overline class="text-primary"><strong>Informações Detalhadas</strong></q-item-label>-->
<!--                    </q-item-section>-->
<!--                </q-item>-->
<!--                <q-item>-->
<!--                    <q-item-section>-->
<!--                        <q-item-label class="text-primary">Capacidade máxima nomimal de elevação (Kg / tn) {{ maximum_nominal_lifting_capacity }}</q-item-label>-->
<!--                    </q-item-section>-->
<!--                </q-item>-->
<!--                <q-item>-->
<!--                    <q-item-section>-->
<!--                        <q-item-label class="text-primary">Capacidade máxima nomimal de alcance vertical (m) {{ maximum_nominal_vertical_reach_capacity }}</q-item-label>-->
<!--                    </q-item-section>-->
<!--                </q-item>-->
<!--                <q-item>-->
<!--                    <q-item-section>-->
<!--                        <q-item-label class="text-primary">Alcance máximo sem JIB (m) {{ maximum_reach_without_jib }}</q-item-label>-->
<!--                    </q-item-section>-->
<!--                </q-item>-->
<!--                <q-item>-->
<!--                    <q-item-section>-->
<!--                        <q-item-label class="text-primary">Alcance máximo vertical (m) {{ maximum_vertical_reach }}</q-item-label>-->
<!--                    </q-item-section>-->
<!--                </q-item>-->
<!--                <q-item>-->
<!--                    <q-item-section>-->
<!--                        <q-item-label class="text-primary">Alcance máximo horizontal (m) {{ maximum_horizontal_reach }}</q-item-label>-->
<!--                    </q-item-section>-->
<!--                </q-item>-->
<!--            </q-list>-->
<!--        </div>-->
          <!-- Informações Detalhadas -->
            <div v-if="project_stages && project_stages.length" class="text-black">
              <div
                v-for="(stage, index) in project_stages"
                :key="index"
                class="q-mb-md"
              >
                <q-separator spaced />
                <div class="text-h6 q-ml-md">
                  {{ stage.name_machine_category }}
                </div>

                <div v-if="stage.parameters">
                  <!-- Se só tiver 1 parâmetro -->
                  <div v-if="Object.keys(stage.parameters).length === 1">
                    <div
                      v-for="(value, key) in stage.parameters"
                      :key="key"
                      class="q-ml-md q-mt-sm"
                    >
                      <p class="text-bold q-mb-xs">Tamanho:</p>
                      <p class="q-mb-md">{{ value }}</p>
                    </div>
                  </div>

                  <!-- Se tiver mais de 1 parâmetro, trata personalizado -->
                  <div v-else class="q-ml-md q-mt-sm">
                    <p v-if="stage.parameters.maximum_horizontal_reach">Alcance máximo horizontal (m): {{ stage.parameters.maximum_horizontal_reach }}</p>
                    <p v-if="stage.parameters.maximum_nominal_lifting_capacity">Capacidade máxima nomimal de elevação (Kg / tn): {{ stage.parameters.maximum_nominal_lifting_capacity }}</p>
                    <p v-if="stage.parameters.maximum_nominal_vertical_reach_capacity">Capacidade máxima nomimal de alcance vertical (m): {{ stage.parameters.maximum_nominal_vertical_reach_capacity }}</p>
                    <p v-if="stage.parameters.maximum_reach_without_jib">Alcance máximo sem JIB (m): {{ stage.parameters.maximum_reach_without_jib }}</p>
                    <p v-if="stage.parameters.maximum_vertical_reach">Alcance máximo vertical (m): {{ stage.parameters.maximum_vertical_reach }}</p>
                  </div>
                </div>
              </div>
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
    </q-page>
</template>


<script>
export default {
    name: 'ProjectDetails',
    data() {
        return {
            user_name: '',
            created_at: '',
            email: '',
            phone: '',
            expected_date: '',
            project_name: '',
            project_description: '',
            name_machine_category: '',
            obstacle_height: '',
            zip_code: '',
            street: '',
            number_street: '',
            complement: '',
            neighborhood: '',
            city_name: '',
            state_name: '',

            project_stages:'',

            maximum_horizontal_reach:'',
            maximum_nominal_lifting_capacity:'',
            maximum_nominal_vertical_reach_capacity:'',
            maximum_vertical_reach:'',
            maximum_reach_without_jib:'',
        };
    },
    mounted() {
        this.get_project_by_uuid();
    },
    methods: {


        async get_project_by_uuid() {
            fetch(`https://fortis-api.55technology.com/v1/project/${this.$route.params.project_uuid}`, {
                headers: { 'token': localStorage.getItem('access_token') }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                this.user_name = data.project.user_name;
                this.created_at = data.project.created_at;
                this.email = data.project.email;
                this.phone = data.project.phone;
                this.expected_date = data.project.expected_date;
                this.project_name = data.project.project_name;
                this.project_description = data.project.project_description;
                this.name_machine_category = data.project_stages[0].name_machine_category;

                this.maximum_horizontal_reach = data.project_stages[0].parameters.maximum_horizontal_reach;
                this.maximum_nominal_lifting_capacity = data.project_stages[0].parameters.maximum_nominal_lifting_capacity;
                this.maximum_nominal_vertical_reach_capacity = data.project_stages[0].parameters.maximum_nominal_vertical_reach_capacity;
                this.maximum_reach_without_jib = data.project_stages[0].parameters.maximum_reach_without_jib;
                this.maximum_vertical_reach = data.project_stages[0].parameters.maximum_vertical_reach;

                this.zip_code = data.project.zip_code;
                this.street = data.project.street;
                this.number_street = data.project.number_street;
                this.complement = data.project.complement;
                this.neighborhood = data.project.neighborhood;
                this.city_name = data.project.city_name;
                this.state_name = data.project.state_name;
                this.name_machine_category = data.project_stages.map(
                  (stage) => stage.name_machine_category
                );
                this.project_stages = data.project_stages;
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
    },
};
</script>
