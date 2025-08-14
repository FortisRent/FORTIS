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
                    <q-img :src="getMachineImage(category.category_name)" width="80px" class="q-mr-md" />
                    <span class="text-h6 text-bold text-primary" style="line-height: 1.2">{{ category.category_name
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
              <q-card>
                <q-card-section>
                  <!-- PARAMETROS TRELIÇADOS -->
                  <div v-if="
                    selection.selected_machines.includes(category.uuid) &&
                    category.category_name ===
                    'Guindaste Móvel de lança treliçada sobre esteiras'
                  ">
                    <q-input label="JIB (m)" v-model="jib_TreliçadoEsteira" type="number" />
                    <q-input label="Peso Máximo (kg / tn)" v-model="max_weight_TreliçadoEsteira" type="number" />
                    <q-input label="Altura Máxima (m)" v-model="max_height_TreliçadoEsteira" type="number" />
                    <q-input label="Raio Máximo (m)" v-model="max_radius_TreliçadoEsteira" type="number" />

                    <q-input label="Capacidade máxima nomimal de elevação (Kg / tn)" v-model="maximum_nominal_lifting_capacity_TreliçadoEsteira
                      " type="number" />
                    <q-input label="Capacidade máxima nomimal de alcance vertical (m)" v-model="maximum_nominal_vertical_reach_capacity_TreliçadoEsteira
                      " type="number" />
                    <q-input label="Alcance máximo sem JIB (m)" v-model="maximum_reach_without_jib_TreliçadoEsteira"
                      type="number" />
                    <q-input label="Alcance máximo vertical (m)" v-model="maximum_vertical_reach_TreliçadoEsteira"
                      type="number" />
                    <q-input label="Alcance máximo horizontal (m)" v-model="maximum_horizontal_reach_TreliçadoEsteira"
                      type="number" />
                  </div>

                  <div v-if="
                    selection.selected_machines.includes(category.uuid) &&
                    category.category_name ===
                    'Guindastes móvel de lança treliçada sobre rodas'
                  ">
                    <q-input label="JIB (m)" v-model="jib_TreliçadoRoda" type="number" />
                    <q-input label="Peso Máximo (kg / tn)" v-model="max_weight_TreliçadoRoda" type="number" />
                    <q-input label="Altura Máxima (m)" v-model="max_height_TreliçadoRoda" type="number" />
                    <q-input label="Raio Máximo (m)" v-model="max_radius_TreliçadoRoda" type="number" />

                    <q-input label="Capacidade máxima nomimal de elevação (Kg / tn)"
                      v-model="maximum_nominal_lifting_capacity_TreliçadoRoda" type="number" />
                    <q-input label="Capacidade máxima nomimal de alcance vertical (m)" v-model="maximum_nominal_vertical_reach_capacity_TreliçadoRoda
                      " type="number" />
                    <q-input label="Alcance máximo sem JIB (m)" v-model="maximum_reach_without_jib_TreliçadoRoda"
                      type="number" />
                    <q-input label="Alcance máximo vertical (m)" v-model="maximum_vertical_reach_TreliçadoRoda"
                      type="number" />
                    <q-input label="Alcance máximo horizontal (m)" v-model="maximum_horizontal_reach_TreliçadoRoda"
                      type="number" />
                  </div>

                  <!-- PARAMETROS TELESCÓPICOS -->
                  <div v-if="
                    selection.selected_machines.includes(category.uuid) &&
                    category.category_name ===
                    'Guindastes móvel de lança telescópica sobre esteiras'
                  ">
                    <q-input label="JIB (m)" v-model="jib_TelescopicoEsteira" type="number" />
                    <q-input label="Peso Máximo (kg / tn)" v-model="max_weight_TelescopicoEsteira" type="number" />
                    <q-input label="Altura Máxima (m)" v-model="max_height_TelescopicoEsteira" type="number" />
                    <q-input label="Raio Máximo (m)" v-model="max_radius_TelescopicoEsteira" type="number" />

                    <q-input label="Capacidade máxima nomimal de elevação (Kg / tn)" v-model="maximum_nominal_lifting_capacity_TelescopicoEsteira
                      " type="number" />
                    <q-input label="Capacidade máxima nomimal de alcance vertical (m)" v-model="maximum_nominal_vertical_reach_capacity_TelescopicoEsteira
                      " type="number" />
                    <q-input label="Alcance máximo sem JIB (m)" v-model="maximum_reach_without_jib_TelescopicoEsteira"
                      type="number" />
                    <q-input label="Alcance máximo vertical (m)" v-model="maximum_vertical_reach_TelescopicoEsteira"
                      type="number" />
                    <q-input label="Alcance máximo horizontal (m)" v-model="maximum_horizontal_reach_TelescopicoEsteira"
                      type="number" />
                  </div>

                  <div v-if="
                    selection.selected_machines.includes(category.uuid) &&
                    category.category_name ===
                    'Guindastes móvel de lança telescópica sobre Rodas Rough Terrain (RT)'
                  ">
                    <q-input label="JIB (m)" v-model="jib_TelescopicoRT" type="number" />
                    <q-input label="Peso Máximo (kg / tn)" v-model="max_weight_TelescopicoRT" type="number" />
                    <q-input label="Altura Máxima (m)" v-model="max_height_TelescopicoRT" type="number" />
                    <q-input label="Raio Máximo (m)" v-model="max_radius_TelescopicoRT" type="number" />

                    <q-input label="Capacidade máxima nomimal de elevação (Kg / tn)"
                      v-model="maximum_nominal_lifting_capacity_TelescopicoRT" type="number" />
                    <q-input label="Capacidade máxima nomimal de alcance vertical (m)" v-model="maximum_nominal_vertical_reach_capacity_TelescopicoRT
                      " type="number" />
                    <q-input label="Alcance máximo sem JIB (m)" v-model="maximum_reach_without_jib_TelescopicoRT"
                      type="number" />
                    <q-input label="Alcance máximo vertical (m)" v-model="maximum_vertical_reach_TelescopicoRT"
                      type="number" />
                    <q-input label="Alcance máximo horizontal (m)" v-model="maximum_horizontal_reach_TelescopicoRT"
                      type="number" />
                  </div>

                  <div v-if="
                    selection.selected_machines.includes(category.uuid) &&
                    category.category_name ===
                    'Guindastes móvel de lança telescópica sobre Rodas All Terrain (AT)'
                  ">
                    <q-input label="JIB (m)" v-model="jib_TelescopicoAT" type="number" />
                    <q-input label="Peso Máximo (kg / tn)" v-model="max_weight_TelescopicoAT" type="number" />
                    <q-input label="Altura Máxima (m)" v-model="max_height_TelescopicoAT" type="number" />
                    <q-input label="Raio Máximo (m)" v-model="max_radius_TelescopicoAT" type="number" />

                    <q-input label="Capacidade máxima nomimal de elevação (Kg / tn)"
                      v-model="maximum_nominal_lifting_capacity_TelescopicoAT" type="number" />
                    <q-input label="Capacidade máxima nomimal de alcance vertical (m)" v-model="maximum_nominal_vertical_reach_capacity_TelescopicoAT
                      " type="number" />
                    <q-input label="Alcance máximo sem JIB (m)" v-model="maximum_reach_without_jib_TelescopicoAT"
                      type="number" />
                    <q-input label="Alcance máximo vertical (m)" v-model="maximum_vertical_reach_TelescopicoAT"
                      type="number" />
                    <q-input label="Alcance máximo horizontal (m)" v-model="maximum_horizontal_reach_TelescopicoAT"
                      type="number" />
                  </div>

                  <div v-if="
                    selection.selected_machines.includes(category.uuid) &&
                    category.category_name ===
                    'Guindaste móvel de lança telescópica articulada (Munck) sobre caminhão com carroceria'
                  ">
                    <q-input label="JIB (m)" v-model="jib_TelescopicoCaminhaoCarroceria" type="number" />
                    <q-input label="Peso Máximo (kg / tn)" v-model="max_weight_TelescopicoCaminhaoCarroceria"
                      type="number" />
                    <q-input label="Altura Máxima (m)" v-model="max_height_TelescopicoCaminhaoCarroceria"
                      type="number" />
                    <q-input label="Raio Máximo (m)" v-model="max_radius_TelescopicoCaminhaoCarroceria" type="number" />

                    <q-input label="Capacidade máxima nomimal de elevação (Kg / tn)" v-model="maximum_nominal_lifting_capacity_TelescopicoCaminhaoCarroceria
                      " type="number" />
                    <q-input label="Capacidade máxima nomimal de alcance vertical (m)" v-model="maximum_nominal_vertical_reach_capacity_TelescopicoCaminhaoCarroceria
                      " type="number" />
                    <q-input label="Alcance máximo sem JIB (m)" v-model="maximum_reach_without_jib_TelescopicoCaminhaoCarroceria
                      " type="number" />
                    <q-input label="Alcance máximo vertical (m)" v-model="maximum_vertical_reach_TelescopicoCaminhaoCarroceria
                      " type="number" />
                    <q-input label="Alcance máximo horizontal (m)" v-model="maximum_horizontal_reach_TelescopicoCaminhaoCarroceria
                      " type="number" />
                  </div>

                  <div v-if="
                    selection.selected_machines.includes(category.uuid) &&
                    category.category_name ===
                    'Guindastes móvel de lança telescópica articulada(Munck) sobre cavalo trator'
                  ">
                    <q-input label="JIB (m)" v-model="jib_TelescopicoCavaloTrator" type="number" />
                    <q-input label="Peso Máximo (kg / tn)" v-model="max_weight_TelescopicoCavaloTrator" type="number" />
                    <q-input label="Altura Máxima (m)" v-model="max_height_TelescopicoCavaloTrator" type="number" />
                    <q-input label="Raio Máximo (m)" v-model="max_radius_TelescopicoCavaloTrator" type="number" />

                    <q-input label="Capacidade máxima nomimal de elevação (Kg / tn)" v-model="maximum_nominal_lifting_capacity_TelescopicoCavaloTrator
                      " type="number" />
                    <q-input label="Capacidade máxima nomimal de alcance vertical (m)" v-model="maximum_nominal_vertical_reach_capacity_TelescopicoCavaloTrator
                      " type="number" />
                    <q-input label="Alcance máximo sem JIB (m)" v-model="maximum_reach_without_jib_TelescopicoCavaloTrator
                      " type="number" />
                    <q-input label="Alcance máximo vertical (m)"
                      v-model="maximum_vertical_reach_TelescopicoCavaloTrator" type="number" />
                    <q-input label="Alcance máximo horizontal (m)"
                      v-model="maximum_horizontal_reach_TelescopicoCavaloTrator" type="number" />
                  </div>

                  <div v-if="
                    selection.selected_machines.includes(category.uuid) &&
                    category.category_name ===
                    'Guindastes móvel de lança telescópica articulada(Munck) sobre cavalo trator e carreta'
                  ">
                    <q-input label="JIB (m)" v-model="jib_TelescopicoCavaloTratorCarreta" type="number" />
                    <q-input label="Peso Máximo (kg / tn)" v-model="max_weight_TelescopicoCavaloTratorCarreta"
                      type="number" />
                    <q-input label="Altura Máxima (m)" v-model="max_height_TelescopicoCavaloTratorCarreta"
                      type="number" />
                    <q-input label="Raio Máximo (m)" v-model="max_radius_TelescopicoCavaloTratorCarreta"
                      type="number" />

                    <q-input label="Capacidade máxima nomimal de elevação (Kg / tn)" v-model="maximum_nominal_lifting_capacity_TelescopicoCavaloTratorCarreta
                      " type="number" />
                    <q-input label="Capacidade máxima nomimal de alcance vertical (m)" v-model="maximum_nominal_vertical_reach_capacity_TelescopicoCavaloTratorCarreta
                      " type="number" />
                    <q-input label="Alcance máximo sem JIB (m)" v-model="maximum_reach_without_jib_TelescopicoCavaloTratorCarreta
                      " type="number" />
                    <q-input label="Alcance máximo vertical (m)" v-model="maximum_vertical_reach_TelescopicoCavaloTratorCarreta
                      " type="number" />
                    <q-input label="Alcance máximo horizontal (m)" v-model="maximum_horizontal_reach_TelescopicoCavaloTratorCarreta
                      " type="number" />
                  </div>

                  <!-- DESCRIÇÃO -->
                  <div class="text-bold text-primary q-mt-md">
                    <span v-if="
                      category.category_name ===
                      'Guindaste Móvel de lança treliçada sobre esteiras'
                    ">ideal para operações pesadas em terrenos irregulares e de
                      difícil acesso.</span>
                    <span v-if="
                      category.category_name ===
                      'Guindastes móvel de lança treliçada sobre rodas'
                    ">indicado para deslocamentos rápidos e operações em
                      terrenos pavimentados.</span>
                    <span v-if="
                      category.category_name ===
                      'Guindastes móvel de lança telescópica sobre esteiras'
                    ">ideal para áreas de difícil acesso e operações que exigem
                      alcance variável.</span>
                    <span v-if="
                      category.category_name ===
                      'Guindastes móvel de lança telescópica sobre Rodas Rough Terrain (RT)'
                    ">projetado para terrenos acidentados e de difícil
                      acesso.</span>
                    <span v-if="
                      category.category_name ===
                      'Guindastes móvel de lança telescópica sobre Rodas All Terrain (AT)'
                    ">ideal para deslocamentos em rodovias e operações em
                      terrenos variados.</span>
                    <span v-if="
                      category.category_name ===
                      'Guindaste móvel de lança telescópica articulada (Munck) sobre caminhão com carroceria'
                    ">ideal para carga, descarga e transporte integrado.</span>
                    <span v-if="
                      category.category_name ===
                      'Guindastes móvel de lança telescópica articulada(Munck) sobre cavalo trator'
                    ">indicado para operações de carga e descarga em longas
                      distâncias.</span>
                    <span v-if="
                      category.category_name ===
                      'Guindastes móvel de lança telescópica articulada(Munck) sobre cavalo trator e carreta'
                    ">ideal para transporte e movimentação de cargas de grandes
                      dimensões.</span>
                  </div>

                  <!-- OPERADOR -->
                  <div class="q-mt-md" v-if="selection.selected_machines.includes(category.uuid)">
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
import GuindasteMovelDeLancaTrelicadaSobreEsteirasImg from '../../assets/guindaste-movel-lanca-trelicada-sobre-esteiras.png';
import GuindasteMovelDeLancaTrelicadaSobreRodasImg from '../../assets/guindaste-movel-lanca-trelicada-sobre-rodas.png';
import GuindasteMovelDeLancaTelescopicaSobreEsteirasImg from '../../assets/guindaste-movel-lanca-telescopica-sobre-esteiras.png';
import GuindastesMovelDeLancaTelescopicaSobreRodasRoughTerrain from '../../assets/guindastes-movel-de-lanca-telescopica-sobre-rodas-rough-terrain.png';
import GuindastesMovelDeLancaTelescopicaSobreRodasAllTerrain from '../../assets/guindastes-movel-de-lanca-telescopica-sobre-rodas-all-terrain.png';
import GuindasteMovelDeLancaTelescopicaArticuladaMunckSobreCaminhaoComCarroceria from '../../assets/guindaste-movel-de-lanca-telescopica-articulada-sobre-caminhao-com-carroceria.png';
import GuindastesMovelDeKLancaTelescopicaArticuladaMunckSobreCavaloTrator from '../../assets/guindastes-movel-de-lanca-telescopica-articulada-sobre-cavalo-trator.png';
import GuindastesMovelDeLancaTelescopicaArticuladaMunckSobreCavaloTratorECarreta from '../../assets/guindastes-movel-de-lanca-telescopica -articulada-sobre-cavalo-trator-e-carreta.png';

export default {
  data() {
    return {
      loading: true,
      loadingMachines: false,
      expandedItems: {},
      inputValue: '',
      project_name_options: [],
      all_project_names: [],
      // parametro do Guindaste Móvel de lança treliçada sobre esteiras
      jib_TreliçadoEsteira: '',
      max_weight_TreliçadoEsteira: '',
      max_height_TreliçadoEsteira: '',
      max_radius_TreliçadoEsteira: '',

      maximum_nominal_lifting_capacity_TreliçadoEsteira: '',
      maximum_nominal_vertical_reach_capacity_TreliçadoEsteira: '',
      maximum_reach_without_jib_TreliçadoEsteira: '',
      maximum_vertical_reach_TreliçadoEsteira: '',
      maximum_horizontal_reach_TreliçadoEsteira: '',

      // Guindastes móvel de lança treliçada sobre rodas
      jib_TreliçadoRoda: '',
      max_weight_TreliçadoRoda: '',
      max_height_TreliçadoRoda: '',
      max_radius_TreliçadoRoda: '',

      maximum_nominal_lifting_capacity_TreliçadoRoda: '',
      maximum_nominal_vertical_reach_capacity_TreliçadoRoda: '',
      maximum_reach_without_jib_TreliçadoRoda: '',
      maximum_vertical_reach_TreliçadoRoda: '',
      maximum_horizontal_reach_TreliçadoRoda: '',

      // Guindastes móvel de lança telescópica sobre esteiras
      jib_TelescopicoEsteira: '',
      max_weight_TelescopicoEsteira: '',
      max_height_TelescopicoEsteira: '',
      max_radius_TelescopicoEsteira: '',

      maximum_nominal_lifting_capacity_TelescopicoEsteira: '',
      maximum_nominal_vertical_reach_capacity_TelescopicoEsteira: '',
      maximum_reach_without_jib_TelescopicoEsteira: '',
      maximum_vertical_reach_TelescopicoEsteira: '',
      maximum_horizontal_reach_TelescopicoEsteira: '',

      // Guindastes móvel de lança telescópica sobre Rodas Rough Terrain (RT)
      jib_TelescopicoRT: '',
      max_weight_TelescopicoRT: '',
      max_height_TelescopicoRT: '',
      max_radius_TelescopicoRT: '',

      maximum_nominal_lifting_capacity_TelescopicoRT: '',
      maximum_nominal_vertical_reach_capacity_TelescopicoRT: '',
      maximum_reach_without_jib_TelescopicoRT: '',
      maximum_vertical_reach_TelescopicoRT: '',
      maximum_horizontal_reach_TelescopicoRT: '',

      // Guindastes móvel de lança telescópica sobre Rodas All Terrain (AT)
      jib_TelescopicoAT: '',
      max_weight_TelescopicoAT: '',
      max_height_TelescopicoAT: '',
      max_radius_TelescopicoAT: '',

      maximum_nominal_lifting_capacity_TelescopicoAT: '',
      maximum_nominal_vertical_reach_capacity_TelescopicoAT: '',
      maximum_reach_without_jib_TelescopicoAT: '',
      maximum_vertical_reach_TelescopicoAT: '',
      maximum_horizontal_reach_TelescopicoAT: '',

      // Guindaste móvel de lança telescópica articulada (Munck) sobre caminhão com carroceria
      jib_TelescopicoCaminhaoCarroceria: '',
      max_weight_TelescopicoCaminhaoCarroceria: '',
      max_height_TelescopicoCaminhaoCarroceria: '',
      max_radius_TelescopicoCaminhaoCarroceria: '',

      maximum_nominal_lifting_capacity_TelescopicoCaminhaoCarroceria: '',
      maximum_nominal_vertical_reach_capacity_TelescopicoCaminhaoCarroceria: '',
      maximum_reach_without_jib_TelescopicoCaminhaoCarroceria: '',
      maximum_vertical_reach_TelescopicoCaminhaoCarroceria: '',
      maximum_horizontal_reach_TelescopicoCaminhaoCarroceria: '',

      // Guindastes móvel de lança telescópica articulada(Munck) sobre cavalo trator TelescopicoCavaloTratorCarreta
      jib_TelescopicoCavaloTrator: '',
      max_weight_TelescopicoCavaloTrator: '',
      max_height_TelescopicoCavaloTrator: '',
      max_radius_TelescopicoCavaloTrator: '',

      maximum_nominal_lifting_capacity_TelescopicoCavaloTrator: '',
      maximum_nominal_vertical_reach_capacity_TelescopicoCavaloTrator: '',
      maximum_reach_without_jib_TelescopicoCavaloTrator: '',
      maximum_vertical_reach_TelescopicoCavaloTrator: '',
      maximum_horizontal_reach_TelescopicoCavaloTrator: '',

      // Guindastes móvel de lança telescópica articulada(Munck) sobre cavalo trator e carreta
      jib_TelescopicoCavaloTratorCarreta: '',
      max_weight_TelescopicoCavaloTratorCarreta: '',
      max_height_TelescopicoCavaloTratorCarreta: '',
      max_radius_TelescopicoCavaloTratorCarreta: '',

      maximum_nominal_lifting_capacity_TelescopicoCavaloTratorCarreta: '',
      maximum_nominal_vertical_reach_capacity_TelescopicoCavaloTratorCarreta:
        '',
      maximum_reach_without_jib_TelescopicoCavaloTratorCarreta: '',
      maximum_vertical_reach_TelescopicoCavaloTratorCarreta: '',
      maximum_horizontal_reach_TelescopicoCavaloTratorCarreta: '',

      machine_selections: [
        {
          id: 1,
          category_uuid: null,
          category_list: [],
          selected_machines: [],
          need_operator_map: {},
        },
      ],
      next_machine_id: 2,

      name: null,
      description: null,
      machine_category_list: [],
      zip_code: '',
      street: '',
      number_street: '',
      complement: '',
      neighborhood: '',
      city_name: null,
      state_name: null,
    };
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
        'Guindaste Móvel de lança treliçada sobre esteiras':
          GuindasteMovelDeLancaTrelicadaSobreEsteirasImg,
        'Guindastes móvel de lança treliçada sobre rodas':
          GuindasteMovelDeLancaTrelicadaSobreRodasImg,
        'Guindastes móvel de lança telescópica sobre esteiras':
          GuindasteMovelDeLancaTelescopicaSobreEsteirasImg,
        'Guindastes móvel de lança telescópica sobre Rodas Rough Terrain (RT)':
          GuindastesMovelDeLancaTelescopicaSobreRodasRoughTerrain,
        'Guindastes móvel de lança telescópica sobre Rodas All Terrain (AT)':
          GuindastesMovelDeLancaTelescopicaSobreRodasAllTerrain,
        'Guindaste móvel de lança telescópica articulada (Munck) sobre caminhão com carroceria':
          GuindasteMovelDeLancaTelescopicaArticuladaMunckSobreCaminhaoComCarroceria,
        'Guindastes móvel de lança telescópica articulada(Munck) sobre cavalo trator':
          GuindastesMovelDeKLancaTelescopicaArticuladaMunckSobreCavaloTrator,
        'Guindastes móvel de lança telescópica articulada(Munck) sobre cavalo trator e carreta':
          GuindastesMovelDeLancaTelescopicaArticuladaMunckSobreCavaloTratorECarreta,
      };
      return images[machineName] || '';
    },

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
          this.machine_category_list = data.machine_category_group.map(
            (category) => ({
              category_name: category.category_name,
              uuid: category.uuid,
            })
          );
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
    async get_machine_category_by_group_uuid(index) {
      const category_uuid = this.machine_selections[index].category_uuid;
      if (!category_uuid) return;

      this.loadingMachines = true;
      await fetch(
        `https://fortis-api.55technology.com/v1/machine/category/group/${category_uuid}`,
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
          const machineItem = {
            machine_category_uuid: uuid,
            need_operator: selection.need_operator_map[uuid] ?? 0,
            parameters,
          };

          if (
            category.category_name ===
            'Guindaste Móvel de lança treliçada sobre esteiras'
          ) {
            machineItem.jib = this.jib_TreliçadoEsteira;
            machineItem.max_weight = this.max_weight_TreliçadoEsteira;
            machineItem.max_height = this.max_height_TreliçadoEsteira;
            machineItem.max_radius = this.max_radius_TreliçadoEsteira;

            parameters.maximum_nominal_lifting_capacity =
              this.maximum_nominal_lifting_capacity_TreliçadoEsteira;
            parameters.maximum_nominal_vertical_reach_capacity =
              this.maximum_nominal_vertical_reach_capacity_TreliçadoEsteira;
            parameters.maximum_reach_without_jib =
              this.maximum_reach_without_jib_TreliçadoEsteira;
            parameters.maximum_vertical_reach =
              this.maximum_vertical_reach_TreliçadoEsteira;
            parameters.maximum_horizontal_reach =
              this.maximum_horizontal_reach_TreliçadoEsteira;
          } else if (
            category.category_name ===
            'Guindastes móvel de lança treliçada sobre rodas'
          ) {
            machineItem.jib = this.jib_TreliçadoRoda;
            machineItem.max_weight = this.max_weight_TreliçadoRoda;
            machineItem.max_height = this.max_height_TreliçadoRoda;
            machineItem.max_radius = this.max_radius_TreliçadoRoda;

            parameters.maximum_nominal_lifting_capacity =
              this.maximum_nominal_lifting_capacity_TreliçadoRoda;
            parameters.maximum_nominal_vertical_reach_capacity =
              this.maximum_nominal_vertical_reach_capacity_TreliçadoRoda;
            parameters.maximum_reach_without_jib =
              this.maximum_reach_without_jib_TreliçadoRoda;
            parameters.maximum_vertical_reach =
              this.maximum_vertical_reach_TreliçadoRoda;
            parameters.maximum_horizontal_reach =
              this.maximum_horizontal_reach_TreliçadoRoda;
          } else if (
            category.category_name ===
            'Guindastes móvel de lança telescópica sobre esteiras'
          ) {
            machineItem.jib = this.jib_TelescopicoEsteira;
            machineItem.max_weight = this.max_weight_TelescopicoEsteira;
            machineItem.max_height = this.max_height_TelescopicoEsteira;
            machineItem.max_radius = this.max_radius_TelescopicoEsteira;

            parameters.maximum_nominal_lifting_capacity =
              this.maximum_nominal_lifting_capacity_TelescopicoEsteira;
            parameters.maximum_nominal_vertical_reach_capacity =
              this.maximum_nominal_vertical_reach_capacity_TelescopicoEsteira;
            parameters.maximum_reach_without_jib =
              this.maximum_reach_without_jib_TelescopicoEsteira;
            parameters.maximum_vertical_reach =
              this.maximum_vertical_reach_TelescopicoEsteira;
            parameters.maximum_horizontal_reach =
              this.maximum_horizontal_reach_TelescopicoEsteira;
          } else if (
            category.category_name ===
            'Guindastes móvel de lança telescópica sobre Rodas Rough Terrain (RT)'
          ) {
            machineItem.jib = this.jib_TelescopicoRT;
            machineItem.max_weight = this.max_weight_TelescopicoRT;
            machineItem.max_height = this.max_height_TelescopicoRT;
            machineItem.max_radius = this.max_radius_TelescopicoRT;

            parameters.maximum_nominal_lifting_capacity =
              this.maximum_nominal_lifting_capacity_TelescopicoRT;
            parameters.maximum_nominal_vertical_reach_capacity =
              this.maximum_nominal_vertical_reach_capacity_TelescopicoRT;
            parameters.maximum_reach_without_jib =
              this.maximum_reach_without_jib_TelescopicoRT;
            parameters.maximum_vertical_reach =
              this.maximum_vertical_reach_TelescopicoRT;
            parameters.maximum_horizontal_reach =
              this.maximum_horizontal_reach_TelescopicoRT;
          } else if (
            category.category_name ===
            'Guindastes móvel de lança telescópica sobre Rodas All Terrain (AT)'
          ) {
            machineItem.jib = this.jib_TelescopicoAT;
            machineItem.max_weight = this.max_weight_TelescopicoAT;
            machineItem.max_height = this.max_height_TelescopicoAT;
            machineItem.max_radius = this.max_radius_TelescopicoAT;

            parameters.maximum_nominal_lifting_capacity =
              this.maximum_nominal_lifting_capacity_TelescopicoAT;
            parameters.maximum_nominal_vertical_reach_capacity =
              this.maximum_nominal_vertical_reach_capacity_TelescopicoAT;
            parameters.maximum_reach_without_jib =
              this.maximum_reach_without_jib_TelescopicoAT;
            parameters.maximum_vertical_reach =
              this.maximum_vertical_reach_TelescopicoAT;
            parameters.maximum_horizontal_reach =
              this.maximum_horizontal_reach_TelescopicoAT;
          } else if (
            category.category_name ===
            'Guindaste móvel de lança telescópica articulada (Munck) sobre caminhão com carroceria'
          ) {
            machineItem.jib = this.jib_TelescopicoCaminhaoCarroceria;
            machineItem.max_weight =
              this.max_weight_TelescopicoCaminhaoCarroceria;
            machineItem.max_height =
              this.max_height_TelescopicoCaminhaoCarroceria;
            machineItem.max_radius =
              this.max_radius_TelescopicoCaminhaoCarroceria;

            parameters.maximum_nominal_lifting_capacity =
              this.maximum_nominal_lifting_capacity_TelescopicoCaminhaoCarroceria;
            parameters.maximum_nominal_vertical_reach_capacity =
              this.maximum_nominal_vertical_reach_capacity_TelescopicoCaminhaoCarroceria;
            parameters.maximum_reach_without_jib =
              this.maximum_reach_without_jib_TelescopicoCaminhaoCarroceria;
            parameters.maximum_vertical_reach =
              this.maximum_vertical_reach_TelescopicoCaminhaoCarroceria;
            parameters.maximum_horizontal_reach =
              this.maximum_horizontal_reach_TelescopicoCaminhaoCarroceria;
          } else if (
            category.category_name ===
            'Guindastes móvel de lança telescópica articulada(Munck) sobre cavalo trator'
          ) {
            machineItem.jib = this.jib_TelescopicoCavaloTrator;
            machineItem.max_weight = this.max_weight_TelescopicoCavaloTrator;
            machineItem.max_height = this.max_height_TelescopicoCavaloTrator;
            machineItem.max_radius = this.max_radius_TelescopicoCavaloTrator;

            parameters.maximum_nominal_lifting_capacity =
              this.maximum_nominal_lifting_capacity_TelescopicoCavaloTrator;
            parameters.maximum_nominal_vertical_reach_capacity =
              this.maximum_nominal_vertical_reach_capacity_TelescopicoCavaloTrator;
            parameters.maximum_reach_without_jib =
              this.maximum_reach_without_jib_TelescopicoCavaloTrator;
            parameters.maximum_vertical_reach =
              this.maximum_vertical_reach_TelescopicoCavaloTrator;
            parameters.maximum_horizontal_reach =
              this.maximum_horizontal_reach_TelescopicoCavaloTrator;
          } else if (
            category.category_name ===
            'Guindastes móvel de lança telescópica articulada(Munck) sobre cavalo trator e carreta'
          ) {
            machineItem.jib = this.jib_TelescopicoCavaloTratorCarreta;
            machineItem.max_weight =
              this.max_weight_TelescopicoCavaloTratorCarreta;
            machineItem.max_height =
              this.max_height_TelescopicoCavaloTratorCarreta;
            machineItem.max_radius =
              this.max_radius_TelescopicoCavaloTratorCarreta;

            parameters.maximum_nominal_lifting_capacity =
              this.maximum_nominal_lifting_capacity_TelescopicoCavaloTratorCarreta;
            parameters.maximum_nominal_vertical_reach_capacity =
              this.maximum_nominal_vertical_reach_capacity_TelescopicoCavaloTratorCarreta;
            parameters.maximum_reach_without_jib =
              this.maximum_reach_without_jib_TelescopicoCavaloTratorCarreta;
            parameters.maximum_vertical_reach =
              this.maximum_vertical_reach_TelescopicoCavaloTratorCarreta;
            parameters.maximum_horizontal_reach =
              this.maximum_horizontal_reach_TelescopicoCavaloTratorCarreta;
          }

          return machineItem;
        });
      });

      fetch(`https://fortis-api.55technology.com/v1/project/`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          token: localStorage.getItem('access_token'),
        },
        body: JSON.stringify({
          name: this.name,
          description: this.description,
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
