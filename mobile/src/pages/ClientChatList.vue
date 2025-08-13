<template>
  <q-page class="client-chat-list-page">
    <!-- Header -->
    <div class="page-header q-pa-md bg-primary text-white">
      <div class="text-h5 text-center">Meus Chats</div>
      <div class="text-caption text-center opacity-80">
        Conversas com empresas
      </div>
    </div>

    <!-- Filtros/Busca -->
    <div class="search-section q-pa-md">
      <q-input
        v-model="searchText"
        outlined
        dense
        placeholder="Buscar por empresa ou projeto..."
        clearable
        class="search-input"
      >
        <template v-slot:prepend>
          <q-icon name="search" color="grey-6" />
        </template>
        <template v-slot:append v-if="searchText">
          <q-chip 
            v-if="filteredChats.length > 0"
            size="sm" 
            color="primary" 
            text-color="white"
            class="search-results-count"
          >
            {{ filteredChats.length }} {{ filteredChats.length === 1 ? 'resultado' : 'resultados' }}
          </q-chip>
        </template>
      </q-input>
      
      <!-- Filtros adicionais -->
      <div v-if="chats.length > 0" class="filter-chips q-mt-sm">
        <q-chip
          v-model:selected="showOnlyUnread"
          clickable
          :color="showOnlyUnread ? 'negative' : (unreadCount > 0 ? 'orange' : 'grey-4')"
          :text-color="showOnlyUnread ? 'white' : (unreadCount > 0 ? 'white' : 'grey-8')"
          icon="mark_as_unread"
          size="sm"
          class="q-mr-sm unread-filter-chip"
          :class="{ 'has-unread-messages': unreadCount > 0 && !showOnlyUnread }"
        >
          Não lidas 
          <q-badge 
            v-if="unreadCount > 0" 
            :color="showOnlyUnread ? 'white' : 'negative'" 
            :text-color="showOnlyUnread ? 'negative' : 'white'"
            rounded
            class="q-ml-xs filter-badge"
          >
            {{ unreadCount }}
          </q-badge>
          <span v-else class="q-ml-xs">(0)</span>
        </q-chip>
        

      </div>
    </div>

    <!-- Loading com Skeletons -->
    <div v-if="loading" class="chat-list-container">
      <div class="chat-list-scroll">
        <!-- Skeleton para simular itens de chat -->
        <div 
          v-for="n in 6" 
          :key="`skeleton-${n}`"
          class="chat-skeleton"
          style="margin-bottom: 12px;"
        >
          <q-item class="chat-item">
            <q-item-section avatar>
              <q-skeleton type="QAvatar" size="48px" />
            </q-item-section>

            <q-item-section>
              <q-item-label>
                <q-skeleton 
                  type="text" 
                  :width="getSkeletonWidth('company', n)" 
                />
              </q-item-label>
              <q-item-label caption>
                <q-skeleton 
                  type="text" 
                  :width="getSkeletonWidth('project', n)" 
                />
              </q-item-label>
              <q-item-label caption>
                <q-skeleton 
                  type="text" 
                  :width="getSkeletonWidth('message', n)" 
                />
              </q-item-label>
            </q-item-section>

            <q-item-section side class="chat-side">
              <div class="column items-end">
                <q-skeleton type="rect" width="60px" height="20px" class="q-mb-xs" />
                <q-skeleton type="text" width="35px" />
              </div>
            </q-item-section>
          </q-item>
        </div>
      </div>
    </div>

    <!-- Lista de Chats -->
    <div v-if="!loading" class="chat-list-container">
      <!-- Vazio -->
      <div v-if="filteredChats.length === 0" class="empty-state text-center q-py-xl">
        <q-icon name="chat_bubble_outline" size="4em" color="grey-4" />
        <div class="text-h6 text-grey-6 q-mt-md">
          {{ searchText ? 'Nenhum chat encontrado' : 'Você ainda não tem chats' }}
        </div>
        <div class="text-caption text-grey-5">
          {{ searchText ? 'Tente buscar com outros termos' : 'Solicite orçamentos para começar conversas com empresas' }}
        </div>
      </div>

      <!-- Lista com Scroll -->
      <div v-else class="chat-list-scroll">
        <q-item 
          v-for="chat in filteredChats" 
          :key="chat.budget_uuid"
          clickable
          @click="openChat(chat)"
          class="chat-item"
          :class="{ 'has-unread': chat.unread_count > 0 }"
          style="margin-bottom: 12px;"
        >
          <q-item-section avatar>
            <q-avatar color="primary" text-color="white" size="48px">
              <q-icon name="business" />
              <!-- Indicador de mensagem não lida -->
              <q-badge 
                v-if="chat.unread_count > 0" 
                color="negative" 
                floating
                rounded
                class="unread-badge"
              >
                {{ chat.unread_count > 99 ? '99+' : chat.unread_count }}
              </q-badge>
            </q-avatar>
          </q-item-section>

          <q-item-section>
            <q-item-label class="text-weight-medium chat-title text-primary">
              {{ chat.company_name || 'Empresa' }}
              <!-- Ponto verde para indicar não lida -->
              <q-icon 
                v-if="chat.unread_count > 0" 
                name="fiber_manual_record" 
                color="positive" 
                size="8px"
                class="q-ml-xs unread-dot"
              />
            </q-item-label>
            <q-item-label caption class="text-grey-6">
              Projeto: {{ chat.identifier }}
            </q-item-label>
            <q-item-label 
              caption 
              v-if="chat.last_message && chat.last_message !== 'Última mensagem...'" 
              class="last-message"
              :class="{ 'text-weight-bold': chat.unread_count > 0 }"
            >
              {{ formatLastMessage(chat.last_message) }}
            </q-item-label>
          </q-item-section>

          <q-item-section side class="chat-side">
            <div class="column items-end">
              <q-chip 
                :color="getStatusColor(chat.status)" 
                text-color="white" 
                size="sm"
                class="q-mb-xs status-chip"
              >
                {{ getStatusLabel(chat.status) }}
              </q-chip>
              
              <div v-if="chat.last_message_time" class="text-caption text-grey-5 time-label">
                {{ formatTime(chat.last_message_time) }}
              </div>
            </div>
          </q-item-section>
        </q-item>
      </div>
    </div>
  </q-page>
</template>

<script>
import { io } from 'socket.io-client';
import { useQuasar } from 'quasar';

export default {
  name: 'ClientChatList',
  setup() {
    const $q = useQuasar();
    return { $q };
  },
  data() {
    return {
      loading: true,
      searchText: '',
      chats: [],
      socket: null,
      showOnlyUnread: false,
    };
  },
  computed: {
    filteredChats() {
      let filtered = [...this.chats];
      
      // Filtro por texto de busca
      if (this.searchText) {
        const search = this.searchText.toLowerCase();
        filtered = filtered.filter(chat => 
          (chat.company_name || '').toLowerCase().includes(search) ||
          (chat.identifier || '').toLowerCase().includes(search) ||
          (chat.last_message || '').toLowerCase().includes(search)
        );
      }
      
      // Filtro por não lidas
      if (this.showOnlyUnread) {
        filtered = filtered.filter(chat => chat.unread_count > 0);
      }
      
      // Ordenação simplificada: 
      // 1. Priorizar chats com mensagens não lidas
      // 2. Depois ordenar por tempo (mais recentes primeiro)
      filtered.sort((a, b) => {
        // Primeiro critério: mensagens não lidas (não lidas vão para o topo)
        const unreadA = a.unread_count || 0;
        const unreadB = b.unread_count || 0;
        
        if (unreadA > 0 && unreadB === 0) return -1; // A tem não lidas, B não
        if (unreadA === 0 && unreadB > 0) return 1;  // B tem não lidas, A não
        
        // Segundo critério: ordenar por tempo (mais recentes primeiro)
        const timeA = new Date(a.last_message_time);
        const timeB = new Date(b.last_message_time);
        
        return timeB - timeA; // Sempre mais recentes primeiro
      });
      
      return filtered;
    },
    
    unreadCount() {
      return this.chats.filter(chat => chat.unread_count > 0).length;
    }
  },
  mounted() {
    this.initializeSocket();
    this.loadClientChats();
    
    // Debug das alturas após carregar
    setTimeout(() => {
      this.debugHeights();
    }, 1000);
  },
  beforeUnmount() {
    if (this.socket) {
      this.socket.disconnect();
    }
  },
  methods: {
    initializeSocket() {
      this.socket = io('http://localhost:3000', {
        rejectUnauthorized: false,
        timeout: 10000,
      });

      this.socket.on('connect', () => {
        console.log('✅ Cliente conectado para lista de chats');
      });

      this.socket.on('disconnect', () => {
        console.log('❌ Cliente desconectado da lista de chats');
      });

              // Escutar atualizações de chats do cliente
        this.socket.on('client_chats', (chats) => {
          
          // Usar dados reais do servidor (sem simulação)
          this.chats = chats || [];
          this.loading = false;
        });

        // Escutar atualizações em tempo real da lista de chats
        this.socket.on('chat_list_update', (updateData) => {
          
          const userUuid = localStorage.getItem('uuid');
          const { budget_uuid, last_message, last_message_time, sender_uuid } = updateData;
          
          // Encontrar e atualizar o chat correspondente
          const chatIndex = this.chats.findIndex(chat => chat.budget_uuid === budget_uuid);
          
          if (chatIndex !== -1) {
            // Atualizar última mensagem
            this.chats[chatIndex].last_message = last_message;
            this.chats[chatIndex].last_message_time = last_message_time;
            
            // Incrementar contador apenas se a mensagem não é do próprio usuário
            if (sender_uuid !== userUuid) {
              this.chats[chatIndex].unread_count = (this.chats[chatIndex].unread_count || 0) + 1;
              
              // Forçar reordenação da lista (chats com mensagens não lidas vão para o topo)
              this.$nextTick(() => {
                // A computed property filteredChats irá reordenar automaticamente
                this.$forceUpdate();
              });
              
              // Mostrar notificação de nova mensagem
              this.$q.notify({
                type: 'info',
                message: `Nova mensagem em ${this.chats[chatIndex].identifier}`,
                timeout: 3000,
                position: 'top',
                actions: [
                  {
                    label: 'Abrir',
                    color: 'white',
                    handler: () => {
                      this.openChat(this.chats[chatIndex]);
                    }
                  }
                ]
              });
            }
          }
        });

        // Escutar erros do servidor
        this.socket.on('error', (error) => {
          console.error('❌ Erro do servidor:', error);
          this.loading = false;
          this.$q.notify({
            type: 'negative',
            message: error.message || 'Erro do servidor',
            timeout: 3000
          });
        });
    },

    loadClientChats() {
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
      
      // Timeout para parar o loading se não receber resposta
      setTimeout(() => {
        if (this.loading) {
          this.loading = false;
          this.$q.notify({
            type: 'warning',
            message: 'Tempo limite atingido. Tente novamente.',
            timeout: 3000
          });
        }
      }, 10000);
      
      // Emitir evento para carregar chats do cliente
      this.socket.emit('load_client_chats', {
        access_token: access_token
      });
    },

    openChat(chat) {
      
      // Zerar contador de não lidas localmente (será atualizado pelo servidor quando carregar o histórico)
      const chatIndex = this.chats.findIndex(c => c.budget_uuid === chat.budget_uuid);
      if (chatIndex !== -1) {
        this.chats[chatIndex].unread_count = 0;
      }
      
      // Determinar qual ID usar como ticket do chat
      let chatTicketId = chat.budget_uuid;
      
      // Se é um projeto sem budget, usar o project UUID
      if (chat.budget_uuid && chat.budget_uuid.startsWith('no-budget-')) {
        chatTicketId = chat.budget_uuid.replace('no-budget-', '');
      } else if (!chat.budget_uuid) {
        // Se budget_uuid é null, usar project_uuid
        chatTicketId = chat.project_uuid;
      }
      
      // Navegar para a página de chat específica
      this.$router.push({
        path: `/user/chat/${chatTicketId}`,
        query: {
          identifier: chat.identifier,
          company_name: chat.company_name,
          project_name: chat.project_name || chat.identifier
        }
      });
    },

    requestNewBudget() {
      // Navegar para página de solicitação de orçamento
      this.$router.push('/user/projetos');
    },
    

    
    // Métodos de teste
    simulateNewMessage() {
      if (this.chats.length === 0) return;
      
      // Selecionar um chat aleatório
      const randomIndex = Math.floor(Math.random() * this.chats.length);
      const randomMessages = [
        'Nova proposta disponível!',
        'Documentos atualizados',
        'Pagamento processado',
        'Reunião agendada para amanhã',
        'Por favor, confirme os dados'
      ];
      
      this.chats[randomIndex].unread_count = (this.chats[randomIndex].unread_count || 0) + 1;
      this.chats[randomIndex].last_message = randomMessages[Math.floor(Math.random() * randomMessages.length)];
      this.chats[randomIndex].last_message_time = new Date().toISOString();
      
      this.$q.notify({
        type: 'info',
        message: `Nova mensagem simulada em ${this.chats[randomIndex].identifier}`,
        timeout: 2000,
        position: 'top'
      });
    },
    
    clearUnreadMessages() {
      this.chats.forEach(chat => {
        chat.unread_count = 0;
      });
      
      this.$q.notify({
        type: 'positive',
        message: 'Todas as mensagens marcadas como lidas',
        timeout: 1500,
        position: 'top'
      });
    },

    getStatusColor(status) {
      const colors = {
        'pending': 'orange',        // ATENDIMENTO/Aguardando - laranja
        'active': 'blue',          // ORÇAMENTAÇÃO - azul para ativo
        'completed': 'positive',   // ACEITO - verde para concluído
        'cancelled': 'negative'    // CANCELADO - vermelho
      };
      return colors[status] || 'grey';
    },

    getStatusLabel(status) {
      const labels = {
        'pending': 'Aguardando',      // Projeto sem orçamento ou em atendimento
        'active': 'Orçamentação',     // Proposta enviada ou em orçamentação
        'completed': 'Aceito',        // Projeto aceito
        'cancelled': 'Cancelado'      // Projeto cancelado
      };
      return labels[status] || 'Indefinido';
    },

    formatLastMessage(message) {
      if (!message) return '';
      
      // Limitar tamanho da mensagem
      const maxLength = 50;
      return message.length > maxLength 
        ? message.substring(0, maxLength) + '...'
        : message;
    },

    formatTime(timestamp) {
      if (!timestamp) return '';
      
      const date = new Date(timestamp);
      const now = new Date();
      const diffMs = now - date;
      const diffHours = diffMs / (1000 * 60 * 60);
      const diffDays = diffMs / (1000 * 60 * 60 * 24);

      if (diffHours < 1) {
        const diffMinutes = Math.floor(diffMs / (1000 * 60));
        return diffMinutes <= 1 ? 'agora' : `${diffMinutes}m`;
      } else if (diffHours < 24) {
        return `${Math.floor(diffHours)}h`;
      } else if (diffDays < 7) {
        return `${Math.floor(diffDays)}d`;
      } else {
        return date.toLocaleDateString('pt-BR', { 
          day: '2-digit', 
          month: '2-digit' 
        });
      }
    },
    
    // Debug para verificar alturas
    debugHeights() {
      this.$nextTick(() => {
        const page = document.querySelector('.client-chat-list-page');
        const header = document.querySelector('.page-header');
        const search = document.querySelector('.search-section');
        const scroll = document.querySelector('.chat-list-scroll');
        const container = document.querySelector('.chat-list-container');
      });
    },
    
    // Teste com diferentes quantidades de chats
    testLayoutWithManyChats() {
      const mockChats = [];
      for (let i = 0; i < 20; i++) {
        mockChats.push({
          budget_uuid: `test-${i}`,
          company_name: `Empresa Test ${i}`,
          identifier: `TEST${i}`,
          unread_count: i % 3 === 0 ? Math.floor(Math.random() * 5) + 1 : 0,
          last_message: `Mensagem de teste ${i}`,
          last_message_time: new Date().toISOString(),
          status: 'active'
        });
      }
      this.chats = mockChats;
      setTimeout(() => this.debugHeights(), 500);
    },
    
    // Gerar larguras variadas para os skeletons
    getSkeletonWidth(type, index) {
      const widths = {
        company: ['45%', '60%', '55%', '70%', '50%', '65%'],
        project: ['35%', '40%', '45%', '38%', '42%', '36%'],
        message: ['75%', '85%', '65%', '90%', '70%', '80%']
      };
      
      return widths[type][index - 1] || widths[type][0];
    }
  }
};
</script>

<style scoped lang="scss">
.client-chat-list-page {
  background: #f5f5f5;
  min-height: calc(100vh - 60px); // Altura mínima subtraindo footer das tabs
  height: calc(100vh - 60px); // Altura fixa subtraindo footer das tabs
  display: flex;
  flex-direction: column;
  overflow: hidden; // Evita scroll na página inteira
}

.page-header {
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  flex-shrink: 0;
}

.search-section {
  background: white;
  border-bottom: 1px solid #e0e0e0;
  flex-shrink: 0;
  
  .search-input {
    .q-field__control {
      border-radius: 12px;
    }
  }
  
  .filter-chips {
    display: flex;
    gap: 8px;
  }
  
  .unread-filter-chip {
    transition: all 0.3s ease;
    
    &.has-unread-messages {
      animation: chipPulse 3s infinite;
    }
    
    .filter-badge {
      font-size: 0.7rem;
      min-width: 16px;
      height: 16px;
      font-weight: bold;
    }
  }
  

  
  .search-results-count {
    font-size: 0.7rem;
    margin-left: 8px;
  }
}

.chat-list-container {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  min-height: 0; // Importante para o flex funcionar
}

.chat-list-scroll {
  flex: 1;
  overflow-y: auto;
  padding: 16px;
  padding-bottom: 24px; // Espaço extra no final para o último item
  height: 0; // Força o flex a calcular a altura corretamente
  scroll-behavior: smooth; // Scroll suave
  
  // Estilo personalizado da scrollbar
  &::-webkit-scrollbar {
    width: 6px;
  }
  
  &::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
  }
  
  &::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
    
    &:hover {
      background: #a8a8a8;
    }
  }
  
  // Para Firefox
  scrollbar-width: thin;
  scrollbar-color: #c1c1c1 #f1f1f1;
}

.chat-item {
  background: white;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e0e0e0;
  position: relative;
  transition: all 0.2s ease;
  
  &:hover {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    transform: translateY(-1px);
  }
  
  // Destaque para chats com mensagens não lidas
  &.has-unread {
    border-left: 4px solid #ff6b6b;
    background: linear-gradient(to right, #fff5f5, white);
    
    .chat-title {
      color: #2c3e50;
    }
    
    .last-message {
      color: #555;
    }
  }
}

// Estilos para os skeletons
.chat-skeleton {
  .chat-item {
    // Remover hover effect nos skeletons
    &:hover {
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
      transform: none;
    }
    
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
        rgba(255, 255, 255, 0.4), 
        transparent
      );
      animation: shimmer 1.5s infinite;
    }
  }
  
  // Posicionamento do skeleton do badge
  .skeleton-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    border-radius: 50%;
    z-index: 10;
  }
}

.unread-badge {
  font-size: 0.7rem;
  min-width: 20px;
  height: 20px;
  font-weight: bold;
  box-shadow: 0 2px 4px rgba(244, 67, 54, 0.3);
  animation: badgePulse 2s infinite;
  
  &.q-badge--floating {
    top: -4px;
    right: -4px;
    z-index: 10;
  }
}

.unread-dot {
  animation: pulse 2s infinite;
}

.chat-title {
  display: flex;
  align-items: center;
  
  .unread-dot {
    flex-shrink: 0;
  }
}

.last-message {
  font-size: 0.85rem;
  line-height: 1.3;
  max-width: 200px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.chat-side {
  min-width: 80px;
  
  .status-chip {
    font-size: 0.7rem;
    height: 20px;
  }
  
  .time-label {
    font-size: 0.75rem;
    white-space: nowrap;
  }
}

.empty-state {
  background: white;
  border-radius: 12px;
  margin: 16px;
  padding: 32px 16px;
  flex-shrink: 0;
}

// Animações
@keyframes pulse {
  0% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
  100% {
    opacity: 1;
  }
}

@keyframes badgePulse {
  0% {
    transform: scale(1);
    box-shadow: 0 2px 4px rgba(244, 67, 54, 0.3);
  }
  50% {
    transform: scale(1.1);
    box-shadow: 0 3px 8px rgba(244, 67, 54, 0.5);
  }
  100% {
    transform: scale(1);
    box-shadow: 0 2px 4px rgba(244, 67, 54, 0.3);
  }
}

@keyframes chipPulse {
  0% {
    box-shadow: 0 1px 3px rgba(255, 152, 0, 0.3);
  }
  50% {
    box-shadow: 0 2px 8px rgba(255, 152, 0, 0.6);
  }
  100% {
    box-shadow: 0 1px 3px rgba(255, 152, 0, 0.3);
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

// Responsividade
@media (max-width: 480px) {
  .client-chat-list-page {
    min-height: calc(100vh - 70px); // Altura mínima ajustada para mobile
    height: calc(100vh - 70px); // Altura fixa ajustada para mobile
  }
  
  .chat-item {
    margin-bottom: 8px;
    
    .last-message {
      max-width: 150px;
    }
  }
  
  .search-section {
    padding: 12px;
  }
  
  .chat-list-scroll {
    padding: 12px;
    padding-bottom: 32px; // Mais espaço no mobile
    // Removendo max-height também no mobile
  }
}
</style>
