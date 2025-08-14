<template>
  <q-page class="bg-white">
    <div class="row items-center q-pa-md q-mt-lg">
      <div class="text-h5 text-primary text-bold q-ml-md">Cadastrar Taxas</div>
    </div>

    <q-separator class="bg-grey" />

    <div class="q-pa-md">
      <div class="row q-col-gutter-md">

        <!-- Formulário -->
        <div class="col-12 col-md-6">
          <q-form @submit="on_submit" @reset="on_reset" class="q-gutter-md">
            <q-input
              rounded
              color="secondary"
              v-model="name"
              label="Tipo de taxa"
              lazy-rules
              :rules="[val => val && val.length > 0 || 'Por favor, insira o nome da taxa.']"
              no-error-icon
            />

            <q-input
              rounded
              color="secondary"
              v-model="amount"
              label="Valor da taxa"
              mask="R$ ###.###,##"
              reverse-fill-mask
              unmasked-value
              lazy-rules
              :rules="[val => val && val.length > 0 || 'Por favor, insira o valor.']"
              no-error-icon
            />

            <div class="row q-gutter-sm">
              <q-btn label="Limpar" type="reset" color="secondary" flat />
              <q-btn label="Cadastrar" @click="insert_tax" color="secondary" />
            </div>
          </q-form>
        </div>

        <!-- Lista -->
        <div class="col-12 col-md-6">
          <div class="bg-grey-4 q-pa-md" style="border-radius: 12px; min-height: 70vh; overflow-y: auto;">
            <p class="text-h5 text-primary text-bold q-mb-md text-center">Lista Taxas cadastradas</p>

            <div
              v-for="(tax, index) in taxes"
              :key="index"
              class="q-pa-sm q-mb-sm"
              style="border: 1px solid #ccc; border-radius: 8px;"
            >
              <div class="row items-center justify-between">
                <div>
                  <div v-if="!tax.editing">
                    <p class="text-subtitle2 text-primary q-mb-xs">{{ tax.name }}</p>
                    <p class="text-caption text-grey-7">Valor: R$ {{ tax.amount }}</p>
                  </div>

                  <div v-else>
                    <q-input
                      v-model="tax.edit_name"
                      label="Tipo de taxa"
                      dense
                      outlined
                      color="primary"
                      class="q-mb-xs"
                    />
                    <q-input
                      v-model="tax.edit_amount"
                      label="Valor da taxa"
                      dense
                      outlined
                      color="primary"
                      class="q-mb-xs"
                    />
                  </div>
                </div>

                <div>
                  <q-btn
                    flat
                    dense
                    icon="edit"
                    color="primary"
                    v-if="!tax.editing"
                    @click="tax.editing = true; tax.edit_name = tax.name; tax.edit_amount = tax.amount"
                  />
                  <q-btn
                    flat
                    dense
                    icon="check"
                    color="green"
                    v-if="tax.editing"
                    @click="update_tax(tax)"
                  />
                  <q-btn
                    flat
                    dense
                    icon="close"
                    color="red"
                    v-if="tax.editing"
                    @click="tax.editing = false"
                  />
                  <q-btn
                    flat
                    dense
                    icon="delete"
                    color="negative"
                    @click="delete_tax(tax)"
                  />
                </div>
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>
  </q-page>
</template>

<script>
export default {
  data() {
    return {
      name: '',
      amount: '',
      taxes: [],
    };
  },
  mounted() {
    this.check_login_status();
    this.get_list_tax();
  },
  methods: {
    check_login_status() {
      if (!localStorage.getItem('access_token')) {
        alert('Not logged in');
        this.$router.push('/login');
      }
    },

    async insert_tax() {
      fetch(
        `https://fortis-api.55technology.com/v1/service/charge/`,
        {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            token: localStorage.getItem('access_token'),
          },
          body: JSON.stringify({
            name: this.name,
            amount: this.amount,
            company_uuid: this.$route.params.company_uuid,
          }),
        }
      )
        .then((response) => {
          if (!response.ok) {
            throw new Error('Por favor, preencha todos os campos.');
          }

          this.$q.notify({
            color: 'green-4',
            textColor: 'white',
            icon: 'cloud_done',
            message: 'Nova taxa cadastrada.',
          });

          this.name = '';
          this.amount = '';
          this.get_list_tax();
        })
        .catch((error) => {
          this.$q.notify({
            color: 'red-4',
            textColor: 'white',
            icon: 'error',
            message: error.message,
          });
        });
    },

    async get_list_tax() {
      fetch(
        `https://fortis-api.55technology.com/v1/service/charge/company/${this.$route.params.company_uuid}`,
        {
          method: 'GET',
          headers: { token: localStorage.getItem('access_token') },
        }
      )
        .then((response) => {
          if (!response.ok) {
            throw new Error('Erro ao buscar taxas.');
          }
          return response.json();
        })
        .then((data) => {
          this.taxes = data.service_charge_company.map((item) => ({
            uuid: item.service_charge_uuid,
            name: item.service_charge_name,
            amount: item.fee_amount,
            editing: false,
            edit_name: '',
            edit_amount: '',
          }));
        })
        .catch((error) => {
          console.error('Erro ao buscar taxas:', error);
        });
    },

    async update_tax(tax) {
      try {
        const response = await fetch(
          `https://fortis-api.55technology.com/v1/service/charge/${tax.uuid}`,
          {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
              token: localStorage.getItem('access_token'),
            },
            body: JSON.stringify({
              name: tax.edit_name,
              amount: tax.edit_amount,
            }),
          }
        );

        if (!response.ok) {
          throw new Error('Erro ao atualizar taxa.');
        }

        this.$q.notify({
          color: 'green-4',
          textColor: 'white',
          icon: 'cloud_done',
          message: 'Taxa atualizada com sucesso.',
        });

        tax.name = tax.edit_name;
        tax.amount = tax.edit_amount;
        tax.editing = false;
      } catch (error) {
        console.error('Erro ao atualizar taxa:', error);
        this.$q.notify({
          color: 'red-4',
          textColor: 'white',
          icon: 'error',
          message: error.message,
        });
      }
    },

    async delete_tax(tax) {
      try {
        const confirm = await this.$q.dialog({
          title: 'Confirmar exclusão',
          message: `Deseja realmente excluir a taxa "${tax.name}"?`,
          cancel: true,
          persistent: true
        }).onOk(async () => {
          const response = await fetch(
            `https://fortis-api.55technology.com/v1/service/charge/${tax.uuid}`,
            {
              method: 'DELETE',
              headers: {
                'Content-Type': 'application/json',
                token: localStorage.getItem('access_token'),
              },
            }
          );

          if (!response.ok) {
            throw new Error('Erro ao excluir taxa.');
          }

          this.$q.notify({
            color: 'green-4',
            textColor: 'white',
            icon: 'delete',
            message: 'Taxa excluída com sucesso.',
          });

          this.get_list_tax();
        });

      } catch (error) {
        console.error('Erro ao excluir taxa:', error);
        this.$q.notify({
          color: 'red-4',
          textColor: 'white',
          icon: 'error',
          message: error.message,
        });
      }
    },
  },
};
</script>

<style scoped>
</style>
