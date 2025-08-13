<template>
    <q-list>
      <q-item-section class="q-pa-md">
            <q-item-label><strong>Ticket #1234</strong></q-item-label>
            <q-item-label class="q-py-sm" caption> <strong>Origem: </strong> maria@gmail.com</q-item-label>
            <q-item-label caption> <strong>Participantes: </strong> geovani@eztools.com (admin), maria@gmail.com (cliente), suporte@eztools.com (suporte)</q-item-label>
            <div class="flex flex-row">
                <q-btn icon="check" flat color="green-8" class="q-ma-sm q-mt-lg" label="Finalizar"/>
                <q-btn icon="bolt" flat color="blue-10" class="q-ma-sm q-mt-lg" label="Encaminhar"/>
                <q-btn icon="person" flat color="blue-10" class="q-ma-sm q-mt-lg" label="Transferir"/>
            </div>
      </q-item-section>

      <q-item-label header>
        10/04/2024
      </q-item-label>

      <q-item clickable v-ripple>
        <q-item-section avatar class="flex flex-center">
          <q-avatar class="q-ma-sm">
            <img src="https://cdn.quasar.dev/img/avatar2.jpg">
          </q-avatar>
        </q-item-section>
        
        <q-item-section>
            <q-item-label lines="1"><strong>Maria</strong></q-item-label>
            <q-item-label lines="10" class="q-py-sm">
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Nisi inventore commodi porro? Tempore ipsam quod libero?
            </q-item-label>
          <q-item-label caption lines="10">
                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Assumenda facere aut totam odio, perferendis, debitis repellat incidunt dolor odit, recusandae nostrum. Odio, dolores, eveniet praesentium vero hic eum architecto delectus labore repellat eaque ex officia quas eius et nobis corporis tenetur. Mollitia dicta eveniet atque nemo maxime omnis, harum officiis rerum rem aut illo pariatur autem tenetur, doloribus molestiae suscipit animi ipsam assumenda reiciendis.
          </q-item-label>
        </q-item-section>

        <q-item-section side top>
          18:55
        </q-item-section>
      </q-item>

      <q-separator class="q-my-md" inset="item" />

      <q-item-label header>
        11/04/2024
      </q-item-label>

      <q-item clickable v-ripple>
        <q-item-section avatar>
          <q-avatar class="q-ma-sm">
            <img src="https://cdn.quasar.dev/img/avatar4.jpg">
          </q-avatar>
        </q-item-section>

        <q-item-section>
          <q-item-label lines="1"><strong>Geovane</strong></q-item-label>
          <q-item-label>
            08:28 - Mensagem encaminhada para @SuporteTeste (suporte@eztools.com)
          </q-item-label>
          <q-item-label caption lines="2">
            Olá! Estamos trabalhando para resolver isso.
          </q-item-label>
        </q-item-section>

        <q-item-section side top>
          08:30
        </q-item-section>
      </q-item>

      <q-separator class="q-my-md" inset="item" />

      <q-item clickable v-ripple>
        <q-item-section avatar>
          <q-avatar class="q-ma-sm">
            <img src="https://cdn.quasar.dev/img/avatar2.jpg">
          </q-avatar>
        </q-item-section>

        <q-item-section>
          <q-item-label lines="1"><strong>Maria</strong></q-item-label>
          <q-item-label caption lines="10" class="q-py-md">
                Ok, muito obrigada.
            </q-item-label>
        </q-item-section>

        <q-item-section side top>
          18:55
        </q-item-section>
      </q-item>

      <q-separator class="q-my-md" inset="item" />

    </q-list>
</template>