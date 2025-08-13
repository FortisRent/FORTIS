<template>
  <section 
    id="advantages" 
    class="advantages-container-lp"
  >
    <div class="advantages-intro-lp">
      <h4 class="text-h5 reset-style-lp advantages-subtitle-lp" style="color: #fb0;">Vantagens</h4>
      <h2 class="text-h3 reset-style-lp advantages-title-lp" style="color: #fff;">Por que escolher nossa plataforma?</h2>
      <p class="advantages-description-lp">
        Oferecemos uma ampla gama de máquinas pesadas de alta qualidade. Comparamos as melhores opções para você, garantindo eﬁciência, segurança e o melhor custo-benefício. A contratação é fácil, rápida e segura.
      </p>
      <p class="advantages-description-lp">
        Analisamos carga, local, altura e outras variantes utilizando cálculos de engenharia seguindo o Plano de Rigging.
      </p>
    </div>

    <div class="advantages-items-lp">
      <div class="advantages-item-lp">
        <div class="advantages-icon-lp">
          <img src="../../assets/hoist.svg" />
        </div>
        <span>Aluguel simples e rápido de máquinas pesadas</span>
      </div>

      <div class="advantages-item-lp">
        <div class="advantages-icon-lp">
          <img src="../../assets/deal.svg" />
        </div>
        <span>Aluguel simples e rápido de máquinas pesadas</span>
      </div>

      <div class="advantages-item-lp">
        <div class="advantages-icon-lp">
          <img src="../../assets/operator.svg" />
        </div>
        <span>Aluguel simples e rápido de máquinas pesadas</span>
      </div>
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

  .advantages-container-lp {
    background-color: #303940;
    display: grid;
    grid-template-columns: 1fr 1fr;
    align-items: center;
    gap: 5rem;
    
    padding: 100px;

    background-image: url("../../assets/map.svg");
    background-size: 700px;
    background-repeat: no-repeat;
    background-position: right bottom;

    .advantages-intro-lp{
      display: flex;
      flex-direction: column;
      gap: 1rem;

      .advantages-description-lp{
        font-size: 1.25rem;
        color: #8f9f9f;
        font-weight: 300;
      }
    }

    .advantages-items-lp{
      display: flex;
      flex-direction: column;
      gap: 1rem;

      .advantages-item-lp {
        font-size: 1.5rem;
        color: #f5f5f5;
        font-weight: 600;

        display: flex;
        align-items: center;
        gap: 1rem;

        .advantages-icon-lp {
          display: flex;
          justify-content: center;
          align-items: center;
          background-color: #4e565c;
          width: 100px;
          height: 100px;
          border-radius: 50%;
        }
      }
    }
  }

  /* Reponsivo */

  @media screen and (max-width: 1600px) {
   .advantages-container-lp {
      grid-template-columns: 1fr;
    }
  }

  @media screen and (max-width: 1200px) {
    .advantages-container-lp{
      .advantages-intro-lp{
        .advantages-title-lp {
          font-size: 2.5rem;
        }

        .advantages-description-lp{
          font-size: 1rem;
        }
      }

      .advantages-items-lp{
        .advantages-item-lp{
          font-size: 1rem;
        }
      }
    }
  }

  @media (max-width: 768px) {
    .advantages-container-lp{
      padding: 50px;
      gap: 1rem;

      .advantages-items-lp{
        .advantages-item-lp{
          .advantages-icon-lp{
             width: 70px;
             height: 50px;

             img {
               width: 50%;
               height: 50%;
             }
          }
        }
      }
    }

    .advantages-title-lp {
      font-size: 1.8rem;
    }
  }
</style>