<template>
  <q-layout view="lHh Lpr lFf">
    <!-- Header do Chat -->
    <q-header elevated class="bg-primary text-white">
      <q-toolbar>
        <q-btn 
          flat 
          round 
          dense 
          icon="arrow_back" 
          @click="goBack"
        />
        <q-toolbar-title class="text-weight-bold">
          Chat com {{ companyName || 'Empresa' }}
        </q-toolbar-title>
        <q-chip 
          :color="isConnected ? 'positive' : 'negative'" 
          text-color="white" 
          size="sm"
        >
          {{ isConnected ? 'Online' : 'Offline' }}
        </q-chip>
      </q-toolbar>
    </q-header>

    <!-- Lista de Mensagens -->
    <q-page-container>
      <q-page class="flex column">
        <q-scroll-area 
          ref="scrollArea"
          class="col chat-messages"
          style="height: calc(100vh - 140px);"
        >
          <div class="q-pa-md">
            <!-- Mensagem de carregamento -->
            <div v-if="loading" class="text-center q-py-md">
              <q-spinner color="primary" size="2em" />
              <div class="text-caption text-grey-6 q-mt-sm">
                Carregando mensagens...
              </div>
            </div>

            <!-- Mensagens -->
            <div 
              v-for="message in messages" 
              :key="message._id"
              class="message-wrapper q-mb-md"
              :class="message.sent ? 'sent' : 'received'"
            >
              <div class="message-bubble" :class="message.sent ? 'bg-primary text-white' : 'bg-grey-3'">
                <div class="message-text">{{ message.text }}</div>
                <div class="message-meta">
                  <span class="message-author" v-if="!message.sent">{{ message.user_name }}</span>
                  <span class="message-time">{{ formatTime(message.created_at) }}</span>
                </div>
              </div>
            </div>

            <!-- Mensagem vazia -->
            <div v-if="!loading && messages.length === 0" class="text-center q-py-xl">
              <q-icon name="chat_bubble_outline" size="4em" color="grey-4" />
              <div class="text-grey-6 q-mt-md">
                Nenhuma mensagem ainda.
                <br>Envie uma mensagem para começar a conversa!
              </div>
            </div>
          </div>
        </q-scroll-area>

        <!-- Input de Mensagem -->
        <div class="chat-input bg-white q-pa-md">
          <div class="row items-end q-gutter-sm">
            <div class="col">
              <q-input
                v-model="messageText"
                outlined
                dense
                placeholder="Digite sua mensagem..."
                autogrow
                :max-height="100"
                @keyup.enter.exact.prevent="sendMessage"
                @keyup.enter.shift.exact.prevent="messageText += '\n'"
                :disable="!isConnected || sending"
              >
                <template v-slot:before>
                  <q-btn 
                    round 
                    flat 
                    dense 
                    icon="attach_file"
                    color="grey-6"
                    size="sm"
                    :disable="!isConnected"
                  />
                </template>
              </q-input>
            </div>
            <div>
              <q-btn
                round
                unelevated
                color="primary"
                icon="send"
                :disable="!canSendMessage"
                :loading="sending"
                @click="sendMessage"
              />
            </div>
          </div>
          
          <!-- Status de conexão -->
          <div v-if="!isConnected" class="text-center q-mt-sm">
            <q-chip color="orange" text-color="white" size="sm">
              <q-icon name="wifi_off" size="xs" class="q-mr-xs" />
              Reconectando...
            </q-chip>
          </div>
        </div>
      </q-page>
    </q-page-container>
  </q-layout>
</template>

<script>
import { io } from 'socket.io-client';
import { useQuasar } from 'quasar';

export default {
  name: 'ChatClient',
  setup() {
    const $q = useQuasar();
    return { $q };
  },
  data() {
    return {
      socket: null,
      budgetUuid: null,
      companyName: '',
      messageText: '',
      messages: [],
      loading: true,
      sending: false,
      isConnected: false,
      reconnectAttempts: 0,
      maxReconnectAttempts: 5,
    };
  },
  computed: {
    canSendMessage() {
      return this.messageText.trim() && this.isConnected && !this.sending;
    }
  },
  mounted() {
    // Pegar o budget UUID da rota ou localStorage
    this.budgetUuid = this.$route.params.budget_uuid || localStorage.getItem('current_budget_uuid');
    
    if (!this.budgetUuid) {
      this.$q.notify({
        type: 'negative',
        message: 'ID do projeto não encontrado',
        timeout: 3000
      });
      this.$router.go(-1);
      return;
    }

    this.initializeSocket();
    this.loadMessages();
  },
  beforeUnmount() {
    if (this.socket) {
      this.socket.disconnect();
    }
  },
  methods: {
    initializeSocket() {
      console.log('🔌 Conectando ao chat...');
      
      this.socket = io('http://localhost:3000', {
        rejectUnauthorized: false,
        timeout: 10000,
      });

      this.socket.on('connect', () => {
        console.log('✅ Conectado ao chat');
        this.isConnected = true;
        this.reconnectAttempts = 0;
        
        // Entrar na sala do budget
        this.socket.emit('join_room', this.budgetUuid);
      });

      this.socket.on('disconnect', () => {
        console.log('❌ Desconectado do chat');
        this.isConnected = false;
      });

      this.socket.on('connect_error', (error) => {
        console.error('❌ Erro de conexão:', error);
        this.isConnected = false;
        
        if (this.reconnectAttempts < this.maxReconnectAttempts) {
          this.reconnectAttempts++;
          setTimeout(() => {
            console.log(`🔄 Tentativa de reconexão ${this.reconnectAttempts}/${this.maxReconnectAttempts}`);
            this.socket.connect();
          }, 3000 * this.reconnectAttempts);
        } else {
          this.$q.notify({
            type: 'negative',
            message: 'Não foi possível conectar ao chat. Verifique sua conexão.',
            timeout: 5000
          });
        }
      });

      // Receber mensagens
      this.socket.on('chat_message', (message) => {
        console.log('📨 Nova mensagem recebida:', message);
        this.addMessage(message);
        this.scrollToBottom();
      });

      // Receber histórico de mensagens
      this.socket.on('chat_history', (messages) => {
        console.log('📜 Histórico de mensagens:', messages);
        this.messages = [];
        messages.forEach(msg => this.addMessage(msg));
        this.loading = false;
        this.scrollToBottom();
      });
    },

    loadMessages() {
      const access_token = localStorage.getItem('access_token');
      if (!access_token) {
        this.$q.notify({
          type: 'negative',
          message: 'Token de acesso não encontrado. Faça login novamente.',
          timeout: 3000
        });
        this.$router.push('/login');
        return;
      }

      // Solicitar histórico de mensagens
      this.socket.emit('load_chat_history', {
        budget_uuid: this.budgetUuid,
        access_token: access_token
      });
    },

    sendMessage() {
      if (!this.canSendMessage) return;

      const access_token = localStorage.getItem('access_token');
      if (!access_token) {
        this.$q.notify({
          type: 'negative',
          message: 'Token expirado. Faça login novamente.',
          timeout: 3000
        });
        return;
      }

      this.sending = true;

      const messageData = {
        ticket: this.budgetUuid,
        access_token: access_token,
        text: this.messageText.trim()
      };

      console.log('📤 Enviando mensagem:', messageData);

      this.socket.emit('chat_message', messageData);

      // Limpar input
      this.messageText = '';
      this.sending = false;
    },

    addMessage(messageData) {
      const userUuid = localStorage.getItem('uuid');
      
      const message = {
        _id: messageData._id || messageData.id || Date.now(),
        text: messageData.text,
        user_uuid: messageData.user_uuid,
        user_name: messageData.user_name,
        created_at: messageData.created_at || new Date().toISOString(),
        sent: messageData.user_uuid === userUuid
      };

      // Evitar mensagens duplicadas
      if (!this.messages.find(m => m._id === message._id)) {
        this.messages.push(message);
      }
    },

    formatTime(timestamp) {
      if (!timestamp) return '';
      
      const date = new Date(timestamp);
      const now = new Date();
      
      // Se é hoje, mostrar só a hora
      if (date.toDateString() === now.toDateString()) {
        return date.toLocaleTimeString('pt-BR', { 
          hour: '2-digit', 
          minute: '2-digit' 
        });
      }
      
      // Se não, mostrar data e hora
      return date.toLocaleString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
      });
    },

    scrollToBottom() {
      this.$nextTick(() => {
        if (this.$refs.scrollArea) {
          this.$refs.scrollArea.setScrollPosition('vertical', 9999, 300);
        }
      });
    },

    goBack() {
      this.$router.go(-1);
    }
  }
};
</script>

<style scoped lang="scss">
.chat-messages {
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}

.message-wrapper {
  display: flex;
  
  &.sent {
    justify-content: flex-end;
    
    .message-bubble {
      max-width: 80%;
      border-radius: 18px 18px 4px 18px;
    }
  }
  
  &.received {
    justify-content: flex-start;
    
    .message-bubble {
      max-width: 80%;
      border-radius: 18px 18px 18px 4px;
    }
  }
}

.message-bubble {
  padding: 12px 16px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  
  .message-text {
    word-wrap: break-word;
    line-height: 1.4;
  }
  
  .message-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 4px;
    font-size: 0.75rem;
    opacity: 0.8;
    
    .message-author {
      font-weight: 500;
    }
  }
}

.chat-input {
  border-top: 1px solid #e0e0e0;
  box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.1);
}
</style>

