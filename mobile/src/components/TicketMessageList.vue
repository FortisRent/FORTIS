<template>
	<div class="messages-container">
		<!-- Indicador quando nenhum chat está selecionado -->
		<div v-if="!store.current_ticket" class="empty-state">
			<q-icon name="chat" size="4rem" color="grey-5" />
			<div class="empty-title">Selecione um chat para começar</div>
			<div class="empty-subtitle">Escolha uma conversa na lista ao lado</div>
		</div>

		<!-- Indicador de carregamento -->
		<div v-else-if="loading" class="loading-state">
			<q-spinner size="2rem" color="primary" />
			<div class="loading-text">Carregando mensagens...</div>
		</div>

		<!-- Lista de mensagens -->
		<div v-else class="messages-list-container">
			<q-scroll-area class="messages-scroll" ref="scrollArea">
				<div class="messages-list">
					<div 
						v-for="(msg, index) in this.store.messages" 
						:key="index"
						class="message-wrapper"
						:class="{ 'message-sent': msg.sent, 'message-received': !msg.sent }"
					>
						<div class="message-bubble" :class="msg.sent ? 'bubble-sent' : 'bubble-received'">
							<div class="message-text">{{ msg.text }}</div>
							<div class="message-meta">
								<span v-if="!msg.sent" class="sender-name">{{ msg.user_name }}</span>
								<span class="message-time">{{ formatTime(msg.created_at) }}</span>
								
								<!-- Botões de ação apenas para mensagens enviadas -->
								<div v-if="msg.sent" class="message-actions">
									<q-btn
										dense flat round icon="edit"
										size="sm"
										color="white"
										@click="editMessage(msg)"
										class="action-btn"
									>
										<q-tooltip>Editar mensagem</q-tooltip>
									</q-btn>
									
									<q-btn
										dense flat round icon="delete"
										size="sm"
										color="red"
										@click="deleteMessage(msg)"
										class="action-btn"
									>
										<q-tooltip>Deletar mensagem</q-tooltip>
									</q-btn>
								</div>
							</div>
						</div>
					</div>
				</div>
			</q-scroll-area>
		</div>
	</div>
</template>
  
<script>
	import { io } from "socket.io-client";
	import { useTicketStore } from "src/stores/ticket-store";
	
	export default {
		name: 'TicketMessageList',
		data() {
			return {
				socket: null,
				newMessage: "",
				//user_id: localStorage.getItem("user_uuid"),
				user_id: "123456",
				store: useTicketStore(),
				loading: false
			};
		},
		watch: {
					// Assistir mudanças no ticket atual para carregar histórico
		'store.current_ticket'() {
			if (this.store.current_ticket) {
				this.loading = true;
				this.loadChatHistory();
			}
		},
			// Assistir novas mensagens para scroll automático
			'store.messages'() {
				this.$nextTick(() => {
					this.scrollToBottom();
				});
			}
		},
			mounted() {
		this.initializeSocket();
		
		// Se já há um chat selecionado, carregar histórico
		if (this.store.current_ticket) {
			this.loadChatHistory();
		}
	},
	
	methods: {
		initializeSocket() {
			this.socket = io("http://localhost:3000", { 
				rejectUnauthorized: false,
				timeout: 10000
			});

			// Listen for incoming messages
			this.socket.on("chat_message", (data) => {
				console.log('📨 Nova mensagem recebida:', data);
				this.store.chatMessage(data);
				this.$nextTick(() => {
					this.scrollToBottom();
				});
			});

			// Fetch chat history
			this.socket.on("chat_history", (messages) => {
				console.log('📜 Histórico carregado:', messages.length, 'mensagens');
				this.store.chatHistory(messages);
				this.loading = false;
				this.$nextTick(() => {
					this.scrollToBottom();
				});
			});

			// Erro de conexão
			this.socket.on("error", (error) => {
				console.error('❌ Erro do socket:', error);
				this.loading = false;
			});
		},
		
		loadChatHistory() {
			if (!this.store.current_ticket) {
				console.warn('Nenhum ticket selecionado');
				return;
			}
			
			const access_token = localStorage.getItem('access_token');
			if (!access_token) {
				console.error('Token de acesso não encontrado');
				this.loading = false;
				return;
			}
			
			console.log('📋 Carregando histórico do chat:', this.store.current_ticket);
			
			// Usar o evento correto do servidor
			this.socket.emit("load_chat_history", {
				budget_uuid: this.store.current_ticket,
				access_token: access_token
			});
		},
		
		scrollToBottom() {
			if (this.$refs.scrollArea) {
				this.$refs.scrollArea.setScrollPosition('vertical', 9999, 300);
			}
		},
		
		formatDate(timestamp) {
			if (!timestamp) return '';
			
			const date = new Date(timestamp);
			if (isNaN(date.getTime())) return '';
			
			return date.toLocaleString('pt-BR', {
				day: '2-digit',
				month: '2-digit',
				year: 'numeric',
				hour: '2-digit',
				minute: '2-digit'
			});
		},
		
		formatTime(timestamp) {
			if (!timestamp) return '';

			const date = new Date(timestamp);
			if (isNaN(date.getTime())) return '';
			
			return date.toLocaleTimeString('pt-BR', {
				hour: '2-digit',
				minute: '2-digit'
			});
		},
		
		editMessage(msg) {
			const newText = prompt("Edite a mensagem:", msg.text);
			if (newText && newText !== msg.text) {
				this.socket.emit("update_message", {
					_id: msg._id,
					new_text: newText,
					ticket: this.store.current_ticket,
				});
			}
		},

		deleteMessage(msg) {
			if (confirm("Quer mesmo deletar essa mensagem?")) {
				this.socket.emit("delete_message", {
					_id: msg._id,
					ticket: this.store.current_ticket,
				});
			}
		}
	},
	
	beforeUnmount() {
		if (this.socket) {
			this.socket.disconnect();
		}
	}
};
</script>

<style scoped lang="scss">
// ========== CONTAINER PRINCIPAL ==========
.messages-container {
  display: flex;
  flex-direction: column;
  height: 100%;
  background: #ffffff;
  overflow: hidden;
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
    color: #303940;
    margin: 0;
  }
  
  .empty-subtitle {
    font-size: 1rem;
    color: #4e565c;
    margin: 0;
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
    color: #4e565c;
    font-size: 1rem;
  }
}

// ========== MESSAGES ==========
.messages-list-container {
  flex: 1;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

.messages-scroll {
  flex: 1;
  padding: 1rem;
  
  // Scrollbar personalizada
  &::-webkit-scrollbar {
    width: 4px;
  }
  
  &::-webkit-scrollbar-track {
    background: transparent;
  }
  
  &::-webkit-scrollbar-thumb {
    background: rgba(48, 57, 64, 0.3);
    border-radius: 4px;
  }
}

.messages-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  min-height: 100%;
  justify-content: flex-end;
}

.message-wrapper {
  display: flex;
  max-width: 85%;
  animation: slideIn 0.3s ease-out;
  
  &.message-sent {
    align-self: flex-end;
  }
  
  &.message-received {
    align-self: flex-start;
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
    background: #303940;
    color: white;
  }
  
  &.bubble-received {
    background: #f8f9fa;
    color: #303940;
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
  gap: 0.5rem;
  
  .sender-name {
    font-weight: 600;
  }
  
  .message-time {
    font-size: 0.7rem;
    white-space: nowrap;
  }
}

.message-actions {
  display: flex;
  gap: 0.25rem;
  margin-left: auto;
  
  .action-btn {
    opacity: 0.7;
    transition: opacity 0.2s ease;
    
    &:hover {
      opacity: 1;
    }
  }
}

// ========== ANIMAÇÕES ==========
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

// ========== RESPONSIVIDADE ==========
@media (max-width: 768px) {
  .message-wrapper {
    max-width: 90%;
  }
  
  .message-bubble {
    padding: 0.625rem 0.875rem;
    font-size: 0.9rem;
  }
  
  .messages-scroll {
    padding: 0.75rem;
  }
}

// ========== ACESSIBILIDADE ==========
@media (prefers-reduced-motion: reduce) {
  .message-wrapper {
    animation: none;
  }
}
</style>