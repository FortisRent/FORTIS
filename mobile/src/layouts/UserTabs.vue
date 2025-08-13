<template>
  <q-layout view="hHh lpR fFf">

    <!-- <q-header elevated class="bg-primary text-white" height-hint="98">
      <q-toolbar>
        <q-btn dense flat round icon="menu" @click="toggleLeftDrawer" />

        <q-toolbar-title>
          <q-avatar>
            <img src="https://cdn.quasar.dev/logo-v2/svg/logo-mono-white.svg">
          </q-avatar>
          Logo do MeetMed Aqui
        </q-toolbar-title>
      </q-toolbar>
    </q-header>

    <q-drawer show-if-above v-model="leftDrawerOpen" side="left" bordered>
      <p>Add conteudo do menu aqui</p>
    </q-drawer> -->

    <q-page-container>
        <keep-alive>
            <router-view />
        </keep-alive>
    </q-page-container>

    <q-footer elevated style="max-width: 100%;">
        <q-tabs class="bg-white" style="width: 100%;">
            <q-route-tab to="/user/service" style="color: black;">
              <q-icon name="home" size="30px"/>
              <span style="font-size: small; font-weight: 500;" >Inicio</span>
            </q-route-tab>
            
            <q-route-tab to="/user/timeline/list" style="color: black;">
              <q-icon name="search" size="30px"/>
              <span style="font-size: small; font-weight: 500;" >Projetos</span>
            </q-route-tab>

            <q-route-tab to="/user/chats" style="color: black;">
              <q-icon name="chat" size="30px"/>
              <span style="font-size: small; font-weight: 500;" >Chats</span>
            </q-route-tab>

            <q-route-tab to="/user/manage" style="color: black;">
                <q-icon name="person" size="30px" />
                <span style="font-size: small; font-weight: 500;">Conta</span>
            </q-route-tab>
        </q-tabs>
    </q-footer>

  </q-layout>
</template>

<script>
import { ref } from 'vue'

export default {
  setup () {
    const leftDrawerOpen = ref(false)

    return {
      leftDrawerOpen,
      toggleLeftDrawer () {
        leftDrawerOpen.value = !leftDrawerOpen.value
      }
    }
  }
}
</script>


<style>
  .q-tabs__content {
    display: flex;
    justify-content: space-between;
  }
</style>