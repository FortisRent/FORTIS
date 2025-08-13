<template>
  <q-scroll-area style="height: 80vh">
    <!-- <q-btn label="Iniciar conversa" v-on:click="create_ticket()" color="primary" class="q-mb-lg q-px-xl" style="width: 100%"/> -->

    <q-item-label header class="text-h6 text-primary" v-if="!isMobile">
      Chats ({{ (filteredBudgets?.length || 0) }})
    </q-item-label>

    <!-- Debug info -->
    <div v-if="!budget_list || budget_list.length === 0" class="debug-info q-pa-md">
      <q-icon name="info" color="orange" size="sm" />
      <span class="text-caption text-grey-7 q-ml-sm">
        Aguardando dados do servidor...
      </span>
      <q-btn 
        flat 
        dense 
        size="sm" 
        icon="refresh" 
        @click="retryLoadBudgets"
        class="q-ml-sm"
        color="primary"
      >
        <q-tooltip>Tentar novamente</q-tooltip>
      </q-btn>
      
      <q-btn 
        flat 
        dense 
        size="sm" 
        icon="filter_list_off" 
        @click="resetFilter"
        class="q-ml-sm"
        color="orange"
      >
        <q-tooltip>Reset Filtro</q-tooltip>
      </q-btn>
    </div>

    <q-list>
      <!-- <TicketItem
				v-for="ticket in ticket_list"
				:key="ticket.id"
				@click="change_ticket(ticket.id)"
				:id="ticket.id"
				:title="ticket.user_name"
				:body="ticket.body"
				:created_at="formatDate(ticket.created_at)"
				:last_interaction="formatDate(ticket.last_interaction)"
				color="blue"
				text_color="white"
				icon="message"
				:quantity="ticket.count"
			/> -->

      <!-- Debug: Mostrar contadores -->
      <div class="q-pa-sm text-caption text-grey-6">
        <div>Debug - budget_list.length: {{ budget_list?.length || 0 }}</div>
        <div>Debug - filteredBudgets.length: {{ filteredBudgets?.length || 0 }}</div>
        <div>Debug - participantFilter: {{ participantFilter ? 'Ativo' : 'Inativo' }}</div>
      </div>

      <!-- Fallback para debug: se houver budgets mas não aparecerem -->
      <div v-if="(filteredBudgets?.length || 0) === 0 && (budget_list?.length || 0) > 0" class="q-pa-md text-center">
        <q-icon name="warning" color="orange" size="2em" />
        <div class="text-caption text-grey-6 q-mt-sm">
          Budgets existem ({{ budget_list?.length || 0 }}) mas filteredBudgets está vazio
        </div>
      </div>

      <!-- TESTE DIRETO: usar budget_list ao invés do computed -->
      <div class="q-pa-sm">
        <div class="text-caption text-red q-mb-sm">🚨 TESTE DIRETO (budget_list): {{ budget_list?.length || 0 }} itens</div>
      </div>

      <BudgetItem
        v-for="budget in (budget_list || [])"
        :key="budget.uuid + '-direct'"
        @click="handleClick(budget.uuid)"
        :uuid="budget.uuid"
        :identifier="budget.identifier"
        :user_name="budget.user_name"
        :client_name="budget.client_name"
        :selected="store.current_ticket === budget.uuid"
        color="orange"
        text_color="white"
        icon="message"
      />

      <!-- Também manter o computed para comparação -->
      <div v-if="false">
        <div class="text-caption text-blue q-mb-sm">📊 COMPUTED (filteredBudgets): {{ filteredBudgets?.length || 0 }} itens</div>
        <BudgetItem
          v-for="budget in (filteredBudgets || [])"
          :key="budget.uuid + '-computed'"
          @click="handleClick(budget.uuid)"
          :uuid="budget.uuid"
          :identifier="budget.identifier"
          :user_name="budget.user_name"
          :client_name="budget.client_name"
          :selected="store.current_ticket === budget.uuid"
          color="blue"
          text_color="white"
          icon="message"
        />
      </div>

      <!-- Teste direto com budget_list para verificar se o problema é o computed -->
      <div v-if="(budget_list?.length || 0) > 0" class="q-pa-sm">
        <div class="text-caption text-orange q-mb-sm">Debug - Lista direta (budget_list):</div>
        <q-item 
          v-for="budget in (budget_list || [])" 
          :key="'debug-' + budget.uuid"
          class="q-pa-xs border"
          style="border: 1px solid #ddd; margin-bottom: 4px;"
        >
          <q-item-section>
            <div class="text-caption">{{ budget.identifier }} - {{ budget.client_name || budget.user_name }}</div>
          </q-item-section>
        </q-item>
      </div>

      <!-- <TicketItem
				@click="change_ticket('TesteFortis')"
				id="TesteFortis"
				title="Sarah"
				body="Oi, eu sou a Sarah"
				created_at="00/00/0000"
				last_interaction="00/00/0000"
				color="blue"
				text_color="white"
				icon="message"
				quantity="5"
			/> -->
    </q-list>
  </q-scroll-area>
</template>

<script>
import BudgetItem from './BudgetItem.vue';
// import TicketItem from './TicketItem.vue';
import { io } from 'socket.io-client';
import { useTicketStore } from 'src/stores/ticket-store';

export default {
  name: 'TicketList',
  emits: ['budgets-loaded'],
  components: {
    // TicketItem,
    BudgetItem,
  },
  props: {
    selectedParticipants: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      socket: null,
      store: useTicketStore(),
      ticket_list: [],
      budget_list: [], // Garantir que sempre seja um array
      participantFilter: null,
    };
  },
  computed: {
    filteredBudgets() {
      console.log('🔍 TicketList - filteredBudgets chamado (versão simplificada):');
      console.log('  - this.budget_list:', this.budget_list);
      console.log('  - this.budget_list tipo:', typeof this.budget_list);
      console.log('  - this.budget_list é array?', Array.isArray(this.budget_list));
      console.log('  - this.budget_list.length:', this.budget_list?.length);
      
      // VERSÃO SUPER SIMPLES: sempre retornar todos os budgets
      const result = Array.isArray(this.budget_list) ? this.budget_list : [];
      
      console.log('  - ✅ Retornando (simples):', result.length);
      console.log('  - Primeiro item:', result[0]);
      
      return result;
    }
  },

  mounted() {
    //this.socket = io("http://147.79.106.39:3000", { rejectUnauthorized: false });
    this.socket = io('http://localhost:3000', { rejectUnauthorized: false });

    // Listen for incoming messages
    this.socket.on('chat_message', (data) => {
      this.store.chatMessage(data);
    });

    // Fetch chat history
    this.socket.on('chat_history', (messages) => {
      this.store.chatHistory(messages);
    });

    // Load tickets for this user
    const access_token = localStorage.getItem('access_token');
    console.log('🔑 Access token:', access_token ? 'Presente' : 'Ausente');
    
    if (access_token) {
      console.log('📤 Enviando load_user_budgets...');
      this.debugToken(access_token);
      this.socket.emit('load_user_budgets', access_token);
    } else {
      this.$q.notify({
        type: 'negative',
        message: 'Token de acesso não encontrado. Faça login novamente.',
        timeout: 3000,
        position: 'top'
      });
    }

    this.socket.on('user_budgets', (budgets) => {
      console.log('📊 TicketList - user_budgets recebido:', budgets);
      console.log('📊 TicketList - budgets é array?', Array.isArray(budgets));
      console.log('📊 TicketList - Total de budgets:', budgets ? budgets.length : 0);
      
      this.budget_list = budgets || [];
      
      console.log('📊 TicketList - budget_list atualizado:', this.budget_list?.length || 0);
      console.log('📊 TicketList - Primeiro budget:', this.budget_list?.[0]);
      
      // Forçar atualização do componente para garantir reatividade
      this.$nextTick(() => {
        console.log('📊 TicketList - Após nextTick, filteredBudgets.length:', this.filteredBudgets?.length || 0);
        this.$forceUpdate();
      });
      
      // Emitir evento para o componente pai
      this.$emit('budgets-loaded', budgets || []);
      
      if (!budgets || (budgets?.length || 0) === 0) {
        this.$q.notify({
          type: 'warning',
          message: 'Nenhum chat encontrado',
          timeout: 2000,
          position: 'top'
        });
      } else {
        this.$q.notify({
          type: 'positive',
          message: `${budgets?.length || 0} chats carregados`,
          timeout: 1500,
          position: 'top'
        });
      }
    });

    // Listen for the tickets
    // this.socket.on("user_tickets", (tickets) => {
    // 	this.ticket_list = tickets;
    // });
  },
  methods: {
    change_ticket(ticket_id) {
      console.log('🎯 Selecionando ticket:', ticket_id);
      
      // Sair da sala anterior se existir
      if (this.store.current_ticket) {
        this.socket.emit('leave_room', this.store.current_ticket);
      }
      
      // Entrar na nova sala
      this.socket.emit('join_room', ticket_id);
      
      // Emitir evento para carregar histórico do ticket
      this.socket.emit('load_ticket', ticket_id);
      
      // Definir ticket atual no store
      this.store.setTicket(ticket_id);
      
      console.log(`🚪 Empresa entrou na sala: ${ticket_id}`);
      
      // Feedback visual
      this.$q.notify({
        type: 'positive',
        message: `Chat selecionado: ${ticket_id}`,
        timeout: 1000,
        position: 'top'
      });
    },
    
    handleClick(uuid) {
      console.log('👆 Clique no budget:', uuid);
      
      if (this.$q.screen.lt.md) {
        this.$router.push({ path: `${this.$route.path}/${uuid}` });
      } else {
        this.change_ticket(uuid);
      }
    },

    formatDate(dateStr) {
      if (!dateStr) return '';
      const d = new Date(dateStr);
      return d.toLocaleDateString('pt-BR'); // e.g., 02/06/2025
    },

    create_ticket() {
      const new_ticket_name = prompt('Id do ticket:');
      if (new_ticket_name && new_ticket_name !== '') {
        this.socket.emit('load_ticket', new_ticket_name);
        this.store.setTicket(new_ticket_name);
      }
    },

    editMessage(msg) {
      const newText = prompt('Edite a mensagem:', msg.text);
      if (newText && newText !== msg.text) {
        this.socket.emit('update_message', {
          _id: msg._id,
          new_text: newText,
          ticket: this.store.current_ticket,
        });
      }
    },

    deleteMessage(msg) {
      if (confirm('Quer mesmo deletar essa mensagem?')) {
        this.socket.emit('delete_message', {
          _id: msg._id,
          ticket: this.store.current_ticket,
        });
      }
    },
    
    applyParticipantFilter(filterData) {
      console.log('🎯 TicketList - applyParticipantFilter chamado:');
      console.log('  - filterData:', filterData);
      console.log('  - filterData type:', typeof filterData);
      
      this.participantFilter = filterData;
      
      console.log('  - participantFilter atualizado:', this.participantFilter);
      console.log('  - Forçando atualização do computed...');
      
      // Forçar reavaliação do computed
      this.$nextTick(() => {
        console.log('  - Após nextTick, filteredBudgets.length:', this.filteredBudgets?.length || 0);
      });
    },
    
    resetFilter() {
      console.log('🔄 Resetando filtro...');
      this.participantFilter = null;
      console.log('  - participantFilter resetado:', this.participantFilter);
      this.$forceUpdate();
    },
    
    retryLoadBudgets() {
      console.log('🔄 Tentando carregar budgets novamente...');
      
      // Resetar filtro antes de carregar
      this.resetFilter();
      
      const access_token = localStorage.getItem('access_token');
      
      if (access_token) {
        // Debug do token
        this.debugToken(access_token);
        
        this.socket.emit('load_user_budgets', access_token);
        
        this.$q.notify({
          type: 'info',
          message: 'Tentando carregar chats...',
          timeout: 1000,
          position: 'top'
        });
      } else {
        this.$q.notify({
          type: 'negative',
          message: 'Token não encontrado. Faça login novamente.',
          timeout: 3000,
          position: 'top'
        });
      }
    },
    
    debugToken(token) {
      try {
        const base64Url = token.split('.')[1];
        const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
        const jsonPayload = decodeURIComponent(
          atob(base64).split('').map(c =>
            '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2)
          ).join('')
        );
        const payload = JSON.parse(jsonPayload);
        console.log('🔍 Token payload:', payload);
        console.log('🆔 User UUID:', payload.uuid);
        console.log('👤 User name:', payload.full_name);
      } catch (e) {
        console.error('❌ Erro ao decodificar token:', e);
      }
    },
  },
  computed: {
    isMobile() {
      return this.$q.screen.lt.md;
    },
  },
};
</script>
