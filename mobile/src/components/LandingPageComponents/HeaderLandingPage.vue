<template>
  <q-header elevated class="header-pg">
    <a href="/">
      <q-img
        src="../../assets/logo-fortis-lp.svg"
        style="width: 150px;"
      />
    </a>

    <!-- Menu para telas maiores -->
    <div v-if="$q.screen.gt.sm" class="menu-desktop-pg">
      <q-btn flat href="#services" label="Serviços" class="menu-item-pg" />
      <q-btn flat href="#advantages" label="Vantagens" class="menu-item-pg" />
      <q-btn flat href="#offer-equipment" label="Ofereça Equipamentos" class="menu-item-pg" />
      <q-btn flat href="#contact" label="Contato" class="menu-item-pg" />
      <div class="auth-buttons">
        <q-btn flat to="/login" class="menu-item-pg" >
          <q-icon
            name="person"
          />
          Entre
        </q-btn>
        <span style="padding: 0 15px;">OU</span>
        <q-btn flat to="/register" label="Cadastre-se" class="menu-item-pg" />
      </div>
    </div>

    <!-- Menu hambúrguer para telas menores -->
    <div v-else>
      <q-toolbar>
        <q-btn
          flat
          dense
          round
          icon="menu"
          aria-label="Menu"
          @click="toggleLeftDrawer"
        />
      </q-toolbar>
      <q-drawer
        style="background-color: #303940;"
        v-model="leftDrawerOpen"
        :width="250"
        :breakpoint="550"
      >
        <q-scroll-area style="height: calc(100% - 150px);">
          <q-list padding>
            <q-item to="#services" exact clickable v-ripple>
              <q-item-section class="menu-item-pg">Serviços</q-item-section>
            </q-item>
            <q-item to="#advantages" exact clickable v-ripple>
              <q-item-section class="menu-item-pg">Vantagens</q-item-section>
            </q-item>
            <q-item to="#offer-equipment" exact clickable v-ripple>
              <q-item-section class="menu-item-pg">Ofereça Equipamentos</q-item-section>
            </q-item>
            <q-item to="#contact" exact clickable v-ripple>
              <q-item-section class="menu-item-pg">Contato</q-item-section>
            </q-item>
            <q-item to="/login" exact clickable v-ripple>
              <q-item-section class="menu-item-pg">Entre</q-item-section>
            </q-item>
            <q-item to="/register" exact clickable v-ripple>
              <q-item-section class="menu-item-pg">Cadastre-se</q-item-section>
            </q-item>
          </q-list>
        </q-scroll-area>
      </q-drawer>
    </div>
  </q-header>
</template>

<script>
import { ref } from 'vue';

export default {
  setup() {
    const leftDrawerOpen = ref(false);

    const toggleLeftDrawer = () => {
      leftDrawerOpen.value = !leftDrawerOpen.value;
    };

    return {
      leftDrawerOpen,
      toggleLeftDrawer,
    };
  },
};
</script>

<style scoped>
.header-pg {
  display: flex;
  align-items: center;
  justify-content: space-between;

  height: 100px;
  padding: 0 50px;

  background-color: #303940;
}

.auth-buttons-pg{
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
}

.menu-desktop-pg {
  display: flex;
  gap: 20px;
}

.menu-item-pg {
  color: #fff;
  font-weight: bold;
	transition: 0.3s ease-in-out;
}
</style>