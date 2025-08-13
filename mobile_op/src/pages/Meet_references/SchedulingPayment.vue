<template>
  <q-page class="q-pa-md flex flex-center bg-white">
    <q-btn
			flat
			round
			icon="arrow_back"
			class="q-mr-sm text-primary"
			color="black"
			style="position: absolute; top: 10px; left: 10px; z-index: 10;"
			@click="$router.go(-1)"
    />
    <q-img src="../../assets/meetmedlogo.png" width="80px" height="80px" style="margin-right: 250px;" />
    <q-card flat bordered style="width: 80vw; height: 650px; border-color: #048267 ">

      <div class="q-pa-sm flex text-primary justify-around">
        <p class="text-primary text-h6">Formas de Pagamento</p>
      </div>

      <div class="row q-my-md q-justify-center">
        <q-btn
          :flat="selectedPayment !== 'cartao'"
          color="primary"
          unelevated
          outline
          @click="selectPayment('cartao')"
          icon="credit_card"
          style="width: 130px;"
          class="q-pa-sm q-ma-sm"
        />
        <q-btn
          :flat="selectedPayment !== 'pix'"
          color="primary"
          unelevated
          outline
          class="q-pa-sm q-ma-sm text-bold"
          label="Pix"
          @click="selectPayment('pix')"
          style="width: 130px;"
        />
      </div>

      <div v-if="selectedPayment === 'cartao'">
        <q-input
          v-model="form.nomeTitular"
          outlined
          label="Nome do Titular"
          class="q-ma-md"
        />
        <q-input
          outlined
          v-model="form.numeroCartao"
          label="Número do cartão"
          class="q-ma-md"
          mask="#### #### #### ####"
        />
        <div class="row q-ma-md" style="gap: 10px;">
          <q-input
            outlined
            v-model="form.vencimento"
            label="Vencimento"
            mask="##/##"
            class="col-7"
          />
          <q-input
            outlined
            v-model="form.cvv"
            label="CVV"
            class="col"
            mask="###"
          />
        </div>
        <q-input
          outlined
          v-model="form.parcelamento"
          label="Parcelamento"
          class="q-ma-md"
        />
        <q-input
          outlined
          v-model="form.cpf"
          label="CPF"
          class="q-ma-md"
          mask="###.###.###-##"
        />
        <div class="flex justify-center text-primary">
          <q-checkbox
            v-model="form.salvarDados"
            label="Salvar dados de pagamento"
            class="q-mb-sm"
          />
          <q-btn
            unelevated
            class="q-mb-sm q-mt-sm bg-info"
            style="width: 270px;"
            label="Confirmar Pagamento"
            @click="confirmPayment"
          />
        </div>
      </div>

      <div v-else>
        <div class="flex justify-center text-center text-primary">
          <q-icon name="info" color="black" size="30px"/>
          <p class="q-mt-sm">Info sobre o pagamento Pix</p>
          <p class="text-caption">O pagamento é instantâneo e a liberação é imediata.</p>
          <h4>R$270,00</h4>
        </div>
        <div class="flex justify-center">
          <q-img
            src="https://pngimg.com/uploads/qr_code/qr_code_PNG10.png"
            class="q-mb-md"
            style="width: 150px; height: 150px;"
          />
          <q-btn color="grey-7">Copie o código para pagar</q-btn>
        </div>
      </div>
    </q-card>


    <q-dialog v-model="showConfirmation" persistent>
      <q-card class="bg-white" style="border-color: #048267; border-width: 2px; border-style: solid; width: 300px;">
        <q-card-section class="q-pt-none">
          <div class="q-pa-sm flex items-center">
            <span class="q-pa-md text-center q-ml-sm text-primary text-h6 text-bold">Consulta agendada com sucesso!</span>
            <q-icon name="add_task" color="black" size="60px" class="q-pa-md" style="margin-left: 80px;"  />
          </div>
        </q-card-section>
        <q-card-actions class="flex justify-center">
          <q-btn class="rounded bg-info text-primary text-center" label="OK" @click="showConfirmation = false" style="width: 100px;" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
export default {
  data() {
    return {
      selectedPayment: 'cartao',
      showConfirmation: false,
      form: {
        nomeTitular: '',
        numeroCartao: '',
        vencimento: '',
        cvv: '',
        parcelamento: '',
        cpf: '',
        salvarDados: false
      }
    };
  },
  methods: {
    selectPayment(paymentType) {
      this.selectedPayment = paymentType;
    },
    confirmPayment() {

      fetch(`http://localhost:5510/v1/appointment/scheduling/`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'token': localStorage.getItem('access_token')
        },
        body: JSON.stringify({
          due_date: localStorage.getItem('due_date'),
          doctor_calendar_uuid: localStorage.getItem('calendar_uuid')
        }),
      })
      .then(response => {
        if (!response.ok) {
          throw new Error('Erro ao realizar pagamento.');
        }
        
        this.showConfirmation = true;
        this.$router.push(`/doctor/list`);
      })
      .catch(error => {
        this.$q.notify({
          color: 'red-4',
          textColor: 'white',
          icon: 'cloud_done',
          message: error.message,
        });
      });

    }
  }
};
</script>

<style scoped>
</style>
