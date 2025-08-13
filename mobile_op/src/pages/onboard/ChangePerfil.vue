<template>
    <q-page class="flex flex-center">
      <div class="q-pa-md">
        <div class="text-h5 text-primary text-center q-mb-md">O que você vai fazer?</div>
        <q-list class="column q-gutter-md">
          <q-btn
            v-for="item in items"
            :key="item.label"
            :label="item.label"
            color="secondary"
            unelevated
            class="full-width q-pa-md text-h6 rounded-borders"
            @click="handleClick(item.value)"
          />
        </q-list>
      </div>
    </q-page>
  </template>
  
  <script setup>
  import { useRouter } from 'vue-router';
  
  const router = useRouter();
  
  const items = [
    { label: 'Locador', value: 'locador' },
    { label: 'Locatário', value: 'locatario' },
    { label: 'Operador', value: 'operador' }
  ];
  
  const handleClick = (value) => {
    console.log('Selecionado:', value);
  };
  </script>
  
  <style scoped>
  .rounded-borders {
    border-radius: 16px;
  }
  </style>
  