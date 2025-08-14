<template>
  <q-pull-to-refresh @refresh="refresh">
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
        <div class="text-h6 text-primary text-bold q-ml-md">
          Informações Pessoais
        </div>
      </div>

      <q-separator class="bg-grey" />

      <div class="q-pa-md">
        <q-card class="card-style q-pa-sm q-mb-md">
          <div class="row justify-end">
            <q-btn
							v-if="loaded"
              icon="photo_camera"
              class="q-mt-sm"
              color="black"
              flat
              size="sm"
              @click="$refs.uploader.pickFiles()"
            />

            <q-uploader
              ref="uploader"
              :url="`https://fortis-api.55technology.com/v1/user/profile/upload/`"
              field-name="user_image"
              label="Add img"
              accept=".jpg,.jpeg,.png"
              :headers="headers"
              auto-upload
              @rejected="on_rejected"
              @uploaded="on_upload_profile"
              style="display: none"
              text-color="black"
            />
          </div>
          <div class="text-center text-caption">
            <q-avatar size="90px" class="q-mb-lg">
              <q-img
                v-if="profile_picture_url && loaded"
                :src="`https://fortis-api.55technology.com/${profile_picture_url}`"
                width="90px"
                height="90px"
                fit="cover"
                style="border-radius: 100px"
              />
							<q-skeleton v-else type="QAvatar" size="80px" class="q-mb-sm" />
              <q-icon
                v-if="!profile_picture_url"
                name="person"
                size="55px"
                color="secondary"
                class="q-mr-sm"
              />
            </q-avatar>
          </div>
        </q-card>

        <q-card class="card-style q-pa-sm q-mb-md">
          <div class="row flex justify-between q-mb-none">
            <div class="row items-center no-wrap">
              <q-icon
                name="person"
                size="22px"
                color="secondary"
                class="q-mr-sm"
              />
              <p class="text-bold text-primary text-subtitle1 no-margin">
                Dados Pessoais
              </p>
            </div>
            <q-btn
              icon="edit"
              clickcable
              rounded
              size="10px"
              flat
              color="black"
              to="/user/manage/edit/data"
              v-if="loaded"
            />
          </div>
          <div v-if="loaded" class="text-left q-pa-sm text-caption">
            <p class="text-primary no-margin text-truncate">
              Nome: {{ full_name }}
            </p>
            <p class="text-primary no-margin text-truncate">
              Data de Nascimento: {{ birthdate }}
            </p>
            <p class="text-primary no-margin text-truncate">CPF: {{ cpf }}</p>
            <p class="text-primary no-margin text-truncate">
              E-mail: {{ email }}
            </p>
            <p class="text-primary no-margin text-truncate">
              E-mail: {{ phone }}</p>
          </div>
          <q-skeleton v-else type="QRange" width="100%" height="5rem" style="margin-top: 1rem;" />
        </q-card>

        <q-card class="card-style q-pa-sm q-mb-md">
          <div class="row flex justify-between">
            <div class="row items-center no-wrap">
              <q-icon
                name="location_on"
                size="22px"
                color="secondary"
                class="q-mr-sm"
              />
              <p class="text-bold text-primary text-subtitle1 no-margin">
                Endereço Principal
              </p>
            </div>
            <div>
              <q-btn
								v-if="loaded"
                icon="edit"
                clickcable
                rounded
                size="10px"
                flat
                color="black"
                :to="`/user/manage/edit/address/${address_uuid}`"
              />
              <q-btn
                v-if="!zip_code && loaded"
                no-caps
                flat
                to="/user/manage/info/addaddress"
                class="q-pa-none"
              >
                <q-icon name="add_circle" size="25px" color="secondary" />
              </q-btn>
            </div>
          </div>
          <div v-if="loaded" class="text-left q-pa-sm text-caption">
            <p class="text-primary no-margin text-truncate">
              CEP: {{ zip_code }}
            </p>
            <p class="text-primary no-margin text-truncate">
              UF: {{ state_name }}
            </p>
            <p class="text-primary no-margin text-truncate">
              Cidade: {{ city_name }}
            </p>
            <p class="text-primary no-margin text-truncate">Rua: {{ street }}</p>
            <p class="text-primary no-margin text-truncate">
              Logradouro: {{ neighborhood }}
            </p>
            <p class="text-primary no-margin text-truncate">
              Número: {{ number_street }}
            </p>
            <p class="text-primary no-margin text-truncate">
              Complemento: {{ complement }}
            </p>
          </div>
          <q-skeleton v-else type="QRange" width="100%" height="5rem" style="margin-top: 1rem;" />
        </q-card>
      </div>
    </q-page>
  </q-pull-to-refresh>
</template>

<script>
export default {
  name: 'UserProfile',
  data() {
    return {
      loaded: false,
      profile_picture_url: null,
      user_uuid: '',
      full_name: '',
      address_uuid: '',
      birthdate: '',
      cpf: '',
      user_email: '',
      headers: () => [
        { name: 'token', value: localStorage.getItem('access_token') },
      ],
      zip_code: '',
      state_name: '',
      city_name: '',
      street: '',
      neighborhood: '',
      number_street: '',
      complement: '',
      phone:'',
    };
  },
  mounted() {
    this.get_user_data();
  },
  methods: {
    ToEdit() {
      this.$router.push('/user/manage/edit/');
    },
    async get_user_data() {
      try {
        const response = await fetch(`https://fortis-api.55technology.com/v1/user/logged/`, {
          method: 'GET',
          headers: { token: localStorage.getItem('access_token') },
        });

        if (!response.ok) throw new Error('Network response was not ok');

        const data = await response.json();
        this.full_name = data.user.full_name;
        this.birthdate = data.user.birthdate;
        this.email = data.user.email;
        this.phone = data.user.phone;
        this.cpf = data.user.cpf;
        this.profile_picture_url = data.user.profile_picture_url;
        this.complement = data.user.complement;
        this.neighborhood = data.user.neighborhood;
        this.city_name = data.user.city_name;
        this.state_name = data.user.state_name;
        this.number_street = data.user.number_street;
        this.zip_code = data.user.zip_code;
        this.street = data.user.street;
        this.user_uuid = data.user.uuid;
        this.address_uuid = data.user.address_uuid;

        this.loaded = true;
      } catch (error) {
        this.$q.notify({
          color: 'red-5',
          textColor: 'white',
          icon: 'cloud_done',
          message: 'Ops! Falha ao carregar dados.',
        });
      }
    },
    async refresh(done) {
      await this.get_user_data();
      done();
    },

    on_rejected() {
      this.$q.notify({
        color: 'red-4',
        textColor: 'white',
        icon: 'cloud_done',
        message: 'Falha ao enviar foto.',
      });
    },
    on_upload(info) {
      this.$q.notify({
        color: 'green-6',
        textColor: 'white',
        icon: 'cloud_done',
        message: 'foto enviada com sucesso!.',
      });

      const xhr = info.xhr;
      const data = JSON.parse(xhr.response);

      this.profile_picture_url = null;
      this.profile_picture_url = data.profile_picture_url;
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
</style>
