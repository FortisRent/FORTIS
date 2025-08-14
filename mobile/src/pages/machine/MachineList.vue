<template>
  <q-page class="bg-white">
    <div class="row justify-around items-center q-pa-md q-mt-lg">
      <q-btn
        flat
        round
        icon="chevron_left"
        class="text-primary"
        color="secondary"
        size="18px"
        style="position: absolute; left: 10px"
        @click="$router.go(-1)"
      />
      <div class="text-h6 text-primary text-bold q-ml-md">Equipamentos</div>
    </div>

    <q-separator class="bg-grey" />

    <div v-if="loading" class="column items-center q-pa-md q-gutter-md">
      <q-card
        v-for="n in 3"
        :key="n"
        class="card-style q-pa-md"
        style="width: 100%; max-width: 600px; height: 120px"
      >
        <div class="row items-start">
          <q-skeleton type="QAvatar" size="50px" />
          <div class="column q-ml-md" style="flex: 1">
            <q-skeleton type="text" width="70%" height="1rem" />
            <q-skeleton type="text" width="50%" height="1rem" class="q-mt-xs" />
            <q-skeleton type="text" width="40%" height="1rem" class="q-mt-xs" />
          </div>
        </div>
      </q-card>
    </div>

    <div v-else class="q-pa-md">
      <q-card
        v-for="machine in machine_list"
        :key="machine.id"
        class="card-style q-pa-sm q-mb-md"
      >
        <div class="row justify-between items-start">
          <q-img
            class="q-ma-sm"
            src="../../assets/escavadeirawhats.jpeg"
            width="50px"
            style="min-width: 50px; border-radius: 8px"
          />
          <div class="column q-ml-md" style="flex: 1">
            <p
              class="text-subtitle1 text-bold text-primary no-margin"
              style="line-height: 1.2"
            >
              {{ machine.category_name }}
            </p>
            <div class="q-mt-xs">
              <p class="text-body2 text-grey-8 no-margin">
                <strong>Nome:</strong> {{ machine.name }}
              </p>
              <p class="text-body2 text-grey-8 no-margin">
                <strong>Tipo:</strong> {{ machine.price_type }}
              </p>
              <p class="text-body2 text-grey-8 no-margin">
                <strong>Placa:</strong> {{ machine.license_plate }}
              </p>
              <p v-if="machine.cnpj" class="text-body2 text-grey-8 no-margin">
                <strong>CNPJ:</strong> {{ machine.cnpj }}
              </p>
            </div>
          </div>

          <div class="column items-end q-gutter-y-xs q-mr-sm">
            <q-btn
              icon="edit"
              clickable
              size="sm"
              flat
              color="primary"
              :to="`/user/machine/edit/${machine.uuid}`"
            />
            <q-btn
              icon="delete"
              clickable
              size="sm"
              flat
              color="negative"
              @click="confirm_delete(machine.uuid)"
            />
          </div>
        </div>
      </q-card>
      <q-card class="card-style q-pa-sm q-mb-md">
        <div class="row justify-center items-center q-gutter-sm">
          <q-btn
            icon="add_circle"
            clickable
            rounded
            flat
            size="md"
            color="secondary"
            :to="`/user/machine/insert/${this.$route.params.company_uuid}`"
          />
          <p class="text-subtitle2 text-primary no-margin">Cadastrar Máquina</p>
        </div>
      </q-card>
    </div>

    <q-dialog v-model="delete_dialog">
      <q-card>
        <q-card-section class="row items-center">
          <div style="margin-left: 120px">
            <q-icon name="warning" color="red" size="40px" />
          </div>
          <span class="text-h6 text-center text-primary"
            >Deseja realmente deletar esta máquina?</span
          >
        </q-card-section>
        <q-card-actions align="right">
          <q-btn label="Cancelar" color="primary" v-close-popup />
          <q-btn label="Deletar" color="red" @click="delete_machine" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
export default {
  name: 'MachineList',
  data() {
    return {
      loading: true,
      machine_list: [],
      delete_dialog: false,
      machine_to_delete: null,
    };
  },
  mounted() {
    this.get_machine_list();
  },
  methods: {
    async get_machine_list() {
      fetch(
        `https://fortis-api.55technology.com/v1/machine/company/${this.$route.params.company_uuid}`,
        {
          headers: { token: localStorage.getItem('access_token') },
        }
      )
        .then((response) => {
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          return response.json();
        })
        .then((data) => {
          console.table(data.machine);
          this.machine_list = data.machine;
          this.loading = false;
        })
        .catch((error) => {
          console.error('Error fetching data:', error);
          this.$q.notify({
            color: 'red-5',
            textColor: 'white',
            icon: 'cloud_done',
            message: 'Nenhuma Máquina cadastrada.',
          });
          this.loading = false;
        });
    },
    confirm_delete(machine_uuid) {
      this.machine_to_delete = machine_uuid;
      this.delete_dialog = true;
    },
    async delete_machine() {
      try {
        const response = await fetch(
          `https://fortis-api.55technology.com/v1/machine/${this.machine_to_delete}`,
          {
            method: 'DELETE',
            headers: {
              token: localStorage.getItem('access_token'),
            },
          }
        );
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        this.$q.notify({
          color: 'green-5',
          textColor: 'white',
          icon: 'check',
          message: 'Máquina deletada com sucesso.',
        });
        this.machine_list = this.machine_list.filter(
          (machine) => machine.uuid !== this.machine_to_delete
        );
      } catch (error) {
        console.error('Error Delete Machine:', error);
        this.$q.notify({
          color: 'red-5',
          textColor: 'white',
          icon: 'error',
          message: 'Erro ao deletar a Máquina.',
        });
      } finally {
        this.delete_dialog = false;
        this.machine_to_delete = null;
      }
    },
  },
};
</script>

<style scoped>
.text-truncate {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  display: block;
  max-width: 100%;
}
.card-style {
  border-radius: 10px;
  max-width: 350px;
}

a,
button {
  padding: 0;
  margin: 0;
}
</style>
