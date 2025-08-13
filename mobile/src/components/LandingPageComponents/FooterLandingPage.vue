<template>
  <footer
    class="footer-container-lp"
    style="background-color: #303940;"
  >
    <section 
      id="contact" 
      class="contact-container-lp"
    >
      <span class="border-contact-lp"></span>
      <h4 class="text-h5 reset-style-lp" style="color: #fb0;">Contato</h4>
      <q-list>
        <q-item class="reset-style-lp contact-item">
          <q-item-label class="text-h6 reset-style-lp">Florianópolis:</q-item-label>
          <q-item-content>(41) 3286-8780</q-item-content>
        </q-item>

        <q-item class="reset-style-lp contact-item">
          <q-item-label class="text-h6 reset-style-lp">Curitiba:</q-item-label>
          <q-item-content>(71) 3286-8780</q-item-content>
        </q-item>

        <q-item class="reset-style-lp contact-item">
          <q-item-label class="text-h6 reset-style-lp">São Paulo:</q-item-label>
          <q-item-content>(11) 3286-8780</q-item-content>
        </q-item>
      </q-list>
    </section>
    <span class="flex justify-center" style="padding: 0.5rem;">Copyright &copy; 2024 Fortis </span>
  </footer>
</template>

<script>

</script>

<style scoped lang="scss">
  .reset-style-lp {
    margin: 0;
    padding: 0;
  }

  .contact-container-lp{
    border-top: 1px solid #fff;
    border-bottom: 1px solid #fff;
    padding: 50px 100px;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;

    .border-contact-lp {
      width: 40px;
      border: 1px solid #fb0;
    }

    .contact-item {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      color: #fff;

      q-item-content {
        color: #d4d4d4;
        font-weight: bold;
        font-size: 1rem;
      }
    }
  }

  /* Responsivo */
  @media (max-width: 768px) {
    .contact-container-lp{
      padding: 50px;
    }
  }
</style>