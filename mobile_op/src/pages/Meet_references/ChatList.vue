<template>
  <q-page class="bg-white">
    <q-pull-to-refresh @refresh="refreshData" color="primary" bg-color="white" icon="autorenew" >
        <div class="flex justify-between" >
          <h4 class="q-mb-sm q-ml-md text-primary  text-bold q-pt-md text-h5">Conversas</h4>
        </div>

        <q-separator class="bg-info" />

        <!-- Barra de pesquisa -->
        <div class="bg-white">
          <q-input
            outlined
            placeholder="Pesquisar"
            v-model="search"
            class="q-pa-md"
            bg-color="white"
            rounded
          >
            <template v-slot:prepend>
              <q-icon name="search" />
            </template>
          </q-input>

          <!-- Lista de conversas -->
          <q-list style="min-width: 350px;">
            <q-item
              v-for="(chat, index) in filteredChats"
              :key="index"
              clickable
              to="/help"
              class="line-item"
            >
              <q-item-section avatar>
                <q-avatar color="primary" text-color="white">{{ chat.initials }}</q-avatar>
              </q-item-section>
              <q-item-section class="text-primary text-bold">
                <q-item-label class="text-truncate">{{ chat.name }}</q-item-label>
                <q-item-label caption class="text-truncate">{{ chat.message }}</q-item-label>
              </q-item-section>
              <q-item-section side>
                <q-item-label caption>{{ chat.time }}</q-item-label>
              </q-item-section>
              <q-separator />
            </q-item>
          </q-list>
        </div>
    </q-pull-to-refresh>
  </q-page>
</template>

<script>
export default {
  data() {
    return {
      search: "",
      chats: [
        { initials: "SP", name: "Suporte", message: "Atendimento finalizado.", time: "15:35", avatar: "https://cdn.quasar.dev/img/avatar4.jpg" },
        { initials: "MC", name: "Maria Celina", message: "Boa tarde, tudo bem?", time: "08:32", avatar: "https://cdn.quasar.dev/img/avatar6.jpg" },
      ],
    };
  },
  computed: {
    filteredChats() {
      return this.chats.filter((chat) =>
        chat.name.toLowerCase().includes(this.search.toLowerCase())
      );
    },
  },
  methods: {
    async refreshData(done) {
      // dados ficticios quando for atualizar a pagina 
      setTimeout(() => {
        this.chats.push({
          initials: "JS",
          name: "João Silva",
          message: "Novo chat recebido.",
          time: "Agora",
        });
        done();
      }, 1500);
    },
  },
};
</script>

<style scoped>
.line-item {
  border-bottom: 0.2px solid lightgray;
}
.text-truncate {
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		display: block;
		max-width: 90%;
	}
</style>
