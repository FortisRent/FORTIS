<template>
    <q-page>
      <div>
        <div class="row q-gutter-md q-pa-md">
          <q-btn :to="`/dashboard/finance/taxas/${companyUuid}`" icon="receipt" label="add taxas" text-color="black" />
          <q-btn icon="attach_money" label="financeiro" text-color="black" />
          <q-btn icon="assessment" />
          <q-btn icon="settings" />
        </div>
      </div>
    </q-page>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue'
  
  const companyUuid = ref('')
  
  // pega o uuid do localStorage quando o componente for carregado
  onMounted(() => {
    companyUuid.value = localStorage.getItem('company_uuid') || ''
  })
  </script>
  