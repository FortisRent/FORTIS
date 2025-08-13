<template>
    <div class="q-pa-md" style="max-width: 650px;">
        <q-list class="scroll" v-if="!loading">
            <q-pull-to-refresh
                @refresh="refreshList"
                color="secondary-2"
                bg-color="black"
                icon="autorenew"
                scroll-target="body"
            >
                <q-item-label header>
                    Clientes
                </q-item-label>
    
                <q-item v-for="user in users" :key="user.id" class="q-mb-sm item" clickable :to="`/admin/user/profile/${user.uuid}`" v-ripple>
                    <q-item-section avatar>
                        <q-avatar>
                            <img :src="`https://cdn.quasar.dev/img/avatar1.jpg`">
                        </q-avatar>
                    </q-item-section>
                    
                    <q-item-section>
                        <q-item-label>{{ user.name }}</q-item-label>
                        <q-item-label>{{ user.created_at }}</q-item-label>
                        <q-item-label>{{ user.email }}</q-item-label>
                        <q-item-label>{{ user.phone }}</q-item-label>
                        <q-item-label v-if="user.is_active == 0" style="color: red;">Usuário desativado</q-item-label>
                    </q-item-section>
    
                    <q-item-section side v-if="user.is_active == 1">
                        <q-btn
                            @click.stop="deleteTask(user.uuid)"
                            flat
                            round
                            dense
                            color="white"
                            icon="delete"
                        />
                    </q-item-section>

                    <q-item-section side v-if="user.is_active == 0">
                        <q-btn
                            @click.stop="reactivateUser(user.uuid)"
                            flat
                            round
                            dense
                            color="white"
                            icon="check"
                        />
                    </q-item-section>
    
                    <q-item-section side>
                        <q-btn
                            :to="`user/edit/${user.uuid}`"
                            flat
                            round
                            dense
                            color="white"
                            icon="edit"
                        />
                    </q-item-section>
                </q-item>
            </q-pull-to-refresh>
        </q-list>

        <div v-else>
            <LoaderComponent />
        </div>
    </div>
</template>

<script>
import LoaderComponent from '../../../components/LoaderComponent.vue';

export default {
    name: 'UserList',
    components: {
        LoaderComponent,
    },
    data() {
        return {
            users: [],
            loading: true,
        };
    },
    methods: {
        refreshList(done) {
            this.loading = true;
            fetch('http://localhost:8080/v1/user', {
                method: 'GET',
                headers: {
                    'token': localStorage.getItem('access_token')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                this.users.splice(0, this.users.length, ...data);
                this.loading = false;
                done(); 
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                this.$q.notify({
                    color: 'red-5',
                    textColor: 'white',
                    icon: 'cloud_done',
                    message: 'Ops! Falha ao carregar dados.'
                });
                this.loading = false;
                done();
            });
        },
        deleteTask(user_uid) {
            this.$q.dialog({
                title: 'Confirma?',
                message: 'Você realmente quer deletar este usuário?',
                cancel: true,
                persistent: true,
                dark: true
            }).onOk(() => {
                fetch(`http://localhost:5510/v1/user/${user_uid}`, {
                    method: 'DELETE',
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to delete user');
                    }
                    return response.json();
                })
                .then(data => {
                    this.$q.notify({
                        color: 'red-5',
                        textColor: 'white',
                        icon: 'cloud_done',
                        message: data.message
                    });
                    this.refreshList(() => {
                        console.log('User deleted and list refreshed');
                    });
                })
                .catch(error => {
                    this.$q.notify({
                        color: 'red-5',
                        textColor: 'white',
                        icon: 'cloud_done',
                        message: 'Ops! Falha ao carregar dados.'
                    });
                });
            });
        },
        reactivateUser(user_uid) {
            this.$q.dialog({
                title: 'Confirma?',
                message: 'Você realmente quer reativar este usuário?',
                cancel: true,
                persistent: true,
                dark: true
            }).onOk(() => {
                fetch(`http://localhost:5510/v1/user/reactivate/${user_uid}`, {
                    method: 'PUT',
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to reactivate user');
                    }
                    return response.json();
                })
                .then(data => {
                    this.$q.notify({
                        color: 'green-5',
                        textColor: 'white',
                        icon: 'cloud_done',
                        message: data.message
                    });
                    this.refreshList(() => {
                        console.log('User reactivated and list refreshed');
                    });
                })
                .catch(error => {
                    this.$q.notify({
                        color: 'red-5',
                        textColor: 'white',
                        icon: 'cloud_done',
                        message: 'Ops! Falha ao carregar dados.'
                    });
                });
            });
        }
    },
    mounted() {
        this.refreshList(() => {
            console.log('Initial data fetched');
        });
    }
};
</script>

<style>
.item {
    background-image: linear-gradient(to right, #004240, #00300c);
    border-radius: 5px;
}

.empty-space {
    margin-bottom: 15px;
}
</style>
