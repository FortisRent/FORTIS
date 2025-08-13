<template>
  <q-page class="chat-dashboard-container">
    <!-- Coluna 01 - Lista de Chats -->
    <div class="chat-list-column">
      <!-- Header da lista -->
            <div class="chat-list-header">
        <div class="header-title">
          <q-icon name="chat" size="24px" color="primary" />
          <span class="text-h6 text-primary text-weight-bold">Chats</span>
          <q-chip 
            v-if="filteredChats.length > 0"
            :label="filteredChats.length" 
            color="primary" 
            text-color="white" 
            size="sm"
          />
        </div>
      </div>

      <!-- Busca -->
      <div class="search-section">
        <q-input
          v-model="searchText"
          outlined
          dense
          placeholder="Buscar por cliente ou projeto..."
          clearable
          class="search-input"
        >
          <template v-slot:prepend>
            <q-icon name="search" color="grey-6" />
          </template>
        </q-input>
      </div>

      <!-- Filtros por status -->
      <div class="filters-section">
        <q-chip
          v-for="status in projectStatuses"
          :key="status.value"
          v-model:selected="status.selected"
          clickable
          :color="status.selected ? status.color : 'grey-4'"
          :text-color="status.selected ? 'white' : 'grey-8'"
          :icon="status.icon"
          size="sm"
          class="q-mr-xs q-mb-xs"
        >
          {{ status.label }}
          <q-badge 
            v-if="getStatusCount(status.value) > 0" 
            :color="status.selected ? 'white' : status.color" 
            :text-color="status.selected ? status.color : 'white'"
            rounded
            class="q-ml-xs"
          >
            {{ getStatusCount(status.value) }}
          </q-badge>
        </q-chip>
      </div>

      <!-- Lista de chats -->
      <div class="chat-list-scroll">
        <!-- Loading -->
        <div v-if="loading" class="chat-list-loading">
          <!-- Skeletons para chats -->
          <div v-for="n in 6" :key="`skeleton-${n}`" class="chat-skeleton-item">
            <q-item class="chat-item-skeleton">
              <q-item-section avatar>
                <q-skeleton type="QAvatar" size="48px" />
              </q-item-section>
              <q-item-section>
                <q-item-label>
                  <q-skeleton type="text" :width="getSkeletonWidth('client', n)" />
                </q-item-label>
                <q-item-label caption>
                  <q-skeleton type="text" :width="getSkeletonWidth('project', n)" />
                </q-item-label>
                <q-item-label caption>
                  <q-skeleton type="text" :width="getSkeletonWidth('message', n)" />
                </q-item-label>
              </q-item-section>
              <q-item-section side>
                <q-skeleton type="rect" width="60px" height="20px" />
              </q-item-section>
            </q-item>
          </div>
        </div>

        <!-- Lista real -->
        <q-list v-else separator>
          <q-item
            v-for="chat in filteredChats"
            :key="chat.budget_uuid"
            clickable
            @click="selectChat(chat)"
            :class="{ 'chat-selected': selectedChat?.budget_uuid === chat.budget_uuid }"
            class="chat-item"
          >
            <!-- Avatar do cliente -->
            <q-item-section avatar>
              <q-avatar size="48px">
                <img 
                  v-if="chat.client_avatar" 
                  :src="chat.client_avatar" 
                  @error="setDefaultAvatar"
                />
                <q-icon v-else name="person" size="24px" color="grey-6" />
                
                <!-- Badge de mensagens não lidas -->
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

            <!-- Informações do chat -->
            <q-item-section>
              <q-item-label class="chat-client-name text-primary">
                {{ chat.client_name || 'Cliente' }}
                <q-icon 
                  v-if="chat.unread_count > 0" 
                  name="fiber_manual_record" 
                  color="positive" 
                  size="8px"
                  class="q-ml-xs unread-dot"
                />
              </q-item-label>
              <q-item-label caption class="chat-project-info">
                Projeto: {{ chat.project_identifier || chat.budget_uuid }}
              </q-item-label>
              <q-item-label 
                caption 
                v-if="chat.last_message" 
                class="chat-last-message"
                :class="{ 'text-weight-bold': chat.unread_count > 0 }"
              >
                {{ formatLastMessage(chat.last_message) }}
              </q-item-label>
            </q-item-section>

            <!-- Status e tempo -->
            <q-item-section side class="chat-side">
              <div class="column items-end">
                <q-chip 
                  :color="getProjectStatusColor(chat.status)" 
                  text-color="white" 
                  size="sm"
                  class="q-mb-xs status-chip"
                >
                  {{ getProjectStatusLabel(chat.status) }}
                </q-chip>
                
                <div v-if="chat.last_message_time" class="text-caption text-grey-5">
                  {{ formatTime(chat.last_message_time) }}
                </div>
              </div>
            </q-item-section>
          </q-item>
        </q-list>

        <!-- Estado vazio -->
        <div v-if="!loading && filteredChats.length === 0" class="empty-state">
          <q-icon name="chat_bubble_outline" size="4em" color="grey-4" />
          <div class="text-h6 text-grey-6 q-mt-md">
            {{ searchText ? 'Nenhum chat encontrado' : 'Nenhum chat disponível' }}
          </div>
          <div class="text-caption text-grey-5">
            {{ searchText ? 'Tente buscar com outros termos' : 'Aguardando novos projetos' }}
          </div>
        </div>
      </div>
    </div>

    <!-- Coluna 02 - Área de Chat -->
    <div class="chat-area-column">
      <!-- Header do chat ativo -->
      <div v-if="selectedChat" class="chat-header">
        <div class="chat-header-info">
          <q-avatar size="40px" class="q-mr-md">
            <img 
              v-if="selectedChat.client_avatar" 
              :src="selectedChat.client_avatar"
              @error="setDefaultAvatar"
            />
            <q-icon v-else name="person" size="20px" color="grey-6" />
          </q-avatar>
          <div class="flex-grow">
            <div class="text-subtitle1 text-weight-medium">
              {{ selectedChat.client_name || 'Cliente' }}
            </div>
            <div class="text-caption text-grey-6">
              Projeto: {{ selectedChat.project_identifier || selectedChat.budget_uuid }}
            </div>
          </div>
        </div>
        <div class="chat-header-actions">
          <div class="column items-end q-gutter-xs">
            <q-chip 
              :color="getProjectStatusColor(selectedChat.status)" 
              text-color="white" 
              size="sm"
            >
              {{ getProjectStatusLabel(selectedChat.status) }}
            </q-chip>
            <div v-if="selectedChat.created_at" class="text-caption text-grey-5">
              Criado: {{ formatDate(selectedChat.created_at) }}
            </div>
          </div>
        </div>
      </div>

      <!-- Área de mensagens -->
      <div class="chat-messages-area">
        <TicketMessageList v-if="selectedChat" />
        <div v-else class="no-chat-selected">
          <q-icon name="chat" size="4rem" color="grey-5" />
          <p class="text-grey-5 text-h6 q-mt-md">Selecione um chat para começar</p>
        </div>
      </div>

      <!-- Input de mensagem -->
      <div v-if="selectedChat" class="chat-input-area">
      <AnswerTextArea />
      </div>
    </div>


  </q-page>
</template>

<script>
import TicketMessageList from '../components/TicketMessageList.vue';
import AnswerTextArea from '../components/AnswerTextArea.vue';
import { useQuasar } from 'quasar';
import { useTicketStore } from '../stores/ticket-store';

export default {
  name: 'ChatDashboard',
  setup() {
    const $q = useQuasar();
    const ticketStore = useTicketStore();
    return { $q, ticketStore };
  },
  data() {
    return {
      loading: true,
      searchText: '',
      chats: [],
      selectedChat: null,
      socket: null,
      projectStatuses: [
        {
          label: 'Todos',
          value: 'all',
          color: 'primary',
          icon: 'list',
          selected: true
        },
        {
          label: 'Novo',
          value: 'pending',
          color: 'orange',
          icon: 'new_releases',
          selected: false
        },
        {
          label: 'Orçamentado',
          value: 'active',
          color: 'blue',
          icon: 'assignment',
          selected: false
        },
        {
          label: 'Aceito',
          value: 'completed',
          color: 'positive',
          icon: 'check_circle',
          selected: false
        },
        {
          label: 'Cancelado',
          value: 'cancelled',
          color: 'negative',
          icon: 'cancel',
          selected: false
        }
      ]
    };
  },
  computed: {
    isMobile() {
      return this.$q.screen.lt.md;
    },
    
    filteredChats() {
      let filtered = [...this.chats];
      
      // Filtro por texto de busca
      if (this.searchText) {
        const search = this.searchText.toLowerCase();
        filtered = filtered.filter(chat => 
          (chat.client_name || '').toLowerCase().includes(search) ||
          (chat.project_identifier || '').toLowerCase().includes(search) ||
          (chat.budget_uuid || '').toLowerCase().includes(search) ||
          (chat.last_message || '').toLowerCase().includes(search)
        );
      }
      
      // Filtro por status
      const selectedStatuses = this.projectStatuses
        .filter(status => status.selected && status.value !== 'all')
        .map(status => status.value);
        
      if (selectedStatuses.length > 0) {
        filtered = filtered.filter(chat => selectedStatuses.includes(chat.status));
      }
      
      // Ordenação: não lidas primeiro, depois por tempo
      filtered.sort((a, b) => {
        const unreadA = a.unread_count || 0;
        const unreadB = b.unread_count || 0;
        
        if (unreadA > 0 && unreadB === 0) return -1;
        if (unreadA === 0 && unreadB > 0) return 1;
        
        const timeA = new Date(a.last_message_time || a.created_at);
        const timeB = new Date(b.last_message_time || b.created_at);
        
        return timeB - timeA;
      });
      
      return filtered;
    }
  },
  mounted() {
    this.loadChats();
  },
  beforeUnmount() {
    if (this.socket) {
      this.socket.disconnect();
    }
  },
  methods: {
    async loadChats() {
      try {
        this.loading = true;
        
        // Obter company_uuid do localStorage ou rota
        const company_uuid = localStorage.getItem('company_uuid') || this.$route.params.company_uuid;
        
        if (!company_uuid) {
          throw new Error('Company UUID não encontrado');
        }
        
        const access_token = localStorage.getItem('access_token');
        if (!access_token) {
          throw new Error('Token de acesso não encontrado');
        }
        
        // Carregar projetos da empresa via API
        const response = await fetch(`http://localhost:5510/v1/budget/company/${company_uuid}`, {
          headers: { 
            'token': access_token,
            'Content-Type': 'application/json'
          }
        });

        if (!response.ok) {
          throw new Error('Erro ao carregar projetos da empresa');
        }

        const data = await response.json();
        
        // Mapear dados da API para formato do chat
        this.chats = (data.company_budget || []).map(budget => ({
          budget_uuid: budget.budget_uuid,
          client_name: budget.client_name || 'Cliente',
          client_email: budget.contact || '',
          client_phone: budget.contact || '',
          client_avatar: null,
          project_identifier: budget.identifier,
          status: this.mapStatus(budget.status_name),
          last_message: 'Nenhuma mensagem ainda',
          last_message_time: budget.created_at || new Date().toISOString(),
          unread_count: 0,
          created_at: budget.created_at,
          budget_value: null,
          project_name: budget.project_name
        }));
        
        // Conectar ao socket para dados em tempo real
        this.initializeSocket();
        
        // Carregar contadores de mensagens não lidas
        this.loadUnreadCounts();
        
      } catch (error) {
        console.error('❌ Erro ao carregar chats:', error);
        this.$q.notify({
          type: 'negative',
          message: error.message || 'Erro ao carregar os chats',
          timeout: 3000
        });
      } finally {
        this.loading = false;
      }
    },
    
    mapStatus(statusName) {
      // Mapear status da API para status internos baseado nos status reais da aplicação
      const statusMap = {
        'ATENDIMENTO': 'pending',     // Novo orçamento - pendente
        'ORÇAMENTAÇÃO': 'active',     // Orçamento já enviado - ativo
        'ACEITO': 'completed',        // Aceito - completo/agendado
        'RECUSADO': 'cancelled',      // Recusado - cancelado
        'CANCELADO': 'cancelled',     // Cancelado - cancelado
        'PAGO': 'completed',          // Pago - completo
        'Concluído': 'completed'      // Concluído - completo
      };
      
      return statusMap[statusName] || 'pending';
    },
    
    initializeSocket() {
      // Importar Socket.IO
      import('socket.io-client').then(({ io }) => {
        this.socket = io('http://localhost:3000', {
          rejectUnauthorized: false,
          timeout: 10000,
        });

        this.socket.on('connect', () => {
          console.log('✅ Empresa conectada para chat dashboard');
        });

        this.socket.on('disconnect', () => {
          console.log('❌ Empresa desconectada do chat dashboard');
        });

        // Escutar resposta dos chats da empresa
        this.socket.on('company_chats', (chats) => {
          console.log('📋 Chats da empresa recebidos:', chats);
          
          // Atualizar contadores de mensagens não lidas
          chats.forEach(companyChat => {
            const chatIndex = this.chats.findIndex(chat => chat.budget_uuid === companyChat.budget_uuid);
            if (chatIndex !== -1) {
              this.chats[chatIndex].unread_count = companyChat.unread_count;
              this.chats[chatIndex].last_message = companyChat.last_message;
              this.chats[chatIndex].last_message_time = companyChat.last_message_time;
            }
          });
          
          this.$forceUpdate();
        });

        // Escutar atualizações em tempo real da lista de chats
        this.socket.on('chat_list_update', (updateData) => {
          console.log('🔄 Atualização da lista de chats empresa:', updateData);
          
          const { budget_uuid, last_message, last_message_time, sender_uuid } = updateData;
          
          // Obter UUID do usuário atual (empresa)
          const currentUserUuid = localStorage.getItem('uuid');
          
          // Encontrar e atualizar o chat correspondente
          const chatIndex = this.chats.findIndex(chat => chat.budget_uuid === budget_uuid);
          
          if (chatIndex !== -1) {
            // Atualizar última mensagem
            this.chats[chatIndex].last_message = last_message;
            this.chats[chatIndex].last_message_time = last_message_time;
            
            // Incrementar contador apenas se a mensagem não é da própria empresa
            if (sender_uuid !== currentUserUuid) {
              this.chats[chatIndex].unread_count = (this.chats[chatIndex].unread_count || 0) + 1;
              
              // Mostrar notificação de nova mensagem
              this.$q.notify({
                type: 'info',
                message: `Nova mensagem de cliente no projeto ${this.chats[chatIndex].project_identifier}`,
                timeout: 3000,
                position: 'top-right',
                actions: [
                  {
                    label: 'Ver',
                    color: 'white',
                    handler: () => {
                      this.selectChat(this.chats[chatIndex]);
                    }
                  }
                ]
              });
            }
            
            // Forçar reordenação da lista
            this.$nextTick(() => {
              this.$forceUpdate();
            });
          }
          
          console.log(`📨 Chat ${budget_uuid} atualizado pela empresa`);
        });

        // Escutar erros do servidor
        this.socket.on('error', (error) => {
          console.error('❌ Erro do servidor:', error);
          this.$q.notify({
            type: 'negative',
            message: error.message || 'Erro do servidor',
            timeout: 3000
          });
        });
      });
    },
    
    async loadUnreadCounts() {
      // Carregar contadores de mensagens não lidas via socket
      if (!this.socket || this.chats.length === 0) return;
      
      try {
        const company_uuid = localStorage.getItem('company_uuid') || this.$route.params.company_uuid;
        const access_token = localStorage.getItem('access_token');
        
        if (!company_uuid || !access_token) {
          console.warn('⚠️ Company UUID ou token não encontrado para carregar contadores');
          return;
        }
        
        console.log('🔄 Solicitando contadores de mensagens não lidas via socket...');
        
        // Solicitar chats da empresa com contadores
        this.socket.emit('load_company_chats', {
          access_token: access_token,
          company_uuid: company_uuid
        });
        
      } catch (error) {
        console.error('❌ Erro ao carregar contadores não lidas:', error);
      }
    },
    
    selectChat(chat) {
      this.selectedChat = chat;
      
      // Zerar mensagens não lidas
      if (chat.unread_count > 0) {
        const chatIndex = this.chats.findIndex(c => c.budget_uuid === chat.budget_uuid);
        if (chatIndex !== -1) {
          this.chats[chatIndex].unread_count = 0;
        }
      }
      
      // Atualizar o store do ticket para que os componentes de chat funcionem
      this.ticketStore.setTicket(chat.budget_uuid);
      
      // Limpar mensagens anteriores quando trocar de chat
      this.ticketStore.messages = [];
    },
    
    getStatusCount(statusValue) {
      if (statusValue === 'all') {
        return this.chats.length;
      }
      return this.chats.filter(chat => chat.status === statusValue).length;
    },
    
    getProjectStatusColor(status) {
      const colors = {
        'pending': 'orange',        // ATENDIMENTO - laranja para novo
        'active': 'blue',          // ORÇAMENTAÇÃO - azul para ativo
        'completed': 'positive',   // ACEITO/PAGO - verde para concluído
        'cancelled': 'negative'    // RECUSADO/CANCELADO - vermelho
      };
      return colors[status] || 'grey';
    },
    
    getProjectStatusLabel(status) {
      const labels = {
        'pending': 'Atendimento',          // ATENDIMENTO
        'active': 'Orçamentação',    // ORÇAMENTAÇÃO  
        'completed': 'Concluído',      // ACEITO/PAGO/Concluído
        'cancelled': 'Cancelado'    // RECUSADO/CANCELADO
      };
      return labels[status] || 'Indefinido';
    },
    
    formatLastMessage(message) {
      if (!message) return '';
      const maxLength = 40;
      return message.length > maxLength 
        ? message.substring(0, maxLength) + '...'
        : message;
    },
    
    formatTime(timestamp) {
      if (!timestamp) return '';
      
      // Converter string brasileira DD/MM/YYYY HH:mm:ss para Date
      let date;
      if (typeof timestamp === 'string' && timestamp.includes('/')) {
        // Formato DD/MM/YYYY HH:mm:ss
        const [datePart, timePart] = timestamp.split(' ');
        const [day, month, year] = datePart.split('/');
        const dateStr = `${month}/${day}/${year}`;
        date = timePart ? new Date(`${dateStr} ${timePart}`) : new Date(dateStr);
      } else {
        date = new Date(timestamp);
      }
      
      if (isNaN(date.getTime())) {
        console.warn('Data inválida:', timestamp);
        return '';
      }
      
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
    
    formatDate(dateString) {
      if (!dateString) return '';
      
      // Converter string brasileira DD/MM/YYYY HH:mm:ss para Date
      let date;
      if (typeof dateString === 'string' && dateString.includes('/')) {
        // Formato DD/MM/YYYY HH:mm:ss
        const [datePart, timePart] = dateString.split(' ');
        const [day, month, year] = datePart.split('/');
        const dateStr = `${month}/${day}/${year}`;
        date = timePart ? new Date(`${dateStr} ${timePart}`) : new Date(dateStr);
      } else {
        date = new Date(dateString);
      }
      
      if (isNaN(date.getTime())) {
        console.warn('Data inválida:', dateString);
        return 'Data inválida';
      }
      
      return date.toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      });
    },
    
    formatCurrency(value) {
      if (!value) return 'R$ 0,00';
      return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
      }).format(value);
    },
    
    setDefaultAvatar(event) {
      // Fallback para quando a imagem falha
      event.target.style.display = 'none';
    },
    
    // Métodos para skeletons
    getSkeletonWidth(type, index) {
      const widths = {
        client: ['60%', '75%', '65%', '80%', '55%', '70%'],
        project: ['45%', '50%', '40%', '55%', '48%', '42%'],
        message: ['80%', '90%', '70%', '85%', '75%', '88%']
      };
      
      return widths[type][index - 1] || widths[type][0];
    }
  },
  components: {
    TicketMessageList,
    AnswerTextArea,
  },
};
</script>

<style scoped lang="scss">
// ========== LAYOUT PRINCIPAL ==========
.chat-dashboard-container {
  display: grid;
  grid-template-columns: 320px 1fr;
  height: calc(100vh - 80px);
  background: #f5f7fa;
  gap: 1px;
  overflow: hidden;
}

// ========== COLUNA 1 - LISTA DE CHATS ==========
.chat-list-column {
  background: white;
  display: flex;
  flex-direction: column;
  border-right: 1px solid #e0e6ed;
}

.chat-list-header {
  padding: 1rem;
  border-bottom: 1px solid #e0e6ed;
  background: white;
  flex-shrink: 0;
  
  .header-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
  }
  
  .status-indicator {
    display: flex;
    justify-content: center;
  }
}

.search-section {
  padding: 1rem;
  border-bottom: 1px solid #e0e6ed;
  background: white;
  flex-shrink: 0;
  
  .search-input {
    .q-field__control {
      border-radius: 12px;
    }
  }
}

.filters-section {
  padding: 1rem;
  border-bottom: 1px solid #e0e6ed;
  background: white;
  flex-shrink: 0;
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.chat-list-scroll {
  flex: 1;
  overflow-y: auto;
  background: white;
  max-height: calc(100vh - 280px);
  
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
  border-left: 3px solid transparent;
  transition: all 0.2s ease;
  
  &:hover {
    background: #f8f9fa;
  }
  
  &.chat-selected {
    background: #e3f2fd;
    border-left-color: #1976d2;
  }
  
  .chat-client-name {
    font-weight: 600;
    display: flex;
    align-items: center;
  }
  
  .chat-project-info {
    color: #6c757d;
    font-size: 0.875rem;
  }
  
  .chat-last-message {
    color: #495057;
    font-size: 0.875rem;
    margin-top: 0.25rem;
  }
  
  .status-chip {
    font-size: 0.7rem;
    height: 20px;
  }
}

.unread-badge {
  font-size: 0.7rem;
  min-width: 18px;
  height: 18px;
  font-weight: bold;
  animation: pulse 2s infinite;
}

.unread-dot {
  animation: pulse 2s infinite;
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem 1rem;
  text-align: center;
}

// ========== SKELETONS ==========
.chat-list-loading {
  padding: 1rem;
}

.chat-skeleton-item {
  margin-bottom: 1rem;
  
  .chat-item-skeleton {
    border-radius: 8px;
    position: relative;
    overflow: hidden;
    
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
}

// ========== COLUNA 2 - ÁREA DE CHAT ==========
.chat-area-column {
  background: white;
  display: flex;
  flex-direction: column;
  border-right: 1px solid #e0e6ed;
  max-height: calc(100vh - 80px);
}

.chat-header {
  padding: 1rem;
  border-bottom: 1px solid #e0e6ed;
  background: white;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-shrink: 0;
  
  .chat-header-info {
    display: flex;
    align-items: center;
  }
}

.chat-messages-area {
  flex: 1;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  
  .no-chat-selected {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    text-align: center;
  }
}

.chat-input-area {
  border-top: 1px solid #e0e6ed;
  background: white;
  flex-shrink: 0;
}



// ========== ANIMAÇÕES ==========
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

@keyframes shimmer {
  0% {
    left: -100%;
  }
  100% {
    left: 100%;
  }
}



// ========== RESPONSIVIDADE ==========
@media (max-width: 1200px) {
  .chat-dashboard-container {
    grid-template-columns: 280px 1fr;
  }
}

@media (max-width: 1024px) {
  .chat-dashboard-container {
    grid-template-columns: 260px 1fr;
  }
  
  .chat-list-header,
  .search-section,
  .filters-section {
    padding: 0.75rem;
  }
}

// ========== MOBILE ==========
.page-chat-container-mobile {
	font-family: 'Manrope', sans-serif;
}

.chat-header-container {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  gap: 1rem;
  padding: 2rem 1rem 1rem;
}

@media (max-width: 959px) {
  .q-header {
    display: none !important;
  }
}
</style>
