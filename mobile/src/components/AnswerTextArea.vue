<template>
	<div class="message-input-container">
		<!-- Indicador de status de conexão -->
		<div class="connection-indicator" v-if="!isConnected">
			<q-icon name="cloud_off" size="sm" color="red" />
			<span class="connection-status disconnected">Desconectado</span>
			<q-btn 
				flat 
				dense 
				size="sm" 
				icon="refresh" 
				@click="reconnectSocket"
				class="q-ml-sm"
			>
				<q-tooltip>Tentar reconectar</q-tooltip>
			</q-btn>
		</div>

		<div class="input-row">
			<q-input 
				type="textarea" 
				borderless 
				:placeholder="isConnected ? 'Sua mensagem aqui...' : 'Conectando ao servidor...'"
				class="message-input"
				v-model="new_message"
				@keyup.enter.ctrl="sendMessage"
				@keyup.enter.meta="sendMessage"
				@keyup.enter.exact.prevent="sendMessage"
				:disable="sending || !isConnected"
				autogrow
				:rows="1"
				:max-height="80"
			/>
			<q-btn 
				icon="send" 
				color="primary" 
				round
				@click="sendMessage"
				:disable="!canSendMessage"
				:loading="sending"
				class="send-button"
			>
				<q-tooltip v-if="!isConnected">
					Conectando ao servidor...
				</q-tooltip>
				<q-tooltip v-else-if="!store.current_ticket">
					Selecione um chat primeiro
				</q-tooltip>
				<q-tooltip v-else-if="!new_message.trim()">
					Digite uma mensagem
				</q-tooltip>
				<q-tooltip v-else>
					Enviar mensagem (Enter ou Ctrl+Enter)
				</q-tooltip>
			</q-btn>
		</div>
	</div>
</template>
<script>
import { io } from "socket.io-client";
import { useTicketStore } from "src/stores/ticket-store";

export default {
	name: 'AnswerTextArea',
	data() {
		return {
			socket: null,
			new_message: "",
			store: useTicketStore(),
			sending: false,
			isConnected: false,
			reconnectAttempts: 0,
			maxReconnectAttempts: 5
		};
	},
	computed: {
		canSendMessage() {
			return this.isConnected && 
				   this.store.current_ticket && 
				   this.new_message.trim() && 
				   !this.sending;
		}
	},
	mounted() {
		this.initializeSocket();
	},
	beforeUnmount() {
		if (this.socket) {
			this.socket.disconnect();
		}
	},
	methods: {
		initializeSocket() {
			try {
				this.socket = io("http://localhost:3000", { 
					rejectUnauthorized: false,
					timeout: 10000,
					reconnection: true,
					reconnectionAttempts: this.maxReconnectAttempts,
					reconnectionDelay: 1000
				});

				// Eventos de conexão
				this.socket.on('connect', () => {
					this.isConnected = true;
					this.reconnectAttempts = 0;
					console.log('🟢 Conectado ao servidor de chat');
					
					// Se há um ticket selecionado, entrar na sala
					if (this.store.current_ticket) {
						this.socket.emit('join_room', this.store.current_ticket);
					}
				});

				this.socket.on('disconnect', () => {
					this.isConnected = false;
					console.log('🔴 Desconectado do servidor de chat');
				});

				this.socket.on('connect_error', (error) => {
					this.isConnected = false;
					this.reconnectAttempts++;
					console.error('❌ Erro de conexão:', error);
					
					if (this.reconnectAttempts >= this.maxReconnectAttempts) {
						this.$q.notify({
							type: 'negative',
							message: 'Não foi possível conectar ao servidor de chat',
							position: 'top'
						});
					}
				});

				// Confirmação de entrada na sala
				this.socket.on('joined_room', (budget_uuid) => {
					console.log('📥 Entrou na sala:', budget_uuid);
				});

				// Listen for incoming messages
				this.socket.on("chat_message", (data) => {
					this.store.chatMessage(data);
					console.log('📨 Nova mensagem recebida:', data);
				});

				// Handle errors
				this.socket.on("error", (error) => {
					console.error('❌ Erro do servidor:', error);
					this.$q.notify({
						type: 'negative',
						message: error.message || 'Erro do servidor',
						position: 'top'
					});
				});

			} catch (error) {
				console.error('Erro ao inicializar socket:', error);
				this.$q.notify({
					type: 'negative',
					message: 'Erro ao conectar com o servidor',
					position: 'top'
				});
			}
		},

		async sendMessage() {
			if (!this.canSendMessage) {
				return;
			}

			const messageText = this.new_message.trim();
			const access_token = localStorage.getItem('access_token');

			if (!access_token) {
				this.$q.notify({
					type: 'warning',
					message: 'Token de acesso não encontrado. Faça login novamente.',
					position: 'top'
				});
				return;
			}

			// Verificar se há um chat selecionado
			if (!this.store.current_ticket) {
				this.$q.notify({
					type: 'warning',
					message: 'Selecione um chat primeiro',
					timeout: 2000,
					position: 'top'
				});
				return;
			}

			this.sending = true;

			try {
				const message = {
					ticket: this.store.current_ticket,
					access_token: access_token,
					text: messageText,
				};

				console.log('📤 Empresa enviando mensagem:', message);
				
				// Emitir mensagem (sem join_room aqui para evitar duplicatas)
				this.socket.emit('chat_message', message);
				
				// Limpar campo de texto
				this.new_message = '';

			} catch (error) {
				console.error('Erro ao enviar mensagem:', error);
				this.$q.notify({
					type: 'negative',
					message: 'Erro ao enviar mensagem. Tente novamente.',
					position: 'top'
				});
			} finally {
				this.sending = false;
			}
		},

		editMessage(msg) {
			const newText = prompt("Edite a mensagem:", msg.text);
			if (newText && newText !== msg.text) {
				try {
					this.socket.emit("update_message", {
						_id: msg._id,
						new_text: newText,
						ticket: this.store.current_ticket,
					});
					
					this.$q.notify({
						type: 'positive',
						message: 'Mensagem editada',
						timeout: 1000,
						position: 'top'
					});
				} catch (error) {
					console.error('Erro ao editar mensagem:', error);
					this.$q.notify({
						type: 'negative',
						message: 'Erro ao editar mensagem',
						position: 'top'
					});
				}
			}
		},

		deleteMessage(msg) {
			if (confirm("Quer mesmo deletar essa mensagem?")) {
				try {
					this.socket.emit("delete_message", {
						_id: msg._id,
						ticket: this.store.current_ticket,
					});
					
					this.$q.notify({
						type: 'positive',
						message: 'Mensagem deletada',
						timeout: 1000,
						position: 'top'
					});
				} catch (error) {
					console.error('Erro ao deletar mensagem:', error);
					this.$q.notify({
						type: 'negative',
						message: 'Erro ao deletar mensagem',
						position: 'top'
					});
				}
			}
		},

		// Método para reconectar manualmente
		reconnectSocket() {
			if (this.socket) {
				this.socket.disconnect();
			}
			this.reconnectAttempts = 0;
			this.initializeSocket();
		}
	},
	
	watch: {
		// Quando o ticket atual mudar, entrar na nova sala
		'store.current_ticket'(newTicket, oldTicket) {
			if (this.socket && this.isConnected) {
				// Sair da sala anterior
				if (oldTicket) {
					this.socket.emit('leave_room', oldTicket);
				}
				
				// Entrar na nova sala
				if (newTicket) {
					this.socket.emit('join_room', newTicket);
					console.log('📥 Entrando na sala:', newTicket);
				}
			}
		}
	},
};
</script>

<style scoped>
.q-textarea .q-field__native {
	resize: none !important;
}

/* Container principal */
.message-input-container {
	background: white;
	border-top: 1px solid #e9ecef;
	padding: 1rem;
	box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
	flex-shrink: 0;
	z-index: 10;
	display: flex;
	flex-direction: column;
	gap: 8px;
}

/* Indicador de conexão */
.connection-indicator {
	display: flex;
	align-items: center;
	justify-content: center;
	padding: 8px 12px;
	background-color: #ffebee;
	border-radius: 20px;
	border: 1px solid #ffcdd2;
	animation: pulse 2s infinite;
}

/* Linha do input */
.input-row {
	display: flex;
	gap: 0.75rem;
	align-items: flex-end;
	max-width: 100%;
}

/* Campo de input */
.message-input {
	flex: 1;
}

.message-input :deep(.q-field__control) {
	border-radius: 1.5rem;
	border: 2px solid #e3e5e7;
	background: white;
	transition: border-color 0.2s ease;
	
	&:hover {
		border-color: #303940;
	}
	
	&:focus-within {
		border-color: #303940;
		box-shadow: 0 0 0 3px rgba(48, 57, 64, 0.1);
	}
}

.message-input :deep(.q-field__native) {
	padding: 0.75rem 1rem;
	font-size: 1rem;
}

/* Botão de enviar */
.send-button {
	width: 48px;
	height: 48px;
	background: #303940;
	box-shadow: 0 2px 8px rgba(48, 57, 64, 0.3);
	transition: all 0.2s ease;
	
	&:hover:not(.disabled) {
		transform: translateY(-1px);
		box-shadow: 0 4px 12px rgba(48, 57, 64, 0.4);
		background: #2a3138;
	}
	
	&:active:not(.disabled) {
		transform: translateY(0);
	}
	
	&.disabled {
		opacity: 0.5;
		background: #4e565c;
		box-shadow: none;
	}
}

/* Status de conexão */
.connection-status {
	font-size: 12px;
	font-weight: 500;
	margin-left: 8px;
}

.connection-status.connected {
	color: #4caf50;
}

.connection-status.disconnected {
	color: #f44336;
}

/* Animações */
@keyframes pulse {
	0% { opacity: 1; }
	50% { opacity: 0.5; }
	100% { opacity: 1; }
}

/* Loading spinner */
.q-btn .q-spinner {
	color: white;
}

/* Responsividade */
@media (max-width: 600px) {
	.message-input-container {
		padding: 0.75rem;
	}
	
	.input-row {
		gap: 8px;
	}
	
	.send-button {
		width: 44px;
		height: 44px;
	}
}
</style>