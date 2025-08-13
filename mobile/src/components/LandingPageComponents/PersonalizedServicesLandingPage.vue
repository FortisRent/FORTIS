<template>
  <section
    class="container-personalized-services"
  >
    <div class="container-intro-lp">
      <h4 class="text-h5 reset-style-lp">Serviços Personalizados</h4>
      <h2 class="text-h3 reset-style-lp personalized-title-lp" style="color: #fff;">Encontre a solução perfeita para o seu projeto</h2>
    </div>

    <div class="container-cards">
      <q-card>
        <q-icon
          name="img:src/assets/engineer.svg"
          size="50px"
        />
        <h4 class="text-h5 reset-style-lp">Engenheiros</h4>
        <p class="card-description-lp">Aluguel de equipamentos técnicos para projetos complexos.</p>
      </q-card>
      <q-card>
        <q-icon
          name="img:src/assets/excavator.svg"
          size="50px"
        />
        <h4 class="text-h5 reset-style-lp">Construtoras</h4>
        <p class="card-description-lp">Soluções completas para grandes obras.</p>
      </q-card>
      <q-card>
        <q-icon
          name="img:src/assets/box.svg"
          size="50px"
        />
        <h4 class="text-h5 reset-style-lp">Transportadoras</h4>
        <p class="card-description-lp">Transporte especializado com caminhões e guindastes.</p>
      </q-card>
      <q-card>
        <q-icon
          name="img:src/assets/natural-person.svg"
          size="50px"
        />
        <h4 class="text-h5 reset-style-lp">Pessoas Físicas</h4>
        <p class="card-description-lp">Aluguel de máquinas para reformas e construções menores.</p>
      </q-card>
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

  .container-personalized-services {
    padding: 100px;
    background-color: #303940;
    height: calc(100vh - 100px);

    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 5rem;

    background-image: url("../../assets/app.svg");
    background-size: 50%;
    background-repeat: no-repeat;
    background-position: 6rem bottom;

    h4 {
      color: #fb0;
    }

    .container-intro-lp{
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }
  
    .container-cards {
      display: grid;
      grid-template-columns: 350px 350px;
      grid-template-rows: 250px 250px;
      gap: 2rem;

      div{
        background-color: #3b454d;
        padding: 1rem;

        display: flex;
        flex-direction: column;
        gap: 0.5rem;

        .card-description-lp{
           color: #fff;
           font-size: 1.25rem;
           line-height: 1.5;
        }
      }
    }
  }

  /* Responsivo */

  @media (max-width: 1250px) {
    .container-personalized-services{
      gap: 2rem;
      height: 100%;
      grid-template-columns: 1fr;
      background-size: 80%;
      background-repeat: no-repeat;
    }
  }

  @media (max-width: 900px) {
    .container-personalized-services{
      padding: 50px;

      h4 {
        font-size: 1.25rem;
      }

      .personalized-title-lp{
        font-size: 2.5rem;
      }

      .container-cards{
         grid-template-columns: 1fr;
         grid-template-rows: auto;
         gap: 1rem;

         div{
          .card-description-lp{
            font-size: 1rem;
          }
         }
      }
    }
  }
</style>