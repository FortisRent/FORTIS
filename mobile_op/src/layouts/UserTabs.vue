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

    <q-footer elevated>
        <q-tabs class="bg-white">
            <q-route-tab to="/user/service" style="color: black;">
              <q-icon name="home_work" size="30px"/>
              <span style="font-size: small;" >Inicio</span>
            </q-route-tab>
            
            <q-route-tab to="/user/timeline/list" style="color: black;">
              <q-icon name="front_loader" size="30px"/>
              <span style="font-size: small;" >Em Andamento</span>
            </q-route-tab>

            <q-route-tab to="/user/manage" style="color: black;">

                <q-icon name="menu" size="30px" />
                <span style="font-size: small;">perfil</span>
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
