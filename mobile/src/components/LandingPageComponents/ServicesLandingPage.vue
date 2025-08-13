<template>
  <section 
    id="services" 
    class="services-container-lp"
  >
    <div class="services-header-lp">
      <h4 class="text-h5 reset-style-lp" style="color: #fb0;">Serviços</h4>
      <h2 class="text-h3 reset-style-lp services-title-lp" style="color: #fff;">Encontre a solução perfeita para o seu projeto</h2>
      <p class="services-description">
        Alugue guindastes, caminhões e outros equipamentos pesados com rapidez, diretamente de empresas
        confiáveis.
      </p>
    </div>

    <div class="services-items">
      <div class="services-item">
        <q-icon name="check_box" style="color: #fb0;" size="40px"/>
        <span>Alugue equipamentos</span>
      </div>
      <div class="services-item">
        <q-icon name="check_box" style="color: #fb0;" size="40px"/>
        <span>Contrate operadores</span>
      </div>
    </div>

    <div class="services-img-lp"></div>

    <div class="services-help-lp">
      <h2 style="color: #fff;" class="text-h3 reset-style-lp services-title-lp">Precisa de ajuda para escolher o equipamento certo?</h2>
      <p class="services-description">Nossa equipe de especialistas está pronta para orientá-lo a identificar exatamente o que seu projeto precisa.</p>
    </div>
  </section>
</template>

<script>

</script>

<style scoped lang="scss">
  .reset-style-lp {
    margin: 0;
    padding: 0;
  }

  .services-container-lp {
    background-color: #11161a;
    height: calc(100vh - 100px);

    display: grid;
    grid-template-columns: 1fr 1fr;
    grid-template-rows: 0.5fr 1fr;
    padding: 100px 100px 0 100px;
    gap: 5rem;

    .services-header-lp {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    .services-description {
      font-size: 1.5rem;
      color: #8f9f9f;
      font-weight: 300;
    }

    .services-items {
      display: flex;
      gap: 1rem;
      align-items: center;

      .services-item {
        width: 300px;
        height: 120px;
        padding-left: 2rem;
        background-color: #303940;
        border-radius: 20px;
        display: flex;
        align-items: center;
        gap: 1rem;

        span {
          font-size: 1.5rem;
          color: #f5f5f5;
          font-weight: 600;
        }
      }
    }

    .services-img-lp {
      background-image: url("../../assets/notebook.svg");
      background-size: contain;
      background-repeat: no-repeat;
      background-position: right;
      width: 100%;
      height: 100%;
    }

    .services-help-lp {
      display: flex;
      flex-direction: column;
      justify-content: center;
      gap: 1rem;
    }
  }


  /* Responsivo */
  @media (max-width: 1630px) {
    .services-container-lp{
      height: 100%;
    }
  }

  @media (max-width: 1200px) {
    .services-container-lp {
      .services-title-lp {
        font-size: 2.5rem;
      }

      .services-description{
        font-size: 1rem;
      }

      .services-items{
        flex-direction: column;
        justify-content: center;
      }
    }
  }

  @media (max-width: 768px) {
    .services-container-lp{
      padding: 50px;
      grid-template-columns: 1fr;
      grid-template-rows: repeat(4, 0.8fr);
      gap: 1rem;

      .services-title-lp {
        font-size: 1.8rem;
        line-height: 1.8rem;
      }

      .services-img-lp {
        background-position: center;
      }
    }
  }
</style>