<template>
  <section 
    id="offer-equipment" 
    class="offer-container-lp"
  >
    <div class="offer-intro-lp">
      <h4 class="text-h5 reset-style-lp" style="color: #fb0;">Ofereça Seus Equipamentos</h4>
      <h2 class="text-h3 reset-style-lp offer-title-lp" style="color: #000;">Encontre a solução perfeita para o seu projeto</h2>
      <p class="offer-description reset-style-lp">
        Se você é proprietário de máquinas pesadas, nossa plataforma oferece a visibilidade que você precisa.
      </p>
      <p class="offer-description">
        Anuncie seus equipamentos e encontre clientes qualificados prontos para alugar, maximizando o uso e a rentabilidade dos seus ativos.
      </p>
      <ButtonContactLandingPage
        label="Entre em contato"
      />
    </div>

    <q-img
      src="../../assets/appAndMap.svg"
      fit="contain"
    />
  </section>
</template>

<script>
  import ButtonContactLandingPage from 'src/components/LandingPageComponents/ButtonContactLandingPage.vue';

  export default {
    components: {
      ButtonContactLandingPage
    }
  }
</script>

<style scoped lang="scss">
  .reset-style-lp {
    margin: 0;
    padding: 0;
  }

  .offer-container-lp{
    height: calc(100vh - 100px);
    display: grid;
    grid-template-columns: 1fr 1fr;
    padding: 100px;
    gap: 5rem;

    .offer-intro-lp{
      display: flex;
      flex-direction: column;
      gap: 1rem;
      justify-content: center;

      .offer-description{
        font-size: 1.5rem;
        color: #8f9f9f;
        font-weight: 300;
      }
    }
  }

  /* Responsivo */

  @media (max-width: 1200px) {
    .offer-container-lp {
      height: 100%;
      grid-template-columns: 1fr;
      grid-template-rows: 1fr 350px;
    }

    .offer-title-lp {
      font-size: 2.5rem;
    }

    .offer-description{
      font-size: 1rem;
    }
  }

  @media (max-width: 768px) {
    .offer-container-lp {
      padding: 50px;
      height: 100%;
      gap: 1rem;
    }

    .offer-title-lp {
      font-size: 1.8rem;
      line-height: 1.8rem;
    }
  }
</style>