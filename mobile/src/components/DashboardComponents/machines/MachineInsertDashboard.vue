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
					Adicionar Equipamento
			</div>
		</div>

		<q-separator class="bg-grey" />
			
		
		<q-form @submit="on_submit" @reset="on_reset" class="q-ma-md">
				<div>
					<p class="text-primary text-bold">Máquinas</p>
					<select class="machine-style" v-model="category_uuid">
						<option v-for="machine in machine_list" 
						:key="machine.uuid" :value="machine.uuid">
						{{machine.category_name}}</option>
					</select>
				</div>

				<div v-if="isEscavadeira">
					<p class="text-bold text-primary q-mt-md">Tamanho</p>
					<select class="machine-style" v-if="isEscavadeira" v-model="excavator_size">
						<option v-for="option in optionsEscavadeira" :key="option" :value="option">
							{{ option }}
						</option>
					</select>
				</div>
				<div v-if="isRetroescavadeira">
					<span class="text-primary text-bold">Retroescavadeira Padrão > Profundidade de Escavação Máximo > 4,2 metros</span>
					<select class="machine-style" v-if="isRetroescavadeira" v-model="backhoe_type">
						<option v-for="option in optionsRetroescavadeira" :key="option" :value="option">
							{{ option }}
						</option>
					</select>
				</div>
				<div v-if="isPaCarregadeira">
					<p class="text-bold text-primary q-mt-md">Tamanho</p>
					<select class="machine-style " v-if="isPaCarregadeira" v-model="loader_size">
						<option v-for="option in optionsPaCarregadeira" :key="option" :value="option">
							{{ option }}
						</option>
					</select>
				</div>
				<div v-if="isCompactador">
					<p class="text-bold text-primary q-mt-md">Tipo</p>
					<select class="machine-style " v-if="isCompactador" v-model="compactor_type">
						<option v-for="option in optionsCompactador" :key="option" :value="option">
							{{ option }}
						</option>
					</select>
				</div>	
				<div v-if="isPlataformaElevatoria">
					<p class="text-bold text-primary q-mt-md">Tipo</p>
					<select class="machine-style " v-if="isPlataformaElevatoria" v-model="platform_type">
						<option v-for="option in optionsPlataformaElevatoria" :key="option" :value="option">
							{{ option }}
						</option>
					</select>
				</div>
				<div v-if="isTratoresRoda">
					<p class="text-bold text-primary q-mt-md">Tamanho</p>
					<select class="machine-style" v-if="isTratoresRoda" v-model="wheel_tractor_size">
						<option v-for="option in optionsTratorRoda" :key="option" :value="option">
							{{ option }}
						</option>
					</select>
				</div>
				<div v-if="isTratoresEsteira">
					<p class="text-bold text-primary q-mt-md">Tamanho</p>
					<select class="machine-style" v-if="isTratoresEsteira" v-model="crawler_tractor_size">
						<option v-for="option in optionsTratorEsteira" :key="option" :value="option">
							{{ option }}
						</option>
					</select>
				</div>
				<div v-if="isTratoresEsteira">
					<p class="text-bold text-primary q-mt-md">Tipo do Tamanho</p>
					<select class="machine-style" v-if="isTratoresEsteira" v-model="crawler_tractor_size_type">
						<option v-for="option in optionsTratorEsteiraTamanho" :key="option" :value="option">
							{{ option }}
						</option>
					</select>
				</div>
				<div v-if="isMiniEscavadeira">
					<p class="text-bold text-primary q-mt-md">Tipo</p>
					<select class="machine-style" v-if="isMiniEscavadeira" v-model="mini_excavator_type">
						<option v-for="option in optionsMiniEscavadeira" :key="option" :value="option">
							{{ option }}
						</option>
					</select>
					<span class="text-primary text-bold">Cubagem da Pá Máximo > Até 0,75m3</span>
				</div>
					<div>
						<q-input
							v-if="platform_type === ' Plataforma Elevatório Móvel'"
							rounded
							color="secondary"
							v-model="lifting_load_capacity"
							label="Capacidade de carga nominal (kg)"
						/>
						<q-input
							v-if="platform_type === 'Plataforma Elevatório Móvel'"
							rounded
							color="secondary"
							v-model="lifting_maximum_lifting_height"
							label="Altura máxima de levantamento"
						/>
						<q-input
							v-if="platform_type === 'Veículo com cesto aéreo articulado'"
							rounded
							color="secondary"
							v-model="articulated_load_capacity"
							label="Capacidade de carga nominal (kg)"
						/>
						<q-input
							v-if="platform_type === 'Veículo com cesto aéreo articulado'"
							rounded
							color="secondary"
							v-model="articulated_maximum_lifting_height"
							label="Altura máxima de levantamento"
						/>
						<!-- parametros de elevação -->
						 <p v-if="isGuindasteMoveldeLançaTreliçadaSobreEsteiras || isGuindastesMoveldeLançaTelescopicaSobreEsteiras || isGuindastesMoveldeLançaTelescopicaSobreRodasRoughTerrainRT || isGuindastesMoveldeLançaTreliçadaSobreRodas || isGuindastesMoveldeLançaTelescopicaSobreRodasAllTerrainAT || isGuindasteMoveldeLançaTelescopicaArticuladaMunckSobreCaminhãoComCarroceria || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTrator || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTratoreCarreta" class="text-primary text-bold text-h6 q-mt-md text-center">Parametros de elevaçao da Máquina</p>
						<q-input
							v-if="isGuindasteMoveldeLançaTreliçadaSobreEsteiras || isGuindastesMoveldeLançaTelescopicaSobreEsteiras || isGuindastesMoveldeLançaTelescopicaSobreRodasRoughTerrainRT || isGuindastesMoveldeLançaTreliçadaSobreRodas || isGuindastesMoveldeLançaTelescopicaSobreRodasAllTerrainAT || isGuindasteMoveldeLançaTelescopicaArticuladaMunckSobreCaminhãoComCarroceria || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTrator || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTratoreCarreta"
							rounded
							color="secondary"
							v-model="jib"
							label="JIB (m)"
							type="number"
						/>
						<q-input
							v-if="isGuindasteMoveldeLançaTreliçadaSobreEsteiras || isGuindastesMoveldeLançaTelescopicaSobreEsteiras || isGuindastesMoveldeLançaTelescopicaSobreRodasRoughTerrainRT || isGuindastesMoveldeLançaTreliçadaSobreRodas || isGuindastesMoveldeLançaTelescopicaSobreRodasAllTerrainAT || isGuindasteMoveldeLançaTelescopicaArticuladaMunckSobreCaminhãoComCarroceria || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTrator || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTratoreCarreta"
							rounded
							color="secondary"
							v-model="max_weight"
							label="Peso máximo (kg) / (tn)"
							type="number"
						/>
						<q-input
							v-if="isGuindasteMoveldeLançaTreliçadaSobreEsteiras || isGuindastesMoveldeLançaTelescopicaSobreEsteiras || isGuindastesMoveldeLançaTelescopicaSobreRodasRoughTerrainRT || isGuindastesMoveldeLançaTreliçadaSobreRodas || isGuindastesMoveldeLançaTelescopicaSobreRodasAllTerrainAT || isGuindasteMoveldeLançaTelescopicaArticuladaMunckSobreCaminhãoComCarroceria || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTrator || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTratoreCarreta"
							rounded
							color="secondary"
							v-model="max_height"
							label="Altura máximo (m)"
							type="number"
						/>
						<q-input
							v-if="isGuindasteMoveldeLançaTreliçadaSobreEsteiras || isGuindastesMoveldeLançaTelescopicaSobreEsteiras || isGuindastesMoveldeLançaTelescopicaSobreRodasRoughTerrainRT || isGuindastesMoveldeLançaTreliçadaSobreRodas || isGuindastesMoveldeLançaTelescopicaSobreRodasAllTerrainAT || isGuindasteMoveldeLançaTelescopicaArticuladaMunckSobreCaminhãoComCarroceria || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTrator || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTratoreCarreta"
							rounded
							color="secondary"
							v-model="max_radius"
							label="Raio máximo (m)"
							type="number"
						/>
						<q-input
							v-if="isGuindasteMoveldeLançaTreliçadaSobreEsteiras || isGuindastesMoveldeLançaTelescopicaSobreEsteiras || isGuindastesMoveldeLançaTelescopicaSobreRodasRoughTerrainRT || isGuindastesMoveldeLançaTreliçadaSobreRodas || isGuindastesMoveldeLançaTelescopicaSobreRodasAllTerrainAT || isGuindasteMoveldeLançaTelescopicaArticuladaMunckSobreCaminhãoComCarroceria || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTrator || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTratoreCarreta"
							rounded
							color="secondary"
							v-model="maximum_nominal_lifting_capacity"
							label="Capacidade máxima nomimal de elevação (m)"
							type="number"
						/>
						<q-input
							v-if="isGuindasteMoveldeLançaTreliçadaSobreEsteiras || isGuindastesMoveldeLançaTelescopicaSobreEsteiras || isGuindastesMoveldeLançaTelescopicaSobreRodasRoughTerrainRT || isGuindastesMoveldeLançaTreliçadaSobreRodas || isGuindastesMoveldeLançaTelescopicaSobreRodasAllTerrainAT || isGuindasteMoveldeLançaTelescopicaArticuladaMunckSobreCaminhãoComCarroceria || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTrator || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTratoreCarreta"
							rounded
							color="secondary"
							v-model="maximum_nominal_vertical_reach_capacity"
							label="Capacidade máxima nomimal de alcance vertical (m)"

						/>
						<q-input
							v-if="isGuindasteMoveldeLançaTreliçadaSobreEsteiras || isGuindastesMoveldeLançaTelescopicaSobreEsteiras || isGuindastesMoveldeLançaTelescopicaSobreRodasRoughTerrainRT || isGuindastesMoveldeLançaTreliçadaSobreRodas || isGuindastesMoveldeLançaTelescopicaSobreRodasAllTerrainAT || isGuindasteMoveldeLançaTelescopicaArticuladaMunckSobreCaminhãoComCarroceria || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTrator || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTratoreCarreta"
							rounded
							color="secondary"
							v-model="maximum_reach_without_jib"
							label="Alcance máximo sem JIB (m)"
							type="number"
						/>
						<q-input
							v-if="isGuindasteMoveldeLançaTreliçadaSobreEsteiras || isGuindastesMoveldeLançaTelescopicaSobreEsteiras || isGuindastesMoveldeLançaTelescopicaSobreRodasRoughTerrainRT || isGuindastesMoveldeLançaTreliçadaSobreRodas || isGuindastesMoveldeLançaTelescopicaSobreRodasAllTerrainAT || isGuindasteMoveldeLançaTelescopicaArticuladaMunckSobreCaminhãoComCarroceria || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTrator || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTratoreCarreta"
							rounded
							color="secondary"
							v-model="maximum_vertical_reach"
							label="Alcance máximo vertical (m)"
							type="number"
						/>
						<q-input
						v-if="isGuindasteMoveldeLançaTreliçadaSobreEsteiras || isGuindastesMoveldeLançaTelescopicaSobreEsteiras || isGuindastesMoveldeLançaTelescopicaSobreRodasRoughTerrainRT || isGuindastesMoveldeLançaTreliçadaSobreRodas || isGuindastesMoveldeLançaTelescopicaSobreRodasAllTerrainAT || isGuindasteMoveldeLançaTelescopicaArticuladaMunckSobreCaminhãoComCarroceria || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTrator || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTratoreCarreta"
							rounded
							color="secondary"
							v-model="maximum_horizontal_reach"
							label="Alcance máximo horizontal"
						/>
						<!-- parametros do crane_truck_data -->
						 <div v-if="isGuindastesMoveldeLançaTreliçadaSobreRodas || isGuindastesMoveldeLançaTelescopicaSobreRodasAllTerrainAT || isGuindasteMoveldeLançaTelescopicaArticuladaMunckSobreCaminhãoComCarroceria || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTrator || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTratoreCarreta">
							<p class="text-primary text-bold text-h6 q-mt-md text-center">Caminhão / Chassis</p>
						<q-input
							v-if="isGuindastesMoveldeLançaTreliçadaSobreRodas || isGuindastesMoveldeLançaTelescopicaSobreRodasAllTerrainAT || isGuindasteMoveldeLançaTelescopicaArticuladaMunckSobreCaminhãoComCarroceria || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTrator || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTratoreCarreta"
							rounded
							color="secondary"
							v-model="brand_truck"
							label="Marca"
						/>	
						<q-input
							v-if="isGuindastesMoveldeLançaTreliçadaSobreRodas || isGuindastesMoveldeLançaTelescopicaSobreRodasAllTerrainAT || isGuindasteMoveldeLançaTelescopicaArticuladaMunckSobreCaminhãoComCarroceria || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTrator || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTratoreCarreta"
							rounded
							color="secondary"
							v-model="model_truck"
							label="Modelo"
						/>
						<q-input
							v-if="isGuindastesMoveldeLançaTreliçadaSobreRodas || isGuindastesMoveldeLançaTelescopicaSobreRodasAllTerrainAT || isGuindasteMoveldeLançaTelescopicaArticuladaMunckSobreCaminhãoComCarroceria || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTrator || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTratoreCarreta"
							rounded
							color="secondary"
							v-model="year_truck"
							label="Ano"
							type="number"
						/>
						<q-input
							v-if="isGuindastesMoveldeLançaTreliçadaSobreRodas || isGuindastesMoveldeLançaTelescopicaSobreRodasAllTerrainAT || isGuindasteMoveldeLançaTelescopicaArticuladaMunckSobreCaminhãoComCarroceria || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTrator || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTratoreCarreta"
							rounded
							color="secondary"
							v-model="chassis_number_truck"
							label="Chassis Nº"
							type="number"
						/>
						<q-input
							v-if="isGuindastesMoveldeLançaTreliçadaSobreRodas || isGuindastesMoveldeLançaTelescopicaSobreRodasAllTerrainAT || isGuindasteMoveldeLançaTelescopicaArticuladaMunckSobreCaminhãoComCarroceria || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTrator || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTratoreCarreta"
							rounded
							color="secondary"
							v-model="license_plate_truck"
							label="Placa"
						/>
						<q-input
							v-if="isGuindastesMoveldeLançaTreliçadaSobreRodas || isGuindastesMoveldeLançaTelescopicaSobreRodasAllTerrainAT || isGuindasteMoveldeLançaTelescopicaArticuladaMunckSobreCaminhãoComCarroceria || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTrator || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTratoreCarreta"
							rounded
							color="secondary"
							v-model="vehicle_registration"
							label="Renavam nº"
						/>
						<q-input
							v-if="isGuindastesMoveldeLançaTreliçadaSobreRodas || isGuindastesMoveldeLançaTelescopicaSobreRodasAllTerrainAT || isGuindasteMoveldeLançaTelescopicaArticuladaMunckSobreCaminhãoComCarroceria || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTrator || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTratoreCarreta"
							rounded
							color="secondary"
							v-model="front_axles_count"
							label="Nº de Eixos Dianteira"
							type="number"
						/>
						<q-input
							v-if="isGuindastesMoveldeLançaTreliçadaSobreRodas || isGuindastesMoveldeLançaTelescopicaSobreRodasAllTerrainAT || isGuindasteMoveldeLançaTelescopicaArticuladaMunckSobreCaminhãoComCarroceria || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTrator || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTratoreCarreta"
							rounded
							color="secondary"
							v-model="wheels_per_front_axle"
							label="Nº de Rodas p/ Eixo Dianteira"
							type="number"
						/>
						<q-input
							v-if="isGuindastesMoveldeLançaTreliçadaSobreRodas || isGuindastesMoveldeLançaTelescopicaSobreRodasAllTerrainAT || isGuindasteMoveldeLançaTelescopicaArticuladaMunckSobreCaminhãoComCarroceria || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTrator || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTratoreCarreta"
							rounded
							color="secondary"
							v-model="rear_axles_count"
							label="Nº de Eixos Traseira"
							type="number"
						/>	
						<q-input
							v-if="isGuindastesMoveldeLançaTreliçadaSobreRodas || isGuindastesMoveldeLançaTelescopicaSobreRodasAllTerrainAT || isGuindasteMoveldeLançaTelescopicaArticuladaMunckSobreCaminhãoComCarroceria || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTrator || isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTratoreCarreta"
							rounded
							color="secondary"
							v-model="wheels_per_rear_axle"
							label="Nº de Rodas p/ Eixo Traseira"
							type="number"
						/>
						</div>
							<!-- parametros do trailer_data -->
						<div v-if="isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTratoreCarreta">
							<p class="text-primary text-bold text-h6 q-mt-md text-center">Reboque / Carreta</p>
						<q-input
							v-if="isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTratoreCarreta"
							rounded
							color="secondary"
							v-model="brand_trailer"
							label="Marca"
						/>
						<q-input
							v-if="isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTratoreCarreta"
							rounded
							color="secondary"
							v-model="model_trailer"
							label="Modelo"
						/>
						<q-input
							v-if="isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTratoreCarreta"
							rounded
							color="secondary"
							v-model="year_trailer"
							label="Ano"
							type="number"
						/>
						<q-input
							v-if="isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTratoreCarreta"
							rounded
							color="secondary"
							v-model="chassis_number_trailer"
							label="Chassis Nº"
							type="number"
						/>
						<q-input
							v-if="isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTratoreCarreta"
							rounded
							color="secondary"
							v-model="license_plate_trailer"
							label="Placa"
						/>
						<q-input
							v-if="isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTratoreCarreta"
							rounded
							color="secondary"
							v-model="vehicle_registration_trailer"
							label="Renavam nº"
						/>
						<q-input
							v-if="isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTratoreCarreta"
							rounded
							color="secondary"
							v-model="axles_count_trailer"
							label="Nº de Eixos"
							type="number"
						/>
						<q-input
							v-if="isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTratoreCarreta"
							rounded
							color="secondary"
							v-model="wheels_per_axle_trailer"
							label="Nº de Rodas p/ Eixo"
							type="number"
						/>
						</div>	
					</div>
					<!-- dados padroes das maquinas -->
					<p class="text-primary text-bold text-h6 q-mt-md text-center">Dados padrão da Máquina</p>
					<q-input
						rounded
						color="secondary"
						v-model="name"
						type="text"
						label="Nome do Equipamento"
						lazy-rules
						:rules="[ val => val && val.length > 0 || 'Por favor, insira o Nome do Equipamento']"
						no-error-icon
						class="q-mt-sm"
					/>
					<q-input
						rounded
						color="secondary"
						v-model="brand"
						type="text"
						label="Marca do Equipamento"
						lazy-rules
						:rules="[ val => val && val.length > 0 || 'Por favor, insira a Marca do Equipamento']"
						no-error-icon
						class="q-mt-sm"
					/>
					<q-input
						rounded
						color="secondary"
						v-model="license_plate"
						label="Placa"
						lazy-rules
						:rules="[ val => val && val.length > 0 || 'Por favor, insira a Placa do Equipamento']"
						no-error-icon
						class="q-mt-sm"
					/>
					<q-input
						rounded
						color="secondary"
						v-model="serial_number"
						label="Chassi (número de série)"
						lazy-rules
						:rules="[ val => val && val.length > 0 || 'Por favor, Numero de Série Equipamento']"
						no-error-icon
						class="q-mt-sm"
					/>
					<q-input
						rounded
						color="secondary"
						v-model="year_manufacture"
						label="Ano do Equipamento"
						lazy-rules
						:rules="[ val => val && val.length > 0 || 'Por favor, insira o Ano do Equipamento']"
						no-error-icon
						class="q-mt-sm"
					/>
					<q-uploader 
						:url="`https://fortis-api.55technology.com/v1/machine/photo/upload/`"
						field-name="machine_image"
						label="Foto da Máquina" 
						color="secondary" 
						accept=".jpg,.jpeg,.png"
              			:headers="headers"
						@uploaded="on_upload"
						multiple
						auto-upload
						batch
					/>
					<q-input
						rounded
						color="secondary"
						v-model="description"
						label="Descreva o equipamento"
						lazy-rules
						class="q-mt-sm"
						:rules="[ val => val && val.length > 0 || 'Por favor, descreva o equipamento']"
						no-error-icon
					/>
					<!--Valores de franquia ou fixa-->
					<div>
						<p class="text-black text-bold text-h5">Valores</p>
						
						<div>
							<q-input
								rounded
								color="secondary"
								v-model="price_per_hour"
								label="Preço por Hora"
								mask="R$ ###.###,##"
								reverse-fill-mask
								unmasked-value
								lazy-rules
								:rules="[val => val && val.length > 0 || 'Por favor, insira o Preço por Hora']"
								no-error-icon
							/>
							<q-input
								rounded
								color="secondary"
								v-model="minimum_rental_period"
								label="Período Mínimo de Locação"
								lazy-rules
								:rules="[val => val && val.length > 0 || 'Por favor, insira o Período Mínimo de Locação']"
								no-error-icon
							/>
							<q-input
								rounded
								color="secondary"
								v-model="distance_amount"
								label="Distância"
								lazy-rules
								:rules="[val => val && val.length > 0 || 'Por favor, insira a Distância']"
								no-error-icon
							/>
							<q-input
								rounded
								color="secondary"
								v-model="price_per_distance"
								label="Preço por Distância"
								lazy-rules
								mask="R$ ###.###,##"
								reverse-fill-mask
								unmasked-value
								:rules="[val => val && val.length > 0 || 'Por favor, insira o Preço por Distância']"
								no-error-icon
							/>
							<q-input
								rounded
								color="secondary"
								v-model="special_hour_fee"
								label="Valor de Hora Especial"
								lazy-rules
								mask="R$ ###.###,##"
								reverse-fill-mask
								unmasked-value
								:rules="[val => val && val.length > 0 || 'Por favor, insira a Porcentagem de Hora Especial']"
								no-error-icon
							/>
							<q-input
								rounded
								color="secondary"
								v-model="observation"
								label="Observação"
								lazy-rules
								:rules="[val => val && val.length > 0 || 'Por favor, insira a Observação']"
								no-error-icon
							/>
						</div>
					</div>
					<div class="flex column">
						<q-btn label="Limpar" type="reset" color="secondary" flat  />
						<q-btn label="Adicionar"  @click="insert_machine" color="secondary" />
					</div>
				</q-form>
		</q-page>
</template>
<script>
 
 import { ref } from 'vue'


	export default {
		data() {
			return {
				// parametros escavaçao
				excavator_size:'',
				backhoe_type:'',
				loader_size:'',
				compactor_type:'',
				platform_type:'',
				wheel_tractor_size:'',
				crawler_tractor_size:'',
				crawler_tractor_size_type:'',
				mini_excavator_type:'',

				lifting_load_capacity:'',
				lifting_maximum_lifting_height:'',	
				articulated_load_capacity:'',
				articulated_maximum_lifting_height:'',

				// parametros elevação
				jib:'',
				max_weight:'',
				max_height:'',
				max_radius:'',
				maximum_nominal_lifting_capacity:'',
				maximum_nominal_vertical_reach_capacity:'',
				maximum_reach_without_jib:'',
				maximum_vertical_reach:'',
				maximum_horizontal_reach:'',

				// crane_truck_data
				brand_truck: '',
				model_truck: '',
				year_truck: '',
				chassis_number_truck: '',
				license_plate_truck: '',
				vehicle_registration:'',
				front_axles_count: '',
				wheels_per_front_axle: '',
				rear_axles_count: '',
				wheels_per_rear_axle: '',

				// trailer_data
				brand_trailer:'',
				model_trailer:'', 
				year_trailer:'', 
				chassis_number_trailer: '',
				license_plate_trailer: '',
				vehicle_registration_trailer:'', 
				axles_count_trailer:'', 
				wheels_per_axle_trailer:'',

				// dados padroes 
				machine_photo_list:'',
				machine_list: [],
				category_uuid: null,
				name:'',
				brand:'',
				license_plate:'',
				serial_number:'',
				description:'',
				year_manufacture:'',
				price:'',
				price_per_hour:'',
				minimum_rental_period:'',
				distance_amount:'',
				price_per_distance:'',
				special_hour_fee:'',
				observation:'',

				// selects escavaçao
				val: ref(true),
				EsteiraTamanho:'',
				optionsEscavadeira: ['Escavadeira Pequena > Profundidade de Escavação Máximo > 5,5 metros', ' Escavadeira Média > Profundidade de Escavação Máximo > 6,7 metros', 'Escavadeira Grande > Profundidade de Escavação Máximo > 7,4 metros'],
				optionsRetroescavadeira: ['Convencional 4x2', 'Traçada 4x4'],
				optionsPaCarregadeira: ['Carregadeira Compacta > Cubagem da Pá Máximo > de 0,75m3 a 2,00m3', ' Carregadeira Pequena > Cubagem da Pá Máximo > de 2,00m3 a 4,00m3', 'Carregadeira Média > Cubagem da Pá Máximo > de 4,00m3 a 6,00m3','Carregadeira Grande > Cubagem da Pá Máximo > de 6,00m3 a 10,00m3'],
				optionsCompactador: ['Compactadores de Aterro','Compactadores Pneumáticos','Compactadores de Solo','Compactadores de Solo Vibratório'],
				optionsTratorRoda: ['Pequeno','Médio','Grande'],
				optionsTratorEsteira: ['Pequeno','Médio','Grande'],
				optionsTratorEsteiraTamanho: ['Pequeno (D1)','Pequeno (D2)','Pequeno (D3)','Médio (D4)','Médio (D5)','Médio (D6)',
				'Médio (D7)', 'Médio (D8)', 'Grande (D9)', 'Grande (D10)', 'Grande (D11)','Grande (D12)'
				],
				optionsMiniEscavadeira: ['Sobre esteira','Sobre Rodas'],
				optionsPlataformaElevatoria: [' Plataforma Elevatório Móvel','Veículo com cesto aéreo articulado'],

				headers: () => [ { name: 'token', value: localStorage.getItem('access_token') } ],
			};
		},
		computed: {
		isEscavadeira() {
			const selectedCategory = this.machine_list.find(machine => machine.uuid === this.category_uuid);
			return selectedCategory && selectedCategory.category_name === "Escavadeira";
			},
		isRetroescavadeira() {
			const selectedCategory = this.machine_list.find(machine => machine.uuid === this.category_uuid);
			return selectedCategory && selectedCategory.category_name === "Retroescavadeira";
			}, 
		isPaCarregadeira() {
			const selectedCategory = this.machine_list.find(machine => machine.uuid === this.category_uuid);
			return selectedCategory && selectedCategory.category_name === "Pá Carregadeira";
			},
		isCompactador() {
			const selectedCategory = this.machine_list.find(machine => machine.uuid === this.category_uuid);
			return selectedCategory && selectedCategory.category_name === "Compactador";
			},
		isPlataformaElevatoria() {
			const selectedCategory = this.machine_list.find(machine => machine.uuid === this.category_uuid);
			return selectedCategory && selectedCategory.category_name === "Plataforma elevatória";
			},
		isTratoresRoda() {
			const selectedCategory = this.machine_list.find(machine => machine.uuid === this.category_uuid);
			return selectedCategory && selectedCategory.category_name === "Tratores de roda";
			},
		isTratoresEsteira() {
			const selectedCategory = this.machine_list.find(machine => machine.uuid === this.category_uuid);
			return selectedCategory && selectedCategory.category_name === "Tratores de esteira";
			},
		isMiniEscavadeira() {
			const selectedCategory = this.machine_list.find(machine => machine.uuid === this.category_uuid);
			return selectedCategory && selectedCategory.category_name === "Miniescavadeira";
			},
		isGuindasteMoveldeLançaTreliçadaSobreEsteiras() {
			const selectedCategory = this.machine_list.find(machine => machine.uuid === this.category_uuid);
			return selectedCategory && selectedCategory.category_name === "Guindaste Móvel de lança treliçada sobre esteiras";
			},
		isGuindastesMoveldeLançaTelescopicaSobreEsteiras() {
			const selectedCategory = this.machine_list.find(machine => machine.uuid === this.category_uuid);
			return selectedCategory && selectedCategory.category_name === "Guindastes móvel de lança telescópica sobre esteiras";
			},
		isGuindastesMoveldeLançaTelescopicaSobreRodasRoughTerrainRT() {
			const selectedCategory = this.machine_list.find(machine => machine.uuid === this.category_uuid);
			return selectedCategory && selectedCategory.category_name === "Guindastes móvel de lança telescópica sobre Rodas Rough Terrain (RT)";
			},
		isGuindastesMoveldeLançaTreliçadaSobreRodas() {
			const selectedCategory = this.machine_list.find(machine => machine.uuid === this.category_uuid);
			return selectedCategory && selectedCategory.category_name === "Guindastes móvel de lança treliçada sobre rodas";
			},
		isGuindastesMoveldeLançaTelescopicaSobreRodasAllTerrainAT() {
			const selectedCategory = this.machine_list.find(machine => machine.uuid === this.category_uuid);
			return selectedCategory && selectedCategory.category_name === "Guindastes móvel de lança telescópica sobre Rodas All Terrain (AT)";
			},
		isGuindasteMoveldeLançaTelescopicaArticuladaMunckSobreCaminhãoComCarroceria() {
			const selectedCategory = this.machine_list.find(machine => machine.uuid === this.category_uuid);
			return selectedCategory && selectedCategory.category_name === "Guindaste móvel de lança telescópica articulada (Munck) sobre caminhão com carroceria";
			},
		isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTrator() {
			const selectedCategory = this.machine_list.find(machine => machine.uuid === this.category_uuid);
			return selectedCategory && selectedCategory.category_name === "Guindastes móvel de lança telescópica articulada(Munck) sobre cavalo trator";
			},					
		isGuindastesMoveldeLançaTelescopicaArticuladaMunckSobreCavaloTratoreCarreta() {
			const selectedCategory = this.machine_list.find(machine => machine.uuid === this.category_uuid);
			return selectedCategory && selectedCategory.category_name === "Guindastes móvel de lança telescópica articulada(Munck) sobre cavalo trator e carreta";
			},	
		},
		mounted() {
			this.check_login_status();
			this.get_all_machine_categories()

			console.log(this.$route.params.company_uuid)
		},
		methods: {
			check_login_status() {
				if (!localStorage.getItem('access_token')) {
					alert('Not logged in');
					this.$router.push('/login');
				}
			},
			get_all_machine_categories() {
				this.loading = true;

				fetch(`https://fortis-api.55technology.com/v1/machine/category/`, {
					method: "GET",
					headers: {
						token: localStorage.getItem("access_token"),
					},
				})
				.then((response) => {
					if (!response.ok) {
						throw new Error("Erro ao buscar dados.");
					}
					return response.json();
				})
				.then((data) => {
					console.table(data.machine_categories);

					this.machine_list = data.machine_categories.map(category => ({
						category_name: category.category_name,
						uuid: category.uuid
					}));
				})
				.catch((error) => {
					console.error("Erro ao carregar categorias:", error);
					
					this.$q.notify({
						color: "red-5",
						textColor: "white",
						icon: "error",
						message: "Falha ao carregar categorias.",
					});
				})
				.finally(() => {
					this.loading = false;
				});
			},
			async insert_machine(){
				console.log(this.machine_photo_list);
				fetch('https://fortis-api.55technology.com/v1/machine/', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						'token': localStorage.getItem('access_token')
					},

					body: JSON.stringify({
						company_uuid:		this.$route.params.company_uuid,
						category_uuid: 		this.category_uuid,
						name:				this.name,
						description: 		this.description,
						year_manufacture:	this.year_manufacture,
						price: 				this.price,
						brand:				this.brand,
						license_plate:		this.license_plate,
						serial_number:		this.serial_number,
						machine_photo_list: this.machine_photo_list,
						jib: this.jib,
						max_weight: this.max_weight,
						max_height: this.max_height,
						max_radius: this.max_radius,
						parameters:	{
							excavator_size: this.excavator_size,
							backhoe_type: 	this.backhoe_type,
							loader_size:  	this.loader_size,
							compactor_type: this.compactor_type,
							platform_type: 	this.platform_type,
							lifting_load_capacity: 				this.lifting_load_capacity,
							lifting_maximum_lifting_height:		this.lifting_maximum_lifting_height,
							articulated_load_capacity: 			this.articulated_load_capacity,
							articulated_maximum_lifting_height: this.articulated_maximum_lifting_height,
							wheel_tractor_size: 				this.wheel_tractor_size,
							crawler_tractor_size:				this.crawler_tractor_size,
							crawler_tractor_size_type:			this.crawler_tractor_size_type,
							mini_excavator_type:				this.mini_excavator_type,
							
							maximum_nominal_lifting_capacity: 	this.maximum_nominal_lifting_capacity,
							maximum_nominal_vertical_reach_capacity: this.maximum_nominal_vertical_reach_capacity,
							maximum_reach_without_jib: this.maximum_reach_without_jib,
							maximum_vertical_reach: this.maximum_vertical_reach,
							maximum_horizontal_reach: this.maximum_horizontal_reach,
						},
						crane_truck_data :{
						brand_truck: this.brand_truck,
						model_truck: this.model_truck,
						year_truck: this.year_truck,
						chassis_number_truck: this.chassis_number_truck,
						license_plate_truck: this.license_plate_truck,
						vehicle_registration: this.vehicle_registration,
						front_axles_count: this.front_axles_count,
						wheels_per_front_axle: this.wheels_per_front_axle,
						rear_axles_count: this.rear_axles_count,
						wheels_per_rear_axle: this.wheels_per_rear_axle,
						},

						trailer_data :{
						brand_trailer: this.brand_trailer,
						model_trailer: this.model_trailer,
						year_trailer: this.year_trailer,
						chassis_number_trailer: this.chassis_number_trailer,
						license_plate_trailer: this.license_plate_trailer,
						vehicle_registration_trailer: this.vehicle_registration_trailer,
						axles_count_trailer: this.axles_count_trailer,
						wheels_per_axle_trailer: this.wheels_per_axle_trailer,
						},		

						//se for franquia
						price_per_hour: this.price_per_hour,
						minimum_rental_period: this.minimum_rental_period,
						price_per_distance:	this.price_per_distance,
						distance_amount: this.distance_amount,
						special_hour_fee: this.special_hour_fee,
						observation: this.observation,
						

					}),
				})
				.then(response => {
					if (!response.ok) {
						throw new Error('Por favor, preencha todos os campos.');
					}
	
					this.$q.notify({
						color: 'green-4',
						textColor: 'white',
						icon: 'cloud_done',
						message: 'Equipamento cadastrado.',
					});

					this.$router.go(-1);
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
				message: "O arquivo deve ter menos de 5Mb"
		});
			},	
			on_upload(info){
				this.$q.notify({
					color: 'green-6',
					textColor: 'white',
					icon: 'cloud_done',
					message: "Arquivo enviado. Por favor preencha o restante das informações e envie o formulário."
				});

				const xhr = info.xhr;
				const data = JSON.parse(xhr.response);
				this.machine_photo_list = data.machine_photo_url;
			},
		}
	};
</script>
<style scoped>

.machine-style {
max-width: 340px;
min-width: 340px;
height: 50px;
background-color: white;
border-radius: 3px ;
}


</style>