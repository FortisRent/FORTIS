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
                  Certificados
              </div>
          </div>
   
          <q-separator class="bg-grey" />
      
          <div class="q-pa-md">
        <div class="flex justify-center text-primary">
          <q-uploader 
            ref="uploader"
            :url="`http://localhost:5510/v1/certification/upload/${this.$route.params.employee_uuid}`"
            field-name="certification_file"
            style="min-width: 300px;" 
            :headers="headers"
            auto-upload
            @rejected="on_rejected"
            @uploaded="on_upload_profile"
            accept=".jpg,.jpeg,.png"
            batch
            text-color="black"
            label="PDF do certificado"
            color="secondary"
          />

        </div>
        <q-form @submit="on_submit" @reset="on_reset" class="q-gutter-md q-pa-md">
          <q-input
          rounded
          color="secondary"
          v-model="name"
          label="Nome do Certificado"
          lazy-rules
          :rules="[ val => val && val.length > 0 || 'Por favor, insira o Nome Completo']"
          no-error-icon
        />
  
        <q-input
          rounded
          color="secondary"
          v-model="details"
          label="Descrição"
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
              name:'',
              details: '',
              
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
      async create_employee_certification() {
      if (!this.file_url || !this.name || !this.details) {
        this.$q.notify({
          color: 'red-4',
          textColor: 'white',
          icon: 'error',
          message: 'Por favor, preencha todos os campos e envie o arquivo.',
        });
        return;
      }

        const employee_uuid = this.$route.params.employee_uuid;

        fetch(`http://localhost:5510/v1/employee/certification/`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'token': localStorage.getItem('access_token'),
          },
          body: JSON.stringify({
            employee_uuid: employee_uuid, // Adicionado
            file_url: this.file_url,
            name: this.name,
            details: this.details,
          }),
        })
          .then(response => {
            if (!response.ok) {
              throw new Error('Erro ao salvar o certificado. Verifique os campos.');
            }
            this.$q.notify({
              color: 'green-4',
              textColor: 'white',
              icon: 'cloud_done',
              message: 'Certificado salvo com sucesso!',
            });
            this.$router.go(-1);
          })
          .catch(error => {
            this.$q.notify({
              color: 'red-4',
              textColor: 'white',
              icon: 'error',
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
    on_upload_profile(info) {
  this.$q.notify({
    color: 'green-6',
    textColor: 'white',
    icon: 'cloud_done',
    message: 'Foto enviada com sucesso!',
  });

  const xhr = info.xhr;
  const data = JSON.parse(xhr.response);

  console.log("URL do arquivo recebido:", data.file_url); // Adicionado para depuração

  this.file_url = data.file_url; // Aqui está o erro se 'file_url' for undefined
}
,

    async get_employee_by_logged() {
          try {
              const response = await fetch(`http://localhost:5510/v1/employee/certification/${this.$route.params.employee_uuid}`, {
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