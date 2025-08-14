<template>
    <q-page class="bg-white">
      
        <div class="flex justify-between ">
        <h4 class="q-mb-sm q-ml-md text-primary q-ml-sm">Favoritos</h4>
      </div>

      <q-separator class="bg-info q-mb-md"/>

         
      <q-list class="scroll q-pa-sm" v-if="!loading">
        <q-pull-to-refresh @refresh="refreshList" color="primary" bg-color="white " icon="autorenew"
          scroll-target="body">

          <q-item v-for="doctor in doctors" :key="doctor.id" class="q-mb-sm bg-transparent item custom-border"
            style="border: 1px solid black; border-radius: 8px; padding: 8px;" clickable :to="`/doctor/profile/${doctor.doctor_uuid}`"  v-ripple>
            <q-item-section avatar>
              <q-avatar style="width: 70px; height: 70px;">
                            <q-img
                            v-if="doctor.profile_photo"
                            :src="`https://fortis-api.55technology.com/${doctor.profile_photo}`"
                            width="70px"
                            height="70px"
                            fit="cover"
                            style="border-radius: 100px;"
                            />
                        </q-avatar>
            </q-item-section>
  
            <q-item-section class="text-primary">
              <q-item-label class="text-truncate"><strong>{{ doctor.user_name }}</strong></q-item-label>
              <q-item-label class="text-truncate">{{ doctor.specialty_name }}</q-item-label>
              <q-item-label class="text-truncate"> CRM | {{ doctor.crm }}</q-item-label>
            </q-item-section>
  
            <q-item-section side>
                        
                        <q-btn
                            v-on:click.prevent="delete_favorite(doctor.uuid)"
                            flat
                            round
                            dense
                            color="red"
                            icon="favorite"
                        />
            </q-item-section>
          </q-item>
        </q-pull-to-refresh>
      </q-list>
  
      <div v-else>
        <LoaderComponent />
      </div>
    </q-page>
  </template>
  
  <script>
  import LoaderComponent from '../../components/LoaderComponent.vue';
  
  export default {
    name: 'DoctorList',
    components: {
      LoaderComponent,
    },
    data() {
      return {
        doctors: [],
        loading: true,
        order_options: ['Distância (menor para maior)', 'Distância (maior para menor)', 'Valor (menor para maior)', 'Valor (maior para menor)']
      };
    },
    methods: {
      refreshList(done) {
        this.loading = true;
  
        fetch(`https://fortis-api.55technology.com/v1/favorite/professional/logged/`, {
          method: 'GET',
          headers: {
             'token': localStorage.getItem('access_token')
          }
        })
          .then(response => {
            if (!response.ok) {
              throw new Error('Network response was not ok');
            }
            return response.json();
          })
          .then(data => {
            this.doctors=[];
            this.doctors.splice(0, this.doctors.lenght, ...data.favorite_professionals);
            this.loading = false;
  
            if (this.doctors.lenght == 0) {
              // this.$q.notify({
              //   color: 'secondary-8',
              //   textColor: 'white',
              //   icon: 'cloud_done',
              //   message: 'Nenhum médico favoritado.'
              // });
            }
  
            done();
          })
          .catch(error => {
            console.error('Error fetching data:', error);
  
            // this.$q.notify({
            //   color: 'red-5',
            //   textColor: 'white',
            //   icon: 'cloud_done',
            //   message: 'Médico Desfavoritado.'
            // });
  
            this.loading = false;
            done();
          });
      },
     async delete_favorite(uuid) {
			this.$q.dialog({
				title: 'Confirma?',
				message: 'Você realmente quer desfavoritar?',
				cancel: true,
				persistent: true,
				dark: true
			}).onOk(() => {
				fetch('https://fortis-api.55technology.com/v1/favorite/professional/' + uuid, {
					method: 'DELETE',
					headers: {
						'token': localStorage.getItem('access_token')
					}
				})
				.then(response => {
					if (!response.ok) {
						throw new Error('Network response was not ok');
					}

					this.refreshList();
					
				})
				.catch(error => {
					console.error('Error fetching data:', error);

					// $q.notify({
					// 	color: 'red-5',
					// 	textColor: 'white',
					// 	icon: 'cloud_done',
					// 	message: 'Ops! Falha ao desfavoritar.'
					// });
				})
			}
			).onCancel(() => {
				// console.log('>>>> Cancel')
			}).onDismiss(() => {
				// console.log('I am triggered on both OK and Cancel')
			})
     } 
    },
    mounted() {
      this.refreshList(() => {
        console.log('Initial data fetched');
      });
    }
  }
  </script>
  <style>
  .custom-border {
    border-color: #048267 !important;
  }  
  .custom-rounded {
    border-radius: 12px;
  }
  .text-truncate {
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		display: block;
		max-width: 100%;
	}
  </style>
  