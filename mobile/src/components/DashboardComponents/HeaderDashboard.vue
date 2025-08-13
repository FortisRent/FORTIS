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
        <q-btn
        icon="add"
        v-if="!isMobile"
        label="Criar Projeto"
        text-color="black"
        style="margin-right: 1rem;"
        :to="`/dashboard/create/project/${company_uuid}`"

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
        />

        <q-btn
        rounded
        unelevated
        round
        size="12px"
        icon="power_settings_new"
        class="avatar-dashboard"
        text-color="white"
        to="/logout"
        />
      </div>
    </q-header>

    <q-page-container>
			<keep-alive>
				<router-view />
			</keep-alive>
		</q-page-container>

  </q-layout>
  </template>

<script setup>
import { ref, onMounted } from 'vue';

// Responsividade
const isMobile = ref(false);
const company_uuid = ref('')

onMounted(() => {
  company_uuid.value = localStorage.getItem('company_uuid') || '';
  const updateMobileStatus = () => {
    isMobile.value = window.innerWidth <= 500;
  };
  updateMobileStatus();
  window.addEventListener('resize', updateMobileStatus);
});

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
