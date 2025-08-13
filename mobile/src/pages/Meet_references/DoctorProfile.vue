<template>
	<q-page class="bg-white">
	  <q-card class="my-card" flat style="background-color: transparent;">
		<div class="flex justify-center relative">
		  <q-btn
			flat
			round
			icon="arrow_back"
			class="q-mr-sm text-primary"
			color="white"
			style="position: absolute; top: 10px; left: 10px; z-index: 10; border: 2px solid grey; background-color: grey;"
			@click="$router.go(-1)"
		  />
  
		  <q-skeleton
			v-if="loading"
			type="rectangle"
			width="500px"
			height="300px"
			class="rounded-borders"
		  ></q-skeleton>
		  <q-img
			v-else-if="!image_url"
			src="https://images.pexels.com/photos/994605/pexels-photo-994605.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1"
			width="500px"
			height="250px"
			style="filter: blur(2px);"
		  />
		  <q-img
			v-else
			:src="`http://localhost:5510/${image_url}`"
			width="500px"
			height="250px"
			style="filter: blur(2px);"
		  />
  
		  <q-skeleton
			v-if="loading"
			type="circle"
			width="170px"
			height="170px"
			class="absolute rounded-circle"
			style="bottom: 340px; left: 50%; transform: translateX(-50%);"
		  ></q-skeleton>
		  <q-img
			v-else
			:src="selfie_url ? `http://localhost:5510/${selfie_url}` : 'https://via.placeholder.com/170'"
			width="170px"
			height="170px"
			fit="cover"
			style="position: absolute; bottom: 340px; left: 50%; transform: translateX(-50%); border-radius: 100px;"
		  />
		</div>
  
		<q-card-section>
		  <div class="text-center">
			<q-skeleton v-if="loading" type="text" width="60%" class="q-mb-sm"></q-skeleton>
			<div v-else class="text-h6 text-primary text-truncate">{{ doctor.user_name }}</div>
  
			<q-skeleton v-if="loading" type="text" width="40%"></q-skeleton>
			<span v-else class="text-caption text-primary text-truncate">
			  CRM | {{ doctor.crm }}
			</span>
		  </div>
		</q-card-section>
  
		<q-card-section class="q-pa-md">
		  <div class="text-primary">
			<div class="row items-center q-gutter-sm">
			  <q-skeleton v-if="loading" type="text" width="100%"></q-skeleton>
			  <template v-else>
				<span>Especialidade</span>
				<div class="col q-mx-sm">
				  <q-separator />
				</div>
				<div class="specialty-truncate">
				  <span>{{ doctor.specialty_name }}</span>
				</div>
			  </template>
			</div>
  
			<div class="row items-center q-gutter-sm">
			  <q-skeleton v-if="loading" type="text" width="100%"></q-skeleton>
			  <template v-else>
				<span>Valor da Consulta</span>
				<div class="col q-mx-sm">
				  <q-separator />
				</div>
				<div class="specialty-truncate">
				  <span>R$ {{ doctor.amount / 100 }}</span>
				</div>
			  </template>
			</div>
  
			<div class="row items-center q-gutter-sm">
			  <q-skeleton v-if="loading" type="text" width="100%"></q-skeleton>
			  <template v-else>
				<span>Nota</span>
				<div class="col q-mx-sm">
				  <q-separator />
				</div>
				<template v-if="isNumeric(doctor.average_score)">
				  <div class="specialty-truncate">
					<span>{{ parseFloat(doctor.average_score).toFixed(2) }}</span>
				  </div>
				  <q-icon name="star" color="secondary" />
				</template>
				<template v-else>
				  <div class="specialty-truncate">
					<span>Novo</span>
				  </div>
				</template>
			  </template>
			</div>
		  </div>
		</q-card-section>
  
		<q-card-actions style="display: flex; flex-direction: column; align-items: center;">
		  <div class="flex justify-center q-pa-md q-mb-md">
			<q-skeleton v-if="loading" type="rectangle" width="200px" height="60px"></q-skeleton>
			<q-btn
			  v-else
			  id="calendar_btn"
			  clickable
			  :to="`/doctor/${this.$route.params.doctor_uuid}/scheduling/calendar/`"
			  label="AGENDAR"
			  class="rounded text-weight-bolder"
			/>
		  </div>
  
		  <div class="q-mb-sm">
			<q-skeleton v-if="loading" type="rectangle" width="250px" height="40px"></q-skeleton>
			<q-btn
			  v-else
			  id="favourite_btn"
			  label="Favoritar"
			  icon="favorite"
			  class="rounded"
			  @click="create_favorite_doctor"
			/>
		  </div>
		</q-card-actions>
	  </q-card>
	</q-page>
  </template>
  
  <script>
  export default {
	data() {
	  return {
		loading: true,
		doctor: {
		  image_url: null,
		  selfie_url: null,
		  user_name: "",
		  specialty_name: "",
		  crm: "",
		  doctor_uuid: "",
		  average_score: null,
		  amount: 0,
		},
	  };
	},
	mounted() {
	  this.get_doctor_data();
	},
	methods: {
	  async get_doctor_data() {
		try {
		  const response = await fetch(`http://localhost:5510/v1/doctor/${this.$route.params.doctor_uuid}`);
		  if (!response.ok) throw new Error("Erro ao carregar dados do médico.");
		  const data = await response.json();
		  this.doctor = data.doctor;
		  this.image_url = data.doctor.image_url;
		  this.selfie_url = data.doctor.selfie_url;
		} catch (error) {
		  console.error(error.message);
		} finally {
		  this.loading = false;
		}
	  },
	  async create_favorite_doctor() {
		try {
		  const response = await fetch(`http://localhost:5510/v1/favorite/professional/`, {
			method: "POST",
			headers: {
			  "Content-Type": "application/json",
			  token: localStorage.getItem("access_token"),
			},
			body: JSON.stringify({
			  doctor_uuid: this.$route.params.doctor_uuid,
			}),
		  });
		  if (!response.ok) throw new Error("Médico já Favoritado.");
		} catch (error) {
		  console.error(error.message);
		}
	  },
	  isNumeric(value) {
		return !isNaN(value) && value !== null && value !== undefined;
	  },
	},
  };
  </script>
  
  <style>
  .rounded {
	border-radius: 12px;
  }
  
  #favourite_btn {
	background: white;
	color: #048267;
	font-size: 12px;
	border: 1px solid #048267;
	width: 250px;
  }
  
  #calendar_btn {
	background: #048267;
	color: white;
	font-size: 20px;
	width: 200px;
	height: 60px;
  }
  
  .text-truncate {
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
	display: block;
	max-width: 100%;
  }
  
  .specialty-truncate {
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
	display: block;
	max-width: 40%;
  }
  </style>
  