<template>
  <q-layout class="bg-white">
    <div class="q-pa-md row">
      <!-- Coluna lateral de projetos -->
      <div class="col-6 flex column items-center" style="min-height: 96vh">
        <p class="text-h3 text-primary text-bold">Lista de projetos</p>

        <div class="q-pa-md full-width">
          <div
            v-for="project in projects"
            :key="project.budget_uuid"
            class="q-pa-sm q-mb-sm flex items-center cursor-pointer"
            style="border: 1px solid #ccc; border-radius: 12px"
            @click="selectProject(project)"
          >
            <q-icon name="folder" class="q-mr-sm" />
            <div class="column">
              <span class="text-subtitle1">{{ project.project_name }}</span>
              <span class="text-caption text-grey">{{ project.status_name }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Coluna de detalhes do projeto -->
      <div class="col-6 flex column justify-start items-center bg-grey-4"
        style="border-radius: 10px; min-height: 96vh; padding-top: 20px">
        <div v-if="selectedProject" class="full-width flex column items-center">
          <div class="flex justify-center q-mb-md">
            <q-icon name="folder_open" color="black" size="64px" />
          </div>
          <div class="text-center q-mb-md">
            <p class="text-bold text-primary text-h4">{{ selectedProject.project_name }}</p>
            <p class="text-primary q-mt-sm">Status: {{ selectedProject.status_name }}</p>
            <p class="text-primary">Cliente: {{ selectedProject.client_name }}</p>
            <p class="text-primary">Empresa: {{ selectedProject.company_name }}</p>
            <p class="text-primary">Identificador: {{ selectedProject.identifier }}</p>
            <p class="text-primary">Máquina: {{ selectedProject.machine_name }}</p>
            <p class="text-primary">Descrição: {{ selectedProject.project_description }}</p>
            <p class="text-primary">Criado em: {{ selectedProject.created_at }}</p>
            <p class="text-primary">Previsão: {{ selectedProject.expected_date || 'Não definida' }}</p>
          </div>

          <!-- Botão Financeiro -->
          <q-btn
            v-if="selectedProject"
            :to="`/dashboard/finance/${companyUuid}/${selectedProject.project_uuid}`"
            icon="attach_money"
            label="Financeiro"
            color="primary"
            class="q-mt-md"
          />
        </div>

        <!-- Mensagem inicial -->
        <div v-else>
          <div class="text-center">
            <p class="text-bold text-white text-h4">Selecione um projeto</p>
          </div>
        </div>
      </div>
    </div>
  </q-layout>
</template>

<script>
export default {
  name: "ListaProjetos",
  data() {
    return {
      companyUuid: this.$route.params.company_uuid,
      projects: [],
      selectedProject: null,
      isLoading: false,
    };
  },
  methods: {
    selectProject(project) {
      this.selectedProject = project;
    },

    async get_budget_by_company_uuid() {
      this.isLoading = true;
      try {
        const response = await fetch(
          `http://localhost:5510/v1/budget/company/${this.companyUuid}`,
          {
            headers: { token: localStorage.getItem("access_token") },
          }
        );

        if (!response.ok) throw new Error("Network response was not ok");

        const data = await response.json();
        console.log("Dados recebidos:", data);

        // Filtra só os ACEITO
        this.projects = data.company_budget;
      // .filter(
      //     (item) => item.status_name === "ACEITO"
      //   );
      } catch (error) {
        console.error("Erro ao buscar dados:", error);
        this.$q.notify({
          color: "red-5",
          textColor: "white",
          icon: "error",
          message: "Erro ao buscar orçamentos.",
        });
      } finally {
        this.isLoading = false;
      }
    },
  },
  mounted() {
    this.get_budget_by_company_uuid();
  },
};
</script>
