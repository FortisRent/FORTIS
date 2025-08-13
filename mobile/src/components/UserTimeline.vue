<template>
    <q-scroll-area style="height: 80vh;">
        <q-timeline color="primary" class="q-px-md text-primary">
            <q-timeline-entry color="secondary" avatar="https://cdn.quasar.dev/img/avatar4.jpg">
                <template v-slot:title>
                    Cadastro inicial
                </template>
                <template v-slot:subtitle>
                    05/05/2025
                </template>
            </q-timeline-entry>

            <q-timeline-entry icon="message">
                <template v-slot:title>
                    Iniciou Chamado #2727
                </template>
                <template v-slot:subtitle>
                    16/06/2025
                </template>

                <!-- <div>
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                </div> -->
            </q-timeline-entry>

            <q-timeline-entry color="secondary" icon="add">
                <template v-slot:title>
                    Criou Orçamento # 123
                </template>
                <template v-slot:subtitle>
                    12/05/2025
                </template>
            </q-timeline-entry>

            <q-timeline-entry color="primary" icon="delete">
                <template v-slot:title>
                    Cancelou Orçamento # 123
                </template>
                <template v-slot:subtitle>
                    12/05/2025
                </template>
            </q-timeline-entry>

            <q-timeline-entry icon="done_all" color="secondary">
                <template v-slot:title>
                    Finalizou Chamado #2727
                </template>
                <template v-slot:subtitle>
                    17/06/2025
                </template>

                <div>
                    <p class="text-secondary">Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
                    <q-chip clickable color="green" text-color="white" icon="check"> Finalizado </q-chip>
                </div>
            </q-timeline-entry>
        </q-timeline>
    </q-scroll-area>
</template>