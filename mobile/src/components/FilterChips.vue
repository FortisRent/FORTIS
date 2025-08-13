<template>
  <div class="filter-chips-container">
    <!-- Indicador quando não há participantes -->
    <div v-if="participants.length === 0" class="no-participants">
      <q-icon name="people_outline" size="sm" color="grey-5" />
      <span class="text-grey-5 text-caption">Carregando participantes...</span>
    </div>
    
    <!-- Lista de participantes -->
    <div class="flex" v-else>
      <q-item
        clickable
        class="q-pa-sm participant-chip"
        v-for="(participant, index) in participants"
        :key="participant.id"
        @click="onParticipantClick(participant)"
        :class="{ 'selected-participant': selectedParticipants.includes(participant.id) }"
        style="
          display: flex;
          flex-direction: column;
          width: 100px;
          align-items: center;
          justify-content: center;
          gap: 0.5rem;
        "
      >
      <q-item-section
        avatar
        class="reset-styles"
        style="display: flex; align-items: center; justify-content: center"
      >
        <q-avatar
          size="42px"
          :color="selectedParticipants.includes(participant.id) ? 'primary' : colors[index % colors.length]"
          text-color="white"
          class="text-subtitle1"
        >
          {{ participant.initials }}
        </q-avatar>
      </q-item-section>

      <q-item-section class="text-center">
        <q-item-label class="text-weight-medium text-primary">
          {{ participant.name }}
        </q-item-label>
        <q-item-label caption class="text-grey-6">
          {{ participant.type }}
        </q-item-label>
      </q-item-section>
    </q-item>
    </div>
  </div>
</template>

<script>
import { io } from 'socket.io-client';

export default {
  name: 'FilterChips',
  emits: ['participants-filtered'],
  data() {
    return {
      socket: null,
      participants: [],
      selectedParticipants: [],
      colors: ['orange-8', 'green-6', 'yellow-8', 'blue-5', 'purple-5'],
      budgets: [],
    };
  },
  mounted() {
    this.initializeSocket();
    this.loadParticipants();
  },
  beforeUnmount() {
    if (this.socket) {
      this.socket.disconnect();
    }
  },
  methods: {
    initializeSocket() {
      console.log('🔌 Inicializando socket no FilterChips...');
      this.socket = io('http://localhost:3000', { rejectUnauthorized: false });
      
      this.socket.on('connect', () => {
        console.log('✅ FilterChips conectado ao socket');
      });
      
      this.socket.on('disconnect', () => {
        console.log('❌ FilterChips desconectado do socket');
      });
      
      this.socket.on('user_budgets', (budgets) => {
        console.log('📦 FilterChips recebeu budgets:', budgets);
        this.budgets = budgets;
        this.extractParticipants();
      });
    },
    
    loadParticipants() {
      const access_token = localStorage.getItem('access_token');
      if (access_token && this.socket) {
        this.socket.emit('load_user_budgets', access_token);
      }
    },
    
    extractParticipants() {
      const participantsMap = new Map();
      
      // Garantir que budgets seja um array
      const budgets = Array.isArray(this.budgets) ? this.budgets : [];
      
      console.log('🔍 Extraindo participantes de', budgets.length, 'budgets');
      
      budgets.forEach(budget => {
        // Adicionar usuário se existir
        if (budget.user_name) {
          const userId = `user_${budget.user_name.trim()}`;
          if (!participantsMap.has(userId)) {
            participantsMap.set(userId, {
              id: userId,
              name: this.formatName(budget.user_name),
              type: 'Usuário',
              initials: this.getInitials(budget.user_name),
              originalData: budget
            });
          }
        }
        
        // Adicionar cliente se existir
        if (budget.client_name) {
          const clientId = `client_${budget.client_name.trim()}`;
          if (!participantsMap.has(clientId)) {
            participantsMap.set(clientId, {
              id: clientId,
              name: this.formatName(budget.client_name),
              type: 'Cliente',
              initials: this.getInitials(budget.client_name),
              originalData: budget
            });
          }
        }
      });
      
      this.participants = Array.from(participantsMap.values());
      console.log('👥 Participantes extraídos:', this.participants);
    },
    
    formatName(name) {
      if (!name) return '';
      // Capitalizar primeira letra de cada palavra e limitar tamanho
      const formatted = name.trim()
        .toLowerCase()
        .split(' ')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
      
      // Limitar o nome para exibição (máximo 10 caracteres)
      return formatted.length > 10 ? formatted.substring(0, 10) + '...' : formatted;
    },
    
    getInitials(name) {
      if (!name) return '??';
      const words = name.trim().split(' ');
      if (words.length === 1) {
        return words[0].substring(0, 2).toUpperCase();
      }
      return (words[0].charAt(0) + words[words.length - 1].charAt(0)).toUpperCase();
    },
    
    onParticipantClick(participant) {
      console.log('🖱️ Clique no participante:', participant);
      
      const index = this.selectedParticipants.indexOf(participant.id);
      
      if (index > -1) {
        // Remover da seleção
        this.selectedParticipants.splice(index, 1);
        console.log('➖ Removido da seleção:', participant.name);
      } else {
        // Adicionar à seleção
        this.selectedParticipants.push(participant.id);
        console.log('➕ Adicionado à seleção:', participant.name);
      }
      
      console.log('📋 Seleção atual:', this.selectedParticipants);
      
      // Emitir evento para componente pai
      this.emitFilterChange();
    },
    
    emitFilterChange() {
      const selectedParticipantData = this.participants.filter(p => 
        this.selectedParticipants.includes(p.id)
      );
      
      this.$emit('participants-filtered', {
        selectedIds: this.selectedParticipants,
        selectedParticipants: selectedParticipantData,
        allParticipants: this.participants
      });
    },
    
    clearSelection() {
      this.selectedParticipants = [];
      this.emitFilterChange();
    },
    
    // Método para atualizar budgets externamente
    updateBudgets(budgets) {
      console.log('🔄 FilterChips: Atualizando budgets externamente:', budgets?.length || 0);
      this.budgets = Array.isArray(budgets) ? budgets : [];
      this.extractParticipants();
    }
  }
};
</script>

<style scoped>
.filter-chips-container {
  min-height: 80px;
  width: 100%;
}

.no-participants {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 20px;
  gap: 8px;
}

.participant-chip {
  border-radius: 8px;
  margin: 2px;
  transition: all 0.3s ease;
  cursor: pointer;
}

.participant-chip:hover {
  background-color: rgba(0, 0, 0, 0.05);
  transform: scale(1.02);
}

.reset-styles {
  padding: 0;
  margin: 0;
  box-sizing: border-box;
}

.selected-participant {
  background-color: rgba(25, 118, 210, 0.1) !important;
  border: 2px solid #1976d2;
  font-weight: 500;
}

.selected-participant:hover {
  background-color: rgba(25, 118, 210, 0.2) !important;
}

.selected-participant .q-avatar {
  background-color: #1976d2 !important;
}

.selected-participant .q-item-label {
  color: #1976d2 !important;
  font-weight: 600;
}
</style>
