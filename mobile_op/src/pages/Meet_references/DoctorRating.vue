<template>
    <q-page class="bg-white">
        <div class="flex items-center justify-start q-pt-lg text-primary q-mb-md">
            <q-btn flat round icon="arrow_back" @click="goBack" class="q-mr-sm" style="margin-left: 5px;" />
            <div class="q-ml-md" style="display: flex; flex-direction: column; align-items: flex-start;">
                <div class="text-h5 text-primary text-bold">Avalie sua Consulta</div>
            </div>
        </div>
        <q-separator class="bg-info"/>

        <div class="q-pa-md">
            <div class="text-h6 text-primary text-bold q-mb-none">Como foi sua consulta?</div>
            <p class="text-h7 text-bold q-mb-md">Escolha de 1 a 5 estrelas</p>
            <q-rating 
                v-model="ratingValue" 
                max="5" 
                icon="star" 
                color="yellow-8" 
                size="lg"
                class="q-mb-md"
            />
            <div v-if="ratingValue > 0" class="text-h9 text-bold q-mb-md"></div>

            <div class="text-h6 text-primary text-bold q-mt-md q-mb-none">O que te agradou?</div>
            <p class="text-h7 text-bold q-mb-md">Escolha quantas opções desejar</p>
            <div>
                <q-chip
                    v-for="option in positiveOptions"
                    :key="option"
                    :label="option"
                    :selected="selectedPositiveOptions.includes(option)"
                    :class="{ 'q-chip--selected': selectedPositiveOptions.includes(option) }"
                    clickable
                    @click="toggleOption(option, 'positive')"
                    class="q-mb-sm q-mr-sm"
                />
            </div>
            <div v-if="selectedPositiveOptions.length > 0" class="text-h9 text-bold q-mt-md"></div>

            <div class="text-h6 text-primary text-bold q-mt-lg q-mb-none">O que poderia melhorar?</div>
            <p class="text-h7 text-bold q-mb-md">Escolha quantas opções desejar</p>
            <div>
                <q-chip
                    v-for="option in negativeOptions"
                    :key="option"
                    :label="option"
                    :selected="selectedNegativeOptions.includes(option)"
                    :class="{ 'q-chip--selected': selectedNegativeOptions.includes(option) }"
                    clickable
                    @click="toggleOption(option, 'negative')"
                    class="q-mb-sm q-mr-sm"
                />
            </div>
            <div v-if="selectedNegativeOptions.length > 0" class="text-h9 text-bold q-mt-md"></div>

            <q-list>
                <div class="text-h6 text-primary text-bold q-mt-lg q-mb-md">Favoritar profissional?</div>
                <q-item class="q-mb-lg bg-transparent item custom-border"
                    style="border: 1px solid black; border-radius: 25px; padding: 8px;" v-ripple>
                    <q-item-section avatar>
                    <q-avatar style="width: 70px; height: 70px;">
                      <q-img
                      v-if="selfie_url"
                      :src="`https://fortis-api.55technology.com/${selfie_url}`"
                      width="70px"
                      height="70px"
                      fit="cover"
                      style="border-radius: 100px;"
                    />
                    </q-avatar>
                    </q-item-section>
            
                    <q-item-section class="text-primary text-truncate">
                      <q-item-label><strong>{{ doctor.user_name }}</strong></q-item-label>
                      <q-item-label>{{ doctor.specialty_name }}</q-item-label>
                      <q-item-label> CRM | {{ doctor.crm }}</q-item-label>
                    </q-item-section>
                    <q-item-section side>
                        <q-btn
                            flat
                            round
                            dense
                            color="red"
                            icon="favorite"
                            @click="create_favorite_doctor"
                        />
                    </q-item-section>
                </q-item> 
            </q-list>
            <div class="flex flex-center">
                <q-btn
                    label="Enviar Avaliação"
                    type="submit"
                    no-caps
                    class="text-weight-bolder send-rating"
                    @click="on_submit"
                />
            </div>
        </div>
    </q-page>
</template>

<script>
export default {
  data() {
    return {
      ratingValue: 0,
      positiveOptions: ["Pontualidade", "Ambiente", "Escuta Ativa", "Custo-Benefício", "Qualidade Profissional"],
      selectedPositiveOptions: [],
      negativeOptions: ["Pontualidade", "Ambiente", "Comunicação", "Acessibilidade"],
      selectedNegativeOptions: [],
      doctor: {
		  user_name: '',
		  specialty_name: '',
		  crm: '',
		  doctor_uuid:'',
      selfie_url:null,
		},
    };
    
  },
  methods: {
  goBack() {
    this.$router.go(-1);
  },
  toggleOption(option, type) {
    if (type === "positive") {
      const selectedIndex = this.selectedPositiveOptions.indexOf(option);
      if (selectedIndex === -1) {
        this.selectedPositiveOptions.push(option);
      } else {
        this.selectedPositiveOptions.splice(selectedIndex, 1);
      }
    } else if (type === "negative") {
      const selectedIndex = this.selectedNegativeOptions.indexOf(option);
      if (selectedIndex === -1) {
        this.selectedNegativeOptions.push(option);
      } else {
        this.selectedNegativeOptions.splice(selectedIndex, 1);
      }
    }
  },
  on_submit() {
    const positive_details = this.selectedPositiveOptions.join(", ");
    const negative_details = this.selectedNegativeOptions.join(", ");

    const requestBody = {
      appointment_uuid: this.$route.params.appointment_uuid,
      score: this.ratingValue,
      positive_details,
      negative_details,
    };

    fetch(`https://fortis-api.55technology.com/v1/review`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'token': localStorage.getItem('access_token')
      },
      body: JSON.stringify(requestBody),
    })
      .then(response => {
        if (!response.ok) {
          throw new Error('Erro ao cadastrar avaliação.');
        }
        return response.json();
      })
      .then(data => {
        console.log(data);
        this.$q.notify({
          color: 'green-4',
          textColor: 'white',
          icon: 'cloud_done',
          message: 'Avaliação enviada com sucesso.',
        });
        this.$router.go(-1);
      })
      .catch(error => {
        console.error(error);
        this.$q.notify({
          color: 'red-4',
          textColor: 'white',
          icon: 'cloud_done',
          message: error.message,
        });
      });
  },
  async get_doctor_data() {
    try {
      const response = await fetch(
        `https://fortis-api.55technology.com/v1/doctor/appointment/${this.$route.params.appointment_uuid}`
      );
      if (!response.ok) throw new Error('Erro ao carregar dados do médico.');
      const data = await response.json();
      this.doctor = data.doctor;
      this.selfie_url = data.doctor.selfie_url
    } catch (error) {
      console.error(error.message);
    } finally {
      this.loading = false;
    }
  },
  async create_favorite_doctor() {

    fetch(`https://fortis-api.55technology.com/v1/favorite/professional/`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'token': localStorage.getItem('access_token')
      },
      body: JSON.stringify({
        doctor_uuid: this.doctor.doctor_uuid 
      }),
    })
    .then(response => {
      if (!response.ok) {
        throw new Error('Médico já Favoritado.');
      }

      this.$q.notify({
        color: 'green-4',
        textColor: 'white',
        icon: 'cloud_done',
        message: 'Médico Favoritado com sucesso.',
      });
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
},
mounted() {
  this.get_doctor_data();
}

};
</script>

<style>
  .custom-border {
    border-color: #048267 !important;
  }
  .q-chip {
    margin: 0.5rem 0.25rem;
    background-color: white;
    border: 1px solid #048267;
    color: black;
  }
  .q-chip--selected {
    background-color: #048267 !important;
    color: white !important;
  }
  .send-rating {
    background-color: #048267;
    color: white;
    border-radius: 30px;
  }
  .text-truncate {
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		display: block;
		max-width: 100%;
	}
</style>
