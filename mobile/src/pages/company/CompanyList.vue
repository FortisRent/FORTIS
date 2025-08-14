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
      <div class="text-h6 text-primary text-bold q-ml-md">Minhas Empresas</div>
    </div>

    <q-separator class="bg-grey" />

    <div class="q-pa-md">
      <template v-if="loading">
        <q-card v-for="n in 3" :key="n" class="card-style q-pa-md q-mb-md">
          <div class="row items-center no-wrap">
            <q-icon
              name="front_loader"
              size="22px"
              color="secondary"
              class="q-mr-sm"
            />
            <q-skeleton type="text" width="80%" height="2rem" />
          </div>
        </q-card>
      </template>

      <template v-else>
        <q-card
          v-for="company in company_list"
          :key="company.id"
          class="card-style q-pa-sm q-mb-md"
        >
          <q-btn
            no-caps
            flat
            :to="`/user/company/options/${company.uuid}`"
            class="full-width"
          >
            <div class="col items-left">
              <div class="row items-center no-wrap">
                <q-icon
                  name="front_loader"
                  size="22px"
                  color="secondary"
                  class="q-mr-sm"
                />
                <p
                  class="text-bold text-primary text-subtitle1 no-margin text-truncate"
                >
                  {{ company.name }}
                </p>
              </div>

              <div class="text-left q-pa-sm text-caption">
                <p class="text-primary no-margin">CNPJ: {{ company.cnpj }}</p>
              </div>
            </div>
          </q-btn>
        </q-card>
        <q-card class="card-style q-pa-sm q-mb-md">
          <q-btn
            no-caps
            flat
            to="/user/company/insert"
            class="full-width no-padding"
          >
            <div class="row justify-center items-center full-width">
              <q-icon name="add_circle" size="25px" color="secondary" />
              <p class="text-subtitle2 text-primary no-margin q-ml-sm q-pl-sm">
                Cadastrar Empresa
              </p>
            </div>
          </q-btn>
        </q-card>
      </template>
    </div>
  </q-page>
</template>

<script>
export default {
  name: 'CompanyList',
  data() {
    return {
      loading: true,
      company_list: [],
    };
  },
  mounted() {
    this.get_company_list();
  },
  methods: {
    to_edit() {
      this.$router.push('/user/manage/edit/');
    },
    async get_company_list() {
      fetch('https://fortis-api.55technology.com/v1/company/logged/', {
        headers: { token: localStorage.getItem('access_token') },
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          return response.json();
        })
        .then((data) => {
          console.table(data.company);
          this.company_list = data.company;
          this.loading = false;
        })
        .catch((error) => {
          console.error('Error fetching data:', error);
          this.$q.notify({
            color: 'red-5',
            textColor: 'white',
            icon: 'cloud_done',
            message: 'Nenhuma empresa cadastrada.',
          });
          this.loading = false;
        });
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
  max-width: 80%;
}

.card-style {
  border-radius: 10px;
  min-width: 350px;
}
</style>
