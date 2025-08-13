<template>
  <q-layout class="q-pa-none q-ma-none bg-white" style="padding-top: 56px;">
    <q-header style="position: fixed; max-width: 400px; width: 100%; margin: 0 auto; background-color: white;">  
      <div class="flex  items-center justify-start q-pt-md text-primary q-mb-md">
        <q-btn flat round icon="arrow_back" @click="goBack" class="q-mr-sm" style="margin-left: 5px; color: #727171;" />
        <q-avatar size="50px" class="q-mr-sm q-ml-md">
          <q-img
            src="https://images.pexels.com/photos/614810/pexels-photo-614810.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1"
            width="50px" height="50px" fit="cover"
            style="border-radius: 100px; " />
        </q-avatar>
        <div class="q-ml-md text-truncate" style="display: flex; flex-direction: column; align-items: flex-start;">
          <div class="text-h6 text-bold" style="color: #727171;">Suporte</div>
          <div class="text-caption" style="margin-top: -5px; color: rgba(114, 113, 113, 0.60);">Central de Ajuda</div>
        </div>
      </div>

      <q-separator class="bg-info" />
    </q-header>

    <div
      class="q-mt-xx q-mb-xl q-pa-lg bg-white"
      style="flex: 1; width:100%; max-width:400px; overflow-y: auto;"
      ref="messageContainer">
      <div
        v-for="(message, index) in messages"
        :key="index"
        style="display: flex; flex-direction: column; padding: 5px;">
          <q-card
            ref="lastMessage"
            flat
            bordered
            :style="message.fromUser ? 'align-self: flex-end;' : 'align-self: flex-start'"
            style="
                max-width: 250px;
                max-height: fit-content;
                border: 1px solid #408267;
                border-radius: 20px;
                padding: 10px;
                margin-bottom: 10px;
              ">
            <safe-html-renderer :text="message.text" :link="message.link" style="color:black;" />
            <div style="font-size: 12px; color: grey; text-align: right;">{{ message.time }}</div>
          </q-card>
          <div v-if="message.options && !message.fromUser" style="display: flex; flex-direction: column; align-items: flex-start; margin-top: 5px;">
          <q-btn
            v-for="option in message.options"
            :key="option.go_to"
            :label="option.message"
            :disable="option.disabled"
            color="white"
            no-caps
            rounded
            flat
            style="margin-bottom: 5px; width: 260px; height: 50px; background-color: #408267; display: flex; justify-content: center;"
            :style="{ opacity: option.disabled ? 0.5 : 1 }"
            @click="handleOptionClick(messages, option.go_to)" />
        </div>
      </div>
    </div>
    
    <div class="q-pa-md bg-white" style="position: fixed; bottom: 20px; width: 100%; max-width: 400px;">
      <q-input
        outlined
        bottom-slots
        v-model="text"
        @keyup.enter="sendMessage">
          <template v-slot:before>
            <q-btn round dense flat icon="add" />
          </template>
          <template v-slot:after>
            <q-btn round dense flat icon="send" @click="sendMessage" />
          </template>
      </q-input>
    </div>
  </q-layout>
</template>
  
<script>
  import SafeHtmlRenderer from './SafeHtmlRenderer.vue';

  export default {
    components: {
      SafeHtmlRenderer,
  },
  data() {
    return {
      text: 'Olá. Preciso de ajuda.',
      messages: [],
      clickedOptionId: null,
    };
  },
  methods: {
    goBack() {
      this.$router.go(-1);
    },
    formatMessage(text, link) {
      console.log('formatMessage - text:', text);
      console.log('formatMessage - link:', link);
      if (link) {
        const routerLink = `<router-link to="${link}" style="color: #408267; text-decoration: none;">deste link</router-link>`;
        return text.replace("[LINK]", routerLink);
      }
      return text;
    },
    sendMessage() {
      if (this.text.trim() !== '') {
        const currentTime = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        this.messages.push({ text: this.text, time: currentTime, fromUser: true });
        this.text = '';

        // this.$nextTick(() => this.scrollToEnd());
        setTimeout(this.sendAutoReply, 1000);
      }
    },
    async sendAutoReply() {
      console.log('Dados do boot.json: ', this.chatData);
      try {
        const currentTime = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        const response = await fetch('http://localhost:5510/boot.php');
        if (!response.ok) {
          console.error('Erro ao buscar boot.json:', response.statusText);
          return;
        }
        
        if (!response.ok) {
          console.error('Erro ao buscar boot.json:', response.statusText);
          return;
        }

        const data = await response.json();
        console.log('Dados carregados:', data);
        this.chatData = data.boot;
        this.showChatbotOptions(data.boot, 1, currentTime);
        this.$nextTick(() => this.scrollToEnd());

      } catch (error) {
        console.error('Erro ao carregar boot.json:', error);
      }
    },
    showChatbotOptions(data, id, currentTime) {
        const messageObject = data.find(item => item.id === id);
        
        if (messageObject) {
            this.messages.push({ 
              text: messageObject.message,
              time: currentTime,
              fromUser: false,
              options: messageObject.options,
              link: messageObject.link || null,
             });
            this.clickedOptionId = null;
            this.$nextTick(() => this.scrollToEnd());
        }
    },
    handleOptionClick(_, optionId) {
        const currentTime = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        this.messages.forEach((message) => {
          if (message.options) {
            message.options = message.options.map((option) => ({
              ...option,
              disabled: option.go_to !== optionId,
            }));
          }
        });

        this.clickedOptionId = optionId;

        this.showChatbotOptions(this.chatData, optionId, currentTime);
        this.$nextTick(() => this.scrollToEnd());
    },
    scrollToEnd() {
      window.scrollTo({
        top: 15500,
        behavior: "smooth",
      });
    }
  }
};
  </script>

<style>
.text-truncate {
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		display: block;
		max-width: 50%;
	}
</style>