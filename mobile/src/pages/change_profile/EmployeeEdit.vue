<template>
    <q-page class="bg-white">
      <div class="row justify-around items-center q-pa-md q-mt-lg">
              <q-btn
                  flat
                  round
                  icon="chevron_left"
                  class="text-secondary"
                  color="secondary"
                  size="18px"
                  style="position: absolute; left: 10px;"
                  @click="$router.go(-1)"
              />
              <div class="text-h6 text-primary text-bold q-ml-md">
                  Editar Documentos
              </div>
          </div>
   
          <q-separator class="bg-grey" />
      
          <div class="q-pa-md">
        <div class="flex justify-center text-primary">
            <q-uploader 
            ref="uploader"
						:url="`https://fortis-api.55technology.com/v1/certification/upload/`"
						field-name="user_image"
            style="min-width: 300px; " 
            :headers="headers"
						auto-upload
						@rejected="on_rejected"
						@uploaded="on_upload_profile"
            accept=".jpg,.jpeg,.png"
            batch
            text-color="black"
            label="Frente e Verso da sua CNH"
            color="secondary"
        />
        </div>
        <q-form @submit="on_submit" @reset="on_reset" class="q-gutter-md q-pa-md">
          <q-input
          rounded
          color="secondary"
          v-model="full_name"
          label="Número CNH"
          lazy-rules
          :rules="[ val => val && val.length > 0 || 'Por favor, insira o Nome Completo']"
          no-error-icon
        />
        <q-input
          rounded
          color="secondary"
          v-model="birthdate"
                type="date"
          label="Data de Vencimento CNH"
          lazy-rules
          :rules="[ val => val && val.length > 0 || 'Por favor, insira a Data de Nascimento']"
          no-error-icon
        />
  
        <q-input
          rounded
          color="secondary"
          v-model="identity_document_number"
          label="Tipo"
          lazy-rules
          :rules="[ val => val && val.length > 0 || 'Por favor, insira o Detalhes']"
          no-error-icon
        />
  
        <div class="flex column">
          <q-btn label="Limpar" type="reset" color="secondary" flat  />
          <q-btn label="Atualizar"  @click="create_employee_certification" color="secondary" />
        </div>
        </q-form>
      </div>
      </q-page>
  </template>
  <script>
      export default {
          data() {
              return {
              file_url:'',
              birthdate:'',
              full_name:'',
              phone: '',
              identity_document_number: '',
              };
          },
          mounted() {
          this.check_login_status();
          this.get_employee_by_logged();
  
          },
          methods: {
              check_login_status() {
        if (!localStorage.getItem('access_token')) {
          alert('Not logged in');
          this.$router.push('/login');
        }
      },
      async create_employee_certification(){
  
      fetch(`https://fortis-api.55technology.com/v1/employee/certification/`, {
          method: 'POST',
          headers: {
          'Content-Type': 'application/json',
          'token': localStorage.getItem('access_token')
    },
        body: JSON.stringify({
          file_url:this.file_url,
          birthdate: this.birthdate,
          full_name: this.full_name,
          phone: this.phone,
          identity_document_number: this.identity_document_number
      
    }),
  })
  .then(response => {
    if (!response.ok) {
      throw new Error('Por favor, preencha todos os campos.');
    }
    
    this.showConfirmation = true;
    this.$router.push(`/user/manage`);
  })
  .catch(error => {
    this.$q.notify({
      color: 'red-4',
      textColor: 'white',
      icon: 'cloud_done',
      message: error.message,
    });
  });
  },
      on_rejected(){
			this.$q.notify({
				color: 'red-4',
				textColor: 'white',
				icon: 'cloud_done',
				message: "Falha ao enviar foto."
			});
		},	
		on_upload(info){
			this.$q.notify({
				color: 'green-6',
				textColor: 'white',
				icon: 'cloud_done',
				message: "foto enviada com sucesso!."
			});

			const xhr = info.xhr;
			const data = JSON.parse(xhr.response);

			this.profile_picture_url = null;
			this.profile_picture_url = data.profile_picture_url;
		},
    async get_employee_by_logged() {
          try {
              const response = await fetch(`https://fortis-api.55technology.com/v1/employee/logged/`, {
                  method: "GET",
                  headers: { token: localStorage.getItem("access_token") },
              });

              if (!response.ok) throw new Error("Network response was not ok");

              const data = await response.json();
              this.employee = data.employees[0];

              this.loaded = true;
          } catch (error) {
              this.$q.notify({
                  color: "red-5",
                  textColor: "white",
                  icon: "cloud_done",
                  message: "Ops! Falha ao carregar dados.",
              });
          }
      },
  }
  };
  </script>
  <style>
  </style>