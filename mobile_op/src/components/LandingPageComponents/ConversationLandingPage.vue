<template>
  <section class="conversation-container-lp">
    <div class="conversation-intro-lp">
      <h2 class="text-h3 reset-style-lp conversation-title-lp" style="color: #fff;">Nos explique o serviço que deseja realizar.</h2>
      <ButtonContactLandingPage />
    </div>
    <div class="conversation-bg-lp"></div>
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

  .conversation-container-lp {
    background-color: #4e565c;
    display: grid;
    grid-template-columns: 1fr 1fr;

    .conversation-intro-lp {
      display: flex;
      flex-direction: column;
      gap: 3rem;
      justify-self: end;
      padding: 50px 100px;
    }

    .conversation-bg-lp {
      background-image: url("../../assets/buildings.svg");
      background-size: contain;
      background-repeat: no-repeat;
      background-position: bottom;
      width: 400px;
    }
  }

  /* Reponsivo */
  @media screen and (max-width: 1200px) {
    .conversation-title-lp {
      font-size: 2.5rem;
    }
  }

  @media (max-width: 768px) {
    .conversation-container-lp{
      grid-template-columns: 1fr;

      .conversation-intro-lp{
        padding: 50px;
      }
    }
  }
</style>