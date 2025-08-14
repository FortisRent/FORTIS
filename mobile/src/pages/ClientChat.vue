<template>
  <q-page class="chat-container">
    <!-- Header do Chat -->
    <div class="chat-header">
      <div class="header-content">
        <q-btn 
          flat 
          round 
          dense 
          icon="arrow_back" 
          @click="$router.go(-1)" 
          color="white" 
          class="back-btn"
        />
        
        <div class="header-info">
          <div class="chat-title">{{ $route.query.company_name || 'Chat com Empresa' }}</div>
          <div class="project-info">Projeto: {{ budgetIdentifier }}</div>
        </div>
        
        <div class="connection-status">
          <q-chip 
            :color="isConnected ? 'positive' : 'negative'" 
            text-color="white" 
            size="sm"
            :icon="isConnected ? 'wifi' : 'wifi_off'"
          >
            {{ isConnected ? 'Online' : 'Offline' }}
          </q-chip>
        </div>
      </div>
    </div>

    <!-- Lista de Mensagens -->
    <div class="messages-container" ref="messagesContainer">
      <!-- Loading State com Skeletons -->
      <div v-if="loading" class="messages-list">
        <!-- Skeletons para simular mensagens -->
        <div 
          v-for="n in 8" 
          :key="`skeleton-${n}`"
          class="message-skeleton"
          :class="getSkeletonMessageClass(n)"
          :style="{ animationDelay: `${n * 0.1}s` }"
        >
          <div class="message-bubble" :class="getSkeletonBubbleClass(n)">
            <div class="message-content">
              <!-- Texto da mensagem com larguras variadas -->
              <q-skeleton 
                type="text" 
                :width="getSkeletonMessageWidth(n)" 
                height="16px"
                class="q-mb-xs"
              />
              <q-skeleton 
                v-if="shouldShowSecondLine(n)"
                type="text" 
                :width="getSkeletonSecondLineWidth(n)" 
                height="16px"
                class="q-mb-sm"
              />
              
              <!-- Meta info (nome e hora) -->
              <div class="skeleton-meta">
                <q-skeleton 
                  v-if="!isSkeletonSent(n)"
                  type="text" 
                  width="60px" 
                  height="12px"
                  class="skeleton-sender"
                />
                <q-skeleton 
                  type="text" 
                  width="40px" 
                  height="12px"
                  class="skeleton-time"
                />
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else-if="messages.length === 0" class="empty-state">
        <q-icon name="chat_bubble_outline" size="5em" color="grey-4" />
        <div class="empty-title">Nenhuma mensagem ainda</div>
        <div class="empty-subtitle">Envie uma mensagem para começar a conversa!</div>
      </div>

      <!-- Messages -->
      <div v-else class="messages-list">
        <div 
          v-for="message in messages" 
          :key="message._id"
          class="message-wrapper"
          :class="{ 'message-sent': message.sent, 'message-received': !message.sent }"
        >
          <div class="message-bubble" :class="message.sent ? 'bubble-sent' : 'bubble-received'">
            <div class="message-text">{{ message.text }}</div>
            <div class="message-meta">
              <span v-if="!message.sent" class="sender-name">{{ message.user_name }}</span>
              <span class="message-time">{{ formatTime(message.created_at) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Input Area -->
    <div class="input-container">
      <div class="input-wrapper">
        <q-input 
          v-model="newMessage" 
          outlined 
          dense 
          placeholder="Digite sua mensagem aqui..." 
          autogrow
          :max-height="100" 
          @keyup.enter.exact.prevent="sendMessage" 
          :disable="!isConnected || sending"
          ref="messageInput"
          class="message-input"
          bg-color="white"
        />
        
        <q-btn 
          round 
          unelevated 
          color="primary" 
          icon="send" 
          size="md" 
          :disable="!canSendMessage" 
          :loading="sending"
          @click="sendMessage" 
          class="send-btn"
        >
          <q-tooltip>Enviar mensagem</q-tooltip>
        </q-btn>
      </div>
    </div>
  </q-page>
</template>

<script>
import { io } from 'socket.io-client';
import { useQuasar } from 'quasar';

export default {
  name: 'ClientChat',
  setup() {
    const $q = useQuasar();
    return { $q };
  },
  data() {
    return {
      socket: null,
      budgetUuid: null,
      budgetIdentifier: '',
      newMessage: '',
      messages: [],
      loading: true,
      sending: false,
      isConnected: false,
    };
  },
  computed: {
    canSendMessage() {
      return this.newMessage.trim() && this.isConnected && !this.sending && this.budgetUuid;
    }
  },
  mounted() {
    // Pegar o budget UUID da rota
    this.budgetUuid = this.$route.params.budget_uuid;

    if (!this.budgetUuid) {
      this.$q.notify({
        type: 'negative',
        message: 'ID do projeto não encontrado',
        timeout: 3000
      });
      this.$router.go(-1);
      return;
    }

    // Usar identifier do query parameter se disponível, senão carregar via API
    if (this.$route.query.identifier) {
      this.budgetIdentifier = this.$route.query.identifier;
      console.log('✅ Usando identifier do query parameter:', this.budgetIdentifier);
    } else {
      console.log('⚠️ Query parameter não encontrado, carregando via API...');
      this.loadBudgetData();
    }
    
    this.initializeSocket();
  },
  beforeUnmount() {
    if (this.socket) {
      this.socket.disconnect();
    }
  },
  methods: {
    initializeSocket() {
      console.log('🔌 Cliente conectando ao chat...', this.budgetUuid);

      this.socket = io('http://localhost:3000', {
        rejectUnauthorized: false,
        timeout: 20000,
        reconnection: true,
        reconnectionAttempts: 5,
        reconnectionDelay: 1000,
        forceNew: true
      });

      this.socket.on('connect', () => {
        console.log('✅ Cliente conectado ao chat');
        this.isConnected = true;

        // Entrar na sala do budget
        this.socket.emit('join_room', this.budgetUuid);

        // Carregar histórico de mensagens apenas uma vez
        if (this.loading) {
          this.loadMessages();
        }
      });

      this.socket.on('disconnect', () => {
        console.log('❌ Cliente desconectado do chat');
        this.isConnected = false;
      });

      this.socket.on('connect_error', (error) => {
        console.error('❌ Erro de conexão:', error);
        this.isConnected = false;

        this.$q.notify({
          type: 'negative',
          message: 'Erro ao conectar ao chat',
          timeout: 3000
        });
      });

      // Receber mensagens em tempo real
      this.socket.on('chat_message', (message) => {
        console.log('📨 Nova mensagem recebida:', message);
        this.addMessage(message);
        this.scrollToBottom();
      });

      // Receber histórico de mensagens
      this.socket.on('chat_history', (messages) => {
        console.log('📜 Histórico de mensagens:', messages.length, 'mensagens');
        
        // Evitar reprocessamento se já temos mensagens
        if (this.messages.length > 0 && messages.length === this.messages.length) {
          console.log('⚠️ Histórico já carregado, ignorando...');
          this.loading = false;
          return;
        }
        
        // Limpar mensagens existentes apenas se recebemos novas
        this.messages = [];
        
        // Adicionar mensagens uma por vez
        messages.forEach(msg => this.addMessage(msg));
        
        // Finalizar loading
        this.loading = false;
        
        console.log('✅ Histórico carregado:', this.messages.length, 'mensagens');
        this.scrollToBottom();
      });
    },

    loadMessages() {
      // Evitar múltiplas chamadas simultâneas
      if (!this.loading) {
        console.log('📜 Histórico já foi carregado');
        return;
      }

      const access_token = localStorage.getItem('access_token');
      if (!access_token) {
        this.$q.notify({
          type: 'negative',
          message: 'Token de acesso não encontrado',
          timeout: 3000
        });
        this.loading = false;
        return;
      }

      console.log('📨 Solicitando histórico de mensagens para:', this.budgetUuid);

      // Solicitar histórico de mensagens
      this.socket.emit('load_chat_history', {
        budget_uuid: this.budgetUuid,
        access_token: access_token
      });
    },

    sendMessage() {
      if (!this.canSendMessage) {
        console.log('❌ Não pode enviar mensagem:', {
          canSend: this.canSendMessage,
          connected: this.isConnected,
          sending: this.sending,
          hasMessage: !!this.newMessage.trim(),
          hasBudgetUuid: !!this.budgetUuid
        });
        return;
      }

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
      const messageText = this.newMessage.trim();

      const messageData = {
        ticket: this.budgetUuid,
        access_token: access_token,
        text: messageText
      };

      console.log('📤 Cliente enviando mensagem:', messageData);

      try {
        // Garantir que estamos na sala antes de enviar
        this.socket.emit('join_room', this.budgetUuid);

        // Enviar mensagem via Socket.IO
        this.socket.emit('chat_message', messageData);

        // Limpar input imediatamente
        this.newMessage = '';

        // Focar novamente no input
        this.$nextTick(() => {
          if (this.$refs.messageInput) {
            this.$refs.messageInput.focus();
          }
        });

      } catch (error) {
        console.error('❌ Erro ao enviar mensagem:', error);

        // Restaurar mensagem em caso de erro
        this.newMessage = messageText;

        this.$q.notify({
          type: 'negative',
          message: 'Erro ao enviar mensagem. Tente novamente.',
          timeout: 3000
        });
      } finally {
        // Sempre resetar o estado de envio
        setTimeout(() => {
          this.sending = false;
        }, 500);
      }
    },

    addMessage(messageData) {
      const userUuid = localStorage.getItem('uuid');

      const message = {
        _id: messageData._id || messageData.id || Date.now() + Math.random(),
        text: messageData.text,
        user_uuid: messageData.user_uuid,
        user_name: messageData.user_name,
        created_at: messageData.created_at || new Date().toISOString(),
        sent: messageData.user_uuid === userUuid
      };

      // Verificar duplicatas por ID primeiro, depois por conteúdo
      const isDuplicateById = this.messages.find(m => m._id === message._id);
      const isDuplicateByContent = this.messages.find(m =>
        m.text === message.text &&
        m.user_uuid === message.user_uuid &&
        Math.abs(new Date(m.created_at) - new Date(message.created_at)) < 10000 // 10 segundos
      );

      if (!isDuplicateById && !isDuplicateByContent) {
        console.log('✅ Adicionando nova mensagem:', message.text.substring(0, 50) + '...');
        this.messages.push(message);
        
        // Ordenar mensagens por data para garantir ordem correta
        this.messages.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
      } else {
        console.log('🚫 Mensagem duplicada ignorada:', message.text.substring(0, 50) + '...');
      }
    },

    formatTime(timestamp) {
      if (!timestamp) return '';

      const date = new Date(timestamp);
      return date.toLocaleTimeString('pt-BR', {
        hour: '2-digit',
        minute: '2-digit'
      });
    },

    scrollToBottom() {
      this.$nextTick(() => {
        if (this.$refs.messagesContainer) {
          this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight;
        }
      });
    },

    debugChat() {
      console.log('🐛 DEBUG DO CHAT:');
      console.log('  - Budget UUID:', this.budgetUuid);
      console.log('  - Budget Identifier:', this.budgetIdentifier);
      console.log('  - Conectado:', this.isConnected);
      console.log('  - Socket:', this.socket?.connected);
      console.log('  - Mensagens:', this.messages?.length || 0);
      console.log('  - Nova mensagem:', this.newMessage);
      console.log('  - Pode enviar:', this.canSendMessage);
      console.log('  - Token:', !!localStorage.getItem('access_token'));

      this.$q.notify({
        type: 'info',
        message: `Debug: ${this.messages?.length || 0} mensagens, Conectado: ${this.isConnected ? 'Sim' : 'Não'}`,
        timeout: 3000,
        position: 'top'
      });
    },

    async loadBudgetData() {
      try {
        const response = await fetch(`https://fortis-api.55technology.com/v1/budget/${this.budgetUuid}`, {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
            'token': localStorage.getItem('access_token'),
          },
        });

        if (!response.ok) {
          throw new Error('Erro ao carregar dados do projeto');
        }

        const data = await response.json();
        console.log('📋 Dados do budget carregados:', data);
        
        // Usar o identifier do budget se disponível, senão usar o UUID
        this.budgetIdentifier = data.budget?.identifier || data.identifier || this.budgetUuid;
        
      } catch (error) {
        console.error('❌ Erro ao carregar dados do budget:', error);
        // Em caso de erro, usar o UUID como fallback
        this.budgetIdentifier = this.budgetUuid;
        
        // Não mostrar erro para o usuário, pois é apenas para melhorar a UX
        console.warn('⚠️ Usando UUID como identifier devido ao erro na API');
      }
    },

    // Métodos para controlar os skeletons das mensagens
    getSkeletonMessageClass(index) {
      return this.isSkeletonSent(index) ? 'message-sent' : 'message-received';
    },

    getSkeletonBubbleClass(index) {
      return this.isSkeletonSent(index) ? 'bubble-sent' : 'bubble-received';
    },

    isSkeletonSent(index) {
      // Alternar entre mensagens enviadas e recebidas
      return index % 3 === 1; // 1, 4, 7... são enviadas
    },

    getSkeletonMessageWidth(index) {
      const widths = ['70%', '85%', '60%', '90%', '75%', '65%', '80%', '55%'];
      return widths[index - 1] || widths[0];
    },

    shouldShowSecondLine(index) {
      // Algumas mensagens têm duas linhas para parecer mais realista
      return index % 4 === 0; // 4, 8... têm segunda linha
    },

    getSkeletonSecondLineWidth(index) {
      const widths = ['45%', '50%', '40%', '35%', '55%', '42%', '48%', '38%'];
      return widths[index - 1] || widths[0];
    }
  }
};
</script>

<style scoped lang="scss">
.chat-container {
  display: flex;
  flex-direction: column;
  height: calc(100vh - 60px); // Subtraindo a altura do footer das tabs
  background: #f5f5f5; // Fundo neutro claro
  position: relative;
}

// ========== HEADER ==========
.chat-header {
  background: #303940; // Usando a cor primary da aplicação
  color: white;
  padding: 1rem;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  flex-shrink: 0; // Evita que o header seja comprimido
  z-index: 100;
}

.header-content {
  display: flex;
  align-items: center;
  gap: 1rem;
  max-width: 100%;
}

.back-btn {
  margin-right: 0.5rem;
  
  &:hover {
    background-color: rgba(255, 255, 255, 0.1);
  }
}

.header-info {
  flex: 1;
  min-width: 0; // Para permitir text-overflow
  
  .chat-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0;
    line-height: 1.2;
  }
  
  .project-info {
    font-size: 0.875rem;
    opacity: 0.9;
    margin: 0.25rem 0 0 0;
    line-height: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
}

.connection-status {
  flex-shrink: 0;
}

// ========== MESSAGES CONTAINER ==========
.messages-container {
  flex: 1;
  overflow-y: auto;
  background: #ffffff;
  padding: 1rem;
  display: flex;
  flex-direction: column;
  min-height: 0; // Importante para permitir scroll correto
  
  // Scrollbar personalizada
  &::-webkit-scrollbar {
    width: 4px;
  }
  
  &::-webkit-scrollbar-track {
    background: transparent;
  }
  
  &::-webkit-scrollbar-thumb {
    background: rgba(48, 57, 64, 0.3); // Usando cor primary com transparência
    border-radius: 4px;
  }
}

// ========== LOADING STATE ==========
.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  flex: 1;
  gap: 1rem;
  
  .loading-text {
    color: #4e565c; // Usando cor secondary da aplicação
    font-size: 1rem;
  }
}

// ========== EMPTY STATE ==========
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  flex: 1;
  text-align: center;
  gap: 1rem;
  
  .empty-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #303940; // Usando cor primary da aplicação
    margin: 0;
  }
  
  .empty-subtitle {
    font-size: 1rem;
    color: #4e565c; // Usando cor secondary da aplicação
    margin: 0;
  }
}

// ========== MESSAGES ==========
.messages-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  padding-bottom: 1rem;
}

// ========== MESSAGE SKELETONS ==========
.message-skeleton {
  display: flex;
  max-width: 85%;
  opacity: 0;
  animation: skeletonFadeIn 0.6s ease-out forwards;
  
  &.message-sent {
    align-self: flex-start; // Cliente à direita
  }
  
  &.message-received {
    align-self: flex-end; // Empresa à esquerda
  }
  
  .message-bubble {
    position: relative;
    
    // Adicionar uma animação sutil de shimmer
    &::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(
        90deg, 
        transparent, 
        rgba(255, 255, 255, 0.3), 
        transparent
      );
      animation: shimmer 1.8s infinite;
      border-radius: inherit;
    }
  }
  
  .message-content {
    position: relative;
    z-index: 1;
  }
  
  .skeleton-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 0.5rem;
    opacity: 0.7;
    
    .skeleton-sender {
      margin-right: 0.5rem;
    }
  }
}

.message-wrapper {
  display: flex;
  max-width: 85%;
  
  &.message-sent {
    align-self: flex-start; // Cliente à direita (correto)
  }
  
  &.message-received {
    align-self: flex-end; // Empresa à esquerda (correto) 
  }
}

.message-bubble {
  padding: 0.75rem 1rem;
  border-radius: 1.25rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  position: relative;
  word-wrap: break-word;
  max-width: 100%;
  
  &.bubble-sent {
    background: #303940; // Usando cor primary da aplicação
    color: white;
  }
  
  &.bubble-received {
    background: #f8f9fa;
    color: #303940; // Usando cor primary da aplicação
    border: 1px solid #e3e5e7;
  }
}

.message-text {
  font-size: 1rem;
  line-height: 1.4;
  margin-bottom: 0.25rem;
}

.message-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.75rem;
  opacity: 0.8;
  margin-top: 0.25rem;
  
  .sender-name {
    font-weight: 600;
    margin-right: 0.5rem;
  }
  
  .message-time {
    font-size: 0.7rem;
  }
}

// ========== INPUT AREA ==========
.input-container {
  background: white;
  border-top: 1px solid #e9ecef;
  padding: 1rem;
  box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
  flex-shrink: 0; // Evita que o input seja comprimido
  z-index: 10;
}

.input-wrapper {
  display: flex;
  gap: 0.75rem;
  align-items: flex-end;
  max-width: 100%;
}

.message-input {
  flex: 1;
  
  :deep(.q-field__control) {
    border-radius: 1.5rem;
    border: 2px solid #e3e5e7;
    transition: border-color 0.2s ease;
    
    &:hover {
      border-color: #303940; // Usando cor primary da aplicação
    }
    
    &:focus-within {
      border-color: #303940;
      box-shadow: 0 0 0 3px rgba(48, 57, 64, 0.1);
    }
  }
  
  :deep(.q-field__native) {
    padding: 0.75rem 1rem;
    font-size: 1rem;
  }
}

.send-btn {
  width: 48px;
  height: 48px;
  background: #303940; // Usando cor primary da aplicação
  box-shadow: 0 2px 8px rgba(48, 57, 64, 0.3);
  transition: all 0.2s ease;
  
  &:hover:not(.disabled) {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(48, 57, 64, 0.4);
    background: #2a3138; // Versão mais escura do primary para hover
  }
  
  &:active:not(.disabled) {
    transform: translateY(0);
  }
  
  &.disabled {
    opacity: 0.5;
    background: #4e565c; // Usando cor secondary da aplicação
    box-shadow: none;
  }
}

// ========== RESPONSIVIDADE ==========
@media (max-width: 480px) {
  .chat-container {
    height: calc(100vh - 70px); // Ajuste para mobile com footer
  }
  
  .chat-header {
    padding: 0.75rem;
  }
  
  .header-info {
    .chat-title {
      font-size: 1.125rem;
    }
    
    .project-info {
      font-size: 0.8rem;
    }
  }
  
  .messages-container {
    padding: 0.75rem;
  }
  
  .message-wrapper {
    max-width: 90%;
  }
  
  .message-skeleton {
    max-width: 90%;
  }
  
  .message-bubble {
    padding: 0.625rem 0.875rem;
    font-size: 0.9rem;
  }
  
  .input-container {
    padding: 0.75rem;
  }
  
  .send-btn {
    width: 44px;
    height: 44px;
  }
}

@media (max-width: 360px) {
  .message-wrapper {
    max-width: 95%;
  }
  
  .message-skeleton {
    max-width: 95%;
  }
  
  .header-info {
    .chat-title {
      font-size: 1rem;
    }
    
    .project-info {
      font-size: 0.75rem;
    }
  }
}

// ========== ANIMAÇÕES ==========
.message-wrapper {
  animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes shimmer {
  0% {
    left: -100%;
  }
  100% {
    left: 100%;
  }
}

@keyframes skeletonFadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

// ========== ACESSIBILIDADE ==========
@media (prefers-reduced-motion: reduce) {
  .message-wrapper {
    animation: none;
  }
  
  .send-btn {
    transition: none;
    
    &:hover:not(.disabled) {
      transform: none;
    }
  }
}
</style>
