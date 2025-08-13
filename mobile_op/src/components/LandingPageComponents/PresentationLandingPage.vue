<template>
  <section class="presentation-lp">
    <div class="presentation-cotent-lp">
      <h3 class="presentation-subtitle-lp text-h4">Soluções em projetos de engenharia</h3>
      <h1 class="presentation-title-lp text-h2">Locação de equipamentos para qualquer tamanho de projeto</h1>
      <q-btn
        no-caps
        flat
        size="lg"
        class="presentation-btn-lp"
      >
        Solicite um orçamento
      </q-btn>
    </div>
    <div class="presentation-img-lp"></div>
  </section>
</template>

<script>
  // Fazer funcionalidade do button para solicitar orçamento
</script>

<style scoped lang="scss">
  .presentation-lp{
    background: linear-gradient(to right, #303940, #11161a);
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    grid-template-rows: calc(100vh - 100px);
    align-items: center;
    overflow-x: hidden;

  .presentation-cotent-lp{
    padding: 0 100px;

    .presentation-subtitle-lp{
      color: #fb0; 
    }

    .presentation-title-lp{
      color: #fff; 
    }

    .presentation-btn-lp{
      background-color: #fb0;
      color: #11161a;
    }
  }

  .presentation-img-lp{
    margin-left: 1rem;
    width: 100%;
    height: 100%;
    background-image: url("../../assets/presentation.svg");
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
  }
}

/* Responsivo */
@media (max-width: 1024px) {
  .presentation-subtitle-lp{
    font-size: 1.5rem;
  }

  .presentation-title-lp{
    font-size: 2.5rem;
  }
}

@media screen and (max-width: 768px) {
  .presentation-lp {
    grid-template-columns: 1fr;
    grid-template-rows: 1fr 1fr;
    height: calc(100vh - 100px);

    .presentation-cotent-lp {
      padding: 0 50px;
    }
    
    h1 {
      font-size: 1.8rem;
      line-height: 2rem;
    }

    h3 {
      font-size: 1rem;
      line-height: 1.5rem;
    }

    .presentation-btn-lp{
      line-height: 1.5rem;
    }

    .presentation-img-lp{
      background-position: right;
    }
  }
}
</style>