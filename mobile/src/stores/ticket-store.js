import { defineStore } from 'pinia';

export const useTicketStore = defineStore('ticket_store', {
	state: () => ({
		current_ticket: '', 
		messages: [],
	}),
	actions: {
		setTicket(ticket) {
			this.current_ticket = ticket; 
		},

		chatMessage(data){
			if (data.ticket == this.current_ticket) {
				// Verificar se já existe uma mensagem similar (evitar duplicatas)
				const isDuplicate = this.messages.find(m => 
					m.text === data.text && 
					m.user_uuid === data.user_uuid &&
					Math.abs(new Date(m.created_at) - new Date(data.created_at)) < 5000 // 5 segundos
				);

				if (!isDuplicate) {
					console.log('✅ Dashboard: Adicionando nova mensagem:', data.text);
					this.messages.push({
						_id: data._id,
						text: data.text,
						user_uuid: data.user_uuid,
						user_name: data.user_name,
						avatar: data.user_uuid === '12345'
						? 'https://cdn.quasar.dev/img/avatar2.jpg'
						: 'https://cdn.quasar.dev/img/avatar4.jpg',
						sent: data.user_uuid === localStorage.getItem('uuid') ? true : false,
						created_at: data.created_at
					});	
				} else {
					console.log('🚫 Dashboard: Mensagem duplicada ignorada:', data.text);
				}
			}
		},

		chatHistory(messages){
			this.messages = []; 
			messages.forEach((msg) => {
				this.messages.push({
					_id: msg._id,
					text: msg.text,
					user_uuid: msg.user_uuid,
					user_name: msg.user_name,
					avatar: msg.user_uuid === '12345'
					? 'https://cdn.quasar.dev/img/avatar2.jpg'
					: 'https://cdn.quasar.dev/img/avatar4.jpg',
					sent: msg.user_uuid === localStorage.getItem('uuid') ? true : false,
                    created_at: msg.created_at
				});
			});
		}
	},
});
