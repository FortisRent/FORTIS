// Import packages
import express from "express";
import http from "http";
import { Server } from "socket.io";
import cors from "cors";
import mongoose from "mongoose";
import { log } from "console";
import mysql from "mysql2"


// Create Server instance
const app = express();
const server = http.createServer(app);

let chat_history = [];

// Configure CORS options
const corsOptions = {
  origin: ["http://localhost:9000", "http://localhost:8080", "http://localhost:3000"],
  methods: ["GET", "POST", "OPTIONS"],
  allowedHeaders: ["Content-Type"],
  credentials: true
};

const io = new Server(server, {
  cors: corsOptions,
  transports: ['websocket', 'polling'],
  allowEIO3: true
});

// MongoDB connection
mongoose
//   .connect('mongodb://147.79.106.39:27017/chat_messages_db', {
  .connect('mongodb://localhost:27017/chat_messages_db', {
    useNewUrlParser: true,
    useUnifiedTopology: true,
  })
  .then(() => console.log('Connected to MongoDB'))
  .catch((err) => console.error('Could not connect to MongoDB:', err));

const chatMessageSchema = new mongoose.Schema({
	ticket: String,
	user_uuid: String,
	user_name: String,
	text: String,
	created_at: {
		type: Date,
		default: Date.now,
	},
});

// Schema para controlar mensagens lidas por usuário
const messageReadSchema = new mongoose.Schema({
	user_uuid: String,
	ticket: String,
	last_read_message_id: mongoose.Schema.Types.ObjectId,
	last_read_at: {
		type: Date,
		default: Date.now,
	},
});

const ChatMessage = mongoose.model('ChatMessage', chatMessageSchema);
const MessageRead = mongoose.model('MessageRead', messageReadSchema);


function connect_to_mysql() {
	// Create a connection - USANDO O MESMO BANCO DA API
	const connection = mysql.createConnection({
	  host: '31.97.27.221',
	  user: 'u234488260_fortis',
	  password: 'y5Q*|l9xnX0',
	  database: 'u234488260_fortis'
	});

	return connection;
}


async function save_message (data) {
	try {
		const message = new ChatMessage(data);
		const savedMessage = await message.save();
		console.log("Salvando mensagem: \n\n", savedMessage);
		return savedMessage;
	} catch (error) {
		throw error;
	}
};

// Função para calcular mensagens não lidas para um usuário em um ticket
async function getUnreadCount(user_uuid, ticket) {
	try {
		// Buscar o último registro de leitura do usuário para este ticket
		const lastRead = await MessageRead.findOne({ 
			user_uuid: user_uuid, 
			ticket: ticket 
		});

		let unreadCount = 0;

		if (!lastRead) {
			// Se nunca leu mensagens deste ticket, contar todas as mensagens que não são suas
			unreadCount = await ChatMessage.countDocuments({ 
				ticket: ticket,
				user_uuid: { $ne: user_uuid } 
			});
		} else {
			// Contar mensagens mais recentes que a última lida e que não são do próprio usuário
			unreadCount = await ChatMessage.countDocuments({
				ticket: ticket,
				user_uuid: { $ne: user_uuid },
				created_at: { $gt: lastRead.last_read_at }
			});
		}

		return unreadCount;
	} catch (error) {
		console.error('Erro ao calcular mensagens não lidas:', error);
		return 0;
	}
}

// Função para marcar mensagens como lidas
async function markAsRead(user_uuid, ticket) {
	try {
		// Buscar a última mensagem do ticket
		const lastMessage = await ChatMessage.findOne({ ticket: ticket })
			.sort({ created_at: -1 });

		if (lastMessage) {
			// Atualizar ou criar registro de leitura
			await MessageRead.findOneAndUpdate(
				{ user_uuid: user_uuid, ticket: ticket },
				{ 
					last_read_message_id: lastMessage._id,
					last_read_at: new Date()
				},
				{ upsert: true } // Criar se não existir
			);
			console.log(`✅ Mensagens marcadas como lidas para usuário ${user_uuid} no ticket ${ticket}`);
		}
	} catch (error) {
		console.error('Erro ao marcar mensagens como lidas:', error);
	}
}

function decodeJwt(token) {
  try {
    const base64Url = token.split('.')[1];
    const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
    const jsonPayload = decodeURIComponent(
      atob(base64).split('').map(c =>
        '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2)
      ).join('')
    );
    return JSON.parse(jsonPayload);
  } catch (e) {
    console.error("Token inválido", e);
    return null;
  }
}

app.use(cors(corsOptions));

app.use(express.static("public"));

io.on("connection", (socket) => {
	console.log('🔌 Novo cliente conectado:', socket.id);

	// Entrar em uma sala específica do budget
	socket.on("join_room", (budget_uuid) => {
		socket.join(budget_uuid);
		console.log(`📥 Cliente ${socket.id} entrou na sala: ${budget_uuid}`);
		
		socket.emit("joined_room", budget_uuid);
	});

	// Sair de uma sala
	socket.on("leave_room", (budget_uuid) => {
		socket.leave(budget_uuid);
		console.log(`📤 Cliente ${socket.id} saiu da sala: ${budget_uuid}`);
	});

	// Carregar histórico de mensagens de um budget específico
	socket.on("load_chat_history", async (data) => {
		try {
			const { budget_uuid, access_token } = data;
			
			console.log(`📨 Solicitação de histórico para budget: ${budget_uuid}`);
			
			if (!budget_uuid) {
				console.error("❌ Budget UUID não fornecido");
				socket.emit("error", { message: "Budget UUID é obrigatório" });
				return;
			}

			// Verificar se o usuário tem acesso a este budget
			const payload = decodeJwt(access_token);
			if (!payload) {
				console.error("❌ Token JWT inválido");
				socket.emit("error", { message: "Token inválido" });
				return;
			}

			console.log(`✅ Token válido para usuário: ${payload.full_name} (${payload.uuid})`);

			// Entrar na sala do budget
			socket.join(budget_uuid);
			console.log(`📥 Cliente ${socket.id} entrou na sala: ${budget_uuid}`);

			// Marcar mensagens como lidas quando o usuário carrega o histórico
			await markAsRead(payload.uuid, budget_uuid);

			// Buscar mensagens do MongoDB
			const messages = await ChatMessage.find({ ticket: budget_uuid })
				.sort({ created_at: 1 })
				.exec();
			
			console.log(`📜 Carregando ${messages.length} mensagens para budget: ${budget_uuid}`);
			console.log(`🔍 Cliente na sala: ${socket.id}, Total de clientes na sala: ${io.sockets.adapter.rooms.get(budget_uuid)?.size || 0}`);
			
			// Emitir histórico apenas para o cliente que solicitou
			socket.emit("chat_history", messages);
			
		} catch (error) {
			console.error("❌ Erro ao carregar histórico:", error);
			socket.emit("error", { message: "Erro ao carregar histórico de mensagens" });
		}
	});

	socket.on("load_ticket", async (current_ticket) => {
		socket.join(current_ticket);
		try {
		  const messages_filter = await ChatMessage.find({ ticket: current_ticket }).exec();
		  socket.emit("chat_history", messages_filter);
		  console.log('carregando mensagens do ticket ' + current_ticket + '\n\n ' +messages_filter);
		} catch (error) {
		  console.error("Error loading chat history:", error);
		}
	});

	socket.on("load_user_tickets", async (access_token) => {
		try {
			const payload = decodeJwt(access_token);

			const tickets = await ChatMessage.aggregate([
				{ $match: { user_uuid: payload.uuid } },
				{
					$group: {
						_id: "$ticket",
						firstMessage: { $min: "$created_at" },
						lastMessage: { $max: "$created_at" },
						count: { $sum: 1 }
					}
				}
			]);

			// For each ticket, find the last message text
			let formattedTickets = await Promise.all(tickets.map(async (t) => {
				const lastMsg = await ChatMessage.findOne({ ticket: t._id })
					.sort({ created_at: -1 })
					.limit(1);

				return {
					id: t._id,
					user_name: t.user_name,
					body: lastMsg?.text || '',
					created_at: t.firstMessage,
					last_interaction: t.lastMessage,
					quantity: t.count
				};
			}));

			formattedTickets.sort((a, b) => new Date(b.last_interaction) - new Date(a.last_interaction));

			socket.emit("user_tickets", formattedTickets);
		} catch (error) {
			console.error("Error loading user tickets:", error);
		}
	});

	socket.on("load_user_budgets", async (access_token) => {
		try {
			const payload = decodeJwt(access_token);

			var connection = connect_to_mysql();

			connection.connect(err => {
				if (err) throw err;

				const query = `SELECT b.uuid, p.identifier, c.name as client_name, u.full_name as user_name 
								FROM budget b 
								INNER JOIN project p 
									ON b.project_id = p.id 
								LEFT JOIN project_client pc
									ON pc.project_id = p.id
								LEFT JOIN client c
									ON pc.client_id = c.id
								LEFT JOIN project_user pu
									ON pu.project_id = p.id
								LEFT JOIN user u
									ON pu.user_id = u.id
								WHERE u.uuid = '${payload.uuid}' OR b.company_id = (SELECT id FROM company WHERE responsible_id = (SELECT id FROM user WHERE uuid = '${payload.uuid}'));`;

				connection.query(query, (err, results) => {
					if (err) throw err;

					console.log('Results:', results);

					socket.emit("user_budgets", results);
					connection.end();
				});
			});
			
		} catch (error) {
			console.error("Error loading user tickets:", error);
		}
	});

	// Carregar chats da empresa (budgets da empresa)
	socket.on("load_company_chats", async (data) => {
		try {
			const { access_token, company_uuid } = data;
			
			if (!access_token || !company_uuid) {
				socket.emit("error", { message: "Token de acesso e UUID da empresa são obrigatórios" });
				return;
			}

			const payload = decodeJwt(access_token);
			if (!payload) {
				socket.emit("error", { message: "Token inválido" });
				return;
			}

			console.log(`🏢 Carregando chats da empresa: ${company_uuid}`);

			var connection = connect_to_mysql();

			connection.connect(err => {
				if (err) {
					console.error("Erro ao conectar no MySQL:", err);
					socket.emit("error", { message: "Erro de conexão com banco" });
					return;
				}

				// Query para buscar orçamentos da empresa
				const query = `
					SELECT 
						b.uuid as budget_uuid,
						p.identifier,
						p.name as project_name,
						u.full_name as client_name,
						s.name as status_name,
						b.created_at as last_message_time,
						0 as unread_count
					FROM budget b
					INNER JOIN project p ON b.project_id = p.id
					INNER JOIN user u ON p.user_id = u.id
					LEFT JOIN budget_history bh ON bh.id = (
						SELECT bh1.id
						FROM budget_history bh1
						WHERE bh1.budget_id = b.id
						ORDER BY bh1.created_at DESC
						LIMIT 1
					)
					LEFT JOIN status s ON bh.status_id = s.id
					WHERE b.company_id = (SELECT id FROM company WHERE uuid = ?) AND b.deleted_at IS NULL
					ORDER BY b.created_at DESC
				`;

				connection.query(query, [company_uuid], async (err, results) => {
					if (err) {
						console.error("Erro na query empresa:", err);
						socket.emit("error", { message: "Erro ao buscar chats da empresa" });
						connection.end();
						return;
					}

					console.log(`✅ Encontrados ${results.length} chats da empresa`);

					// Calcular mensagens não lidas para cada chat
					const chatsWithUnread = await Promise.all(results.map(async (chat) => {
						try {
							// Calcular mensagens não lidas da empresa
							const unreadCount = await getUnreadCount(payload.uuid, chat.budget_uuid);
							
							// Buscar última mensagem do chat
							const lastMessage = await ChatMessage.findOne({ ticket: chat.budget_uuid })
								.sort({ created_at: -1 });
							
							return {
								...chat,
								unread_count: unreadCount,
								last_message: lastMessage ? lastMessage.text : 'Nenhuma mensagem ainda',
								last_message_time: lastMessage ? lastMessage.created_at : chat.last_message_time
							};
						} catch (error) {
							console.error(`Erro ao processar chat empresa ${chat.budget_uuid}:`, error);
							return {
								...chat,
								unread_count: 0,
								last_message: 'Erro ao carregar mensagem',
								last_message_time: chat.last_message_time
							};
						}
					}));

					socket.emit("company_chats", chatsWithUnread);
					connection.end();
				});
			});
			
		} catch (error) {
			console.error("Erro ao carregar chats da empresa:", error);
			socket.emit("error", { message: "Erro interno do servidor" });
		}
	});

	// Carregar chats do cliente (apenas budgets onde ele é cliente)
	socket.on("load_client_chats", async (data) => {
		try {
			const { access_token } = data;
			
			if (!access_token) {
				socket.emit("error", { message: "Token de acesso é obrigatório" });
				return;
			}

			const payload = decodeJwt(access_token);
			if (!payload) {
				socket.emit("error", { message: "Token inválido" });
				return;
			}

			console.log(`📋 Carregando chats para cliente: ${payload.full_name} (${payload.uuid})`);
			console.log(`🔍 EXECUTANDO QUERY DO CHAT:`);

			var connection = connect_to_mysql();

			connection.connect(err => {
				if (err) {
					console.error("Erro ao conectar no MySQL:", err);
					socket.emit("error", { message: "Erro de conexão com banco" });
					return;
				}

				// Query IDÊNTICA ao endpoint /v1/project/logged/ (ServiceProgress.vue)
				const query = `
					SELECT 
						p.uuid as project_uuid,
						p.identifier,
						p.name as project_name,
						DATE_FORMAT(p.expected_date, '%d/%m/%Y') as expected_date,
						DATE_FORMAT(p.expected_date, '%W') as day_name,
						p.start_time,
						p.end_time,
						u.full_name as client_name,
						pa.zip_code,
						pa.street,
						pa.number_street,
						pa.complement,
						pa.neighborhood,
						c.name as city_name,
						gs.name as state_name,
						s.name as status_name,
						COALESCE(b.uuid, CONCAT('no-budget-', p.uuid)) as budget_uuid,
						CASE 
							WHEN b.id IS NOT NULL THEN COALESCE(comp.name, 'Empresa')
							ELSE 'Aguardando Orçamento'
						END as company_name,
						CASE 
							WHEN b.id IS NULL THEN 'pending'
							WHEN s.name = 'ACEITO' THEN 'completed'
							WHEN s.name = 'CANCELADO' THEN 'cancelled'
							WHEN s.name = 'ATENDIMENTO' THEN 'pending'
							WHEN s.name = 'PROPOSTA ENVIADA' THEN 'active'
							WHEN s.name IS NULL THEN 'active'
							ELSE 'active'
						END as status,
						'Nenhuma mensagem ainda' as last_message,
						COALESCE(b.created_at, p.created_at) as last_message_time,
						0 as unread_count
					FROM project p
					INNER JOIN user u ON p.user_id = u.id
					LEFT JOIN project_address pa ON pa.project_id = p.id
					LEFT JOIN budget b ON b.project_id = p.id
					LEFT JOIN budget_history bh ON bh.id = (
						SELECT bh1.id
						FROM budget_history bh1
						WHERE bh1.budget_id = b.id
						ORDER BY bh1.created_at DESC
						LIMIT 1
					)
					LEFT JOIN status s ON bh.status_id = s.id
					LEFT JOIN company comp ON b.company_id = comp.id
					LEFT JOIN city c ON pa.city_id = c.id
					LEFT JOIN geo_state gs ON c.state_id = gs.id
					WHERE p.user_id = (SELECT id FROM user WHERE uuid = ?) AND p.deleted_at IS NULL
					ORDER BY p.created_at DESC
				`;
				
				console.log('📋 QUERY COMPLETA:');
				console.log(query);

				connection.query(query, [payload.uuid], async (err, results) => {
					if (err) {
						console.error("Erro na query:", err);
						socket.emit("error", { message: "Erro ao buscar chats" });
						connection.end();
						return;
					}

					console.log(`📋 UUID do usuário: ${payload.uuid}`);
					console.log(`✅ Encontrados ${results.length} chats para cliente ${payload.full_name}`);
					console.log(`📊 TODOS OS PROJETOS RETORNADOS:`);
					results.forEach((project, index) => {
						console.log(`  ${index + 1}. ${project.identifier} - ${project.company_name} - Status: ${project.status}`);
					});

					// Calcular mensagens não lidas e última mensagem para cada chat
					const chatsWithUnread = await Promise.all(results.map(async (chat) => {
						try {
							// Para projetos sem budget, usar o project UUID como ticket
							const ticketId = chat.budget_uuid && chat.budget_uuid.startsWith('no-budget-') 
								? chat.budget_uuid.replace('no-budget-', '') 
								: (chat.budget_uuid || chat.project_uuid);
								
							// Calcular mensagens não lidas
							const unreadCount = await getUnreadCount(payload.uuid, ticketId);
							
							// Buscar última mensagem do chat
							const lastMessage = await ChatMessage.findOne({ ticket: ticketId })
								.sort({ created_at: -1 });
							
							console.log(`💬 Projeto ${chat.identifier}: ticket=${ticketId}, unread=${unreadCount}, lastMsg=${lastMessage ? 'sim' : 'não'}`);
							
							return {
								...chat,
								// Garantir que budget_uuid nunca seja null
								budget_uuid: chat.budget_uuid || `no-budget-${chat.project_uuid}`,
								unread_count: unreadCount,
								last_message: lastMessage ? lastMessage.text : 'Nenhuma mensagem ainda',
								last_message_time: lastMessage ? lastMessage.created_at : chat.last_message_time,
								// Manter o budget_uuid original para identificação no frontend
								original_ticket: ticketId
							};
						} catch (error) {
							console.error(`Erro ao processar chat ${chat.budget_uuid}:`, error);
							return {
								...chat,
								unread_count: 0,
								last_message: 'Erro ao carregar mensagem',
								last_message_time: chat.last_message_time
							};
						}
					}));

					console.log('Chats com contadores de não lidas:', chatsWithUnread.map(c => ({
						identifier: c.identifier,
						unread_count: c.unread_count
					})));

					socket.emit("client_chats", chatsWithUnread);
					connection.end();
				});
			});
			
		} catch (error) {
			console.error("Erro ao carregar chats do cliente:", error);
			socket.emit("error", { message: "Erro interno do servidor" });
		}
	});
	
	// When server receives a message...
	socket.on("chat_message", async (data) => {
		try {
			const { ticket, access_token, text } = data;

			const payload = decodeJwt(access_token);
			if (!payload) {
				socket.emit("error", { message: "Token inválido" });
				return;
			}

			console.log(`💬 Nova mensagem de ${payload.full_name} no budget ${ticket}:`, text);

		  	chat_history.push({ ticket: ticket, user_uuid: payload.uuid, user_name: payload.full_name, text: text });

			var insert = await save_message({ ticket: ticket, user_uuid: payload.uuid, user_name: payload.full_name, text: text });	  

			console.log(`✅ Mensagem salva no MongoDB:`, insert._id);

			// Enviar mensagem para todos os clientes na sala do budget
			io.to(ticket).emit("chat_message", insert);
			console.log(`📢 Mensagem enviada para sala: ${ticket}`);

			// Notificar sobre atualização de contadores de mensagens não lidas
			io.emit("chat_list_update", {
				budget_uuid: ticket,
				last_message: text,
				last_message_time: insert.created_at,
				sender_uuid: payload.uuid
			});

		} catch (error) {
		  console.error("Error handling chat_message:", error);
		  socket.emit("error", { message: "Erro ao enviar mensagem" });
		}
	});

	socket.on("update_message", async (data) => {
		try {
			const { _id, new_text, ticket } = data;

			await ChatMessage.findByIdAndUpdate(_id, { text: new_text });

			// Send updated list
			try {
				const messages_filter = await ChatMessage.find({ ticket: ticket }).exec();
				socket.emit("chat_history", messages_filter);
				console.log("Atualizando mensagem " + _id + " tiket " + ticket);

				console.clear();
				console.log("Mensagens \n\n " + messages_filter);
			} catch (error) {
				console.error("Error loading chat history:", error);
			}
		} catch (error) {
			console.error("Error updating message:", error);
		}
	});

	socket.on("delete_message", async (data) => {
		try {
			const { _id, ticket } = data;

			await ChatMessage.findByIdAndDelete(_id);

			// Send updated list
			try {
				const messages_filter = await ChatMessage.find({ ticket: ticket }).exec();
				socket.emit("chat_history", messages_filter);
				console.log("Deletando mensagem " + _id + " tiket " + ticket);
			} catch (error) {
				console.error("Error loading chat history:", error);
			}
		} catch (error) {
			console.error("Error deleting message:", error);
		}
	});

	socket.on("disconnect", () => {
		// socket.to(ticket).emit("chat_user", `Usuário ${socket.id} desconectado.`)
	});
});

server.listen(3000, () => console.log("listening on :3000"));