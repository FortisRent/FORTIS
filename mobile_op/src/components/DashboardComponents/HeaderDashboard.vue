<template>
  <q-layout>

    <q-header elevated class="header-dashboard">
      <router-link :to="`/dashboard/${company_uuid}`">
      <q-img
        src="../../assets/logo-fortis.svg"
        style="width: 150px; cursor: pointer;"
      />
    </router-link>
      
      <div class="btns-header-dashboard">
        <!-- <q-btn
        icon="add"
        v-if="!isMobile"
        label="Criar Projeto"
        text-color="black"
        style="margin-right: 1rem;"
        @click="dialog = true"
        />
        <q-btn
        icon="add"
        v-else
        text-color="black"
        style="margin-right: 1rem;"
        rounded
        unelevated
        round
        @click="dialog = true"
        /> -->
        
        <q-btn
        rounded
        unelevated
        round
        size="12px"
        icon="person"
        class="avatar-dashboard"
        text-color="white"
        />
      </div>
    </q-header>
    
    <q-dialog v-model="dialog">
      <q-card>
        <q-card-section style="max-height: 50vh" class="scroll">
          
          <q-page class="">
            
            <div class="flex  text-bold text-h5 text-primary text-left q-mb-sm q-ml-md q-pt-xl">
              Criar Projeto 
            </div>
            
            
            <q-separator class="bg-grey " />
            <q-input class="q-ma-md" outlined v-model="name" label="Nome do Projeto" />
            <div class="flex justify-center">
              <select  v-model="category_uuid" style="min-width: 400px;">
                <option v-for="machine in machine_list" 
                :key="machine.uuid" :value="machine.uuid">
                {{machine.category_name}}</option>
              </select>
            </div>
            
            <div >
              <q-input class="q-ma-md" outlined v-model="max_weight" label="Peso máximo" />
              
              <span class="text-primary q-ml-md text-bold">Descreva o Serviço*</span>
              <div class="q-pa-md" style="min-width: 400px">
                <q-input
                v-model="description"
                filled
                type="textarea"
                style="border: 1px solid secondary;"
                />
              </div>
              <div>
                <q-card class="my-card" >
                  <img src="../../assets/elevation.png">
                </q-card>
              </div>
              <div>
                <q-input class="q-mt-sm" outlined v-model="count" label="Quantidade"  />
                <q-input class="q-mt-sm" outlined v-model="weight" label="Peso (kg)"  />
                <q-input class="q-mt-sm" outlined v-model="width" label="Largura (m)"  />
                <q-input class="q-mt-sm" outlined v-model="length" label="Comprimento (m)"  />
                <q-input class="q-mt-sm" outlined v-model="height" label="Altura (m)"  />
                <q-input class="q-mt-sm" outlined v-model="lifting_height" label="E-Altura de Elevação (m)"  />
                <q-input class="q-mt-sm" outlined v-model="radius" label="R-raio"  />
                <q-input class="q-mt-sm" outlined v-model="clearance" label="A-Afastamento do Guindaste (m)"  />
                <q-input class="q-mt-sm" outlined v-model="indentation" label="D-Recuo (m)"  />
                <q-input class="q-mt-sm" outlined v-model="obstacle_height" label="B-Altura do Obstáculo (m)"  />
              </div>
              <div class="q-pa-md">
                <p class="text-primary text-bold text-h6 q-mt-md">Data de previsão</p>
                <div class="flex justify-center">
                  <q-date class="q-ml-lg "
                  v-model="expected_date"
                  color="secondary"
                  default-year-month="2025/08"
                  />
                </div>
              </div>
              <div class="q-pa-md">
                <p class="text-primary text-bold text-h6">Localização do serviço</p>
                <q-input class="q-mt-sm" outlined v-model="zip_code" label="CEP" @blur="get_address_by_cep" />
                <q-input class="q-mt-sm" outlined v-model="street" label="Rua" />
                <q-input class="q-mt-sm" outlined v-model="number_street" label="Número" />
                <q-input class="q-mt-sm" outlined v-model="complement" label="Complemento" />
                <q-input class="q-mt-sm" outlined v-model="neighborhood" label="Bairro" />
                <q-input class="q-mt-sm" outlined v-model="city_id" label="Cidade" />
                <q-input class="q-mt-sm" outlined v-model="state_id" label="UF" />
                <spam class="text-primary" >Todos os campos sao obrigatorios*</spam>
                <div class="flex justify-center q-mt-sm">
                  <q-btn label="Criar Projeto" color="secondary" class="text-primary" @click="on_submit"/>
                </div>
              </div>
            </div>
          </q-page>
          
        </q-card-section>
        
        <q-separator />
        
        <q-card-actions align="right">
          <q-btn flat label="Cancelar" color="primary" v-close-popup />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <q-page-container>
			<keep-alive>
				<router-view />
			</keep-alive>
		</q-page-container>

  </q-layout>
  </template>
  
<script setup>
import { ref, onMounted } from "vue";

// Responsividade
const isMobile = ref(false);
const dialog = ref(false);
const company_uuid = ref('')

onMounted(() => {
  company_uuid.value = localStorage.getItem('company_uuid') || '';
  const updateMobileStatus = () => {
    isMobile.value = window.innerWidth <= 500;
  };
  updateMobileStatus();
  window.addEventListener("resize", updateMobileStatus);
});
  
// Variáveis do formulário
const name = ref(null);
const description = ref(null);
const expected_date = ref(null);
const category_uuid = ref(null);
const machine_list = ref([]);
const max_weight = ref(null);
const zip_code = ref(null);
const street = ref(null);
const number_street = ref(null);
const complement = ref(null);
const neighborhood = ref(null);
const city_id = ref(null);
const state_id = ref(null);
const count = ref(null);
const weight = ref(null);
const length = ref(null);
const width = ref(null);
const height = ref(null);
const lifting_height = ref(null);
const obstacle_height = ref(null);
const indentation = ref(null);
const clearance = ref(null);
const radius = ref(null);

// Função para buscar categorias de máquinas
// const get_all_machine_categories = () => {
//   fetch(`http://localhost:5510/v1/machine/category/`, {
//     method: "GET",
//     headers: {
//       token: localStorage.getItem("access_token"),
//     },
//   })
//     .then((response) => {
//       if (!response.ok) throw new Error("Erro ao buscar dados.");
//       return response.json();
//     })
//     .then((data) => {
//       machine_list.value = data.machine_categories.map(category => ({
//         category_name: category.category_name,
//         uuid: category.uuid
//       }));
//     })
//     .catch((error) => {
//       console.error("Erro ao carregar categorias:", error);
//     });
// };

// // Chamar a função ao montar o componente
// onMounted(() => {
//   get_all_machine_categories();
// });

// Função para enviar o formulário
const on_submit = () => {
  fetch(`http://localhost:5510/v1/project/lifting/`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'token': localStorage.getItem('access_token'),
    },
    body: JSON.stringify({
      name: name.value,
      machine_category_uuid: category_uuid.value,
      description: description.value,
      expected_date: expected_date.value,
      max_weight: max_weight.value,
      zip_code: zip_code.value,
      street: street.value,
      number_street: number_street.value,
      complement: complement.value,
      neighborhood: neighborhood.value,
      city_id: city_id.value,
      state_id: state_id.value,
      count: count.value,
      weight: weight.value,
      length: length.value,
      width: width.value,
      height: height.value,
      lifting_height: lifting_height.value,
      obstacle_height: obstacle_height.value,
      indentation: indentation.value,
      clearance: clearance.value,
      radius: radius.value,
    }),
  })
    .then((response) => {
      if (!response.ok) throw new Error('Erro ao cadastrar novos dados.');

      alert('Dados cadastrados com sucesso.');
      window.location.href = `/dashboard`;
    })
    .catch((error) => {
      alert(error.message);
    });
};

// Função para buscar endereço pelo CEP
const get_address_by_cep = async () => {
  if (!zip_code.value || zip_code.value.length !== 8) {
    alert('Por favor, insira um CEP válido com 8 dígitos.');
    return;
  }

  try {
    const response = await fetch(`https://viacep.com.br/ws/${zip_code.value}/json/`);
    if (!response.ok) throw new Error('Erro ao buscar CEP');

    const data = await response.json();
    if (data.erro) {
      alert('CEP não encontrado.');
      return;
    }

    street.value = data.logradouro || '';
    neighborhood.value = data.bairro || '';
    city_id.value = data.localidade || '';
    state_id.value = data.uf || '';
    number_street.value = data.complemento || '';

  } catch (error) {
    console.error('Erro ao buscar endereço:', error);
    alert('Erro ao buscar o endereço. Tente novamente.');
  }
};
</script>


<style scoped lang="scss">
  .header-dashboard {
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 80px;
    padding: 0 50px;
    background-color: #fff;
    color: #000;

    .btns-header-dashboard {
      .avatar-dashboard {
        background-color: #303940;
      }
    }
  }
</style>
