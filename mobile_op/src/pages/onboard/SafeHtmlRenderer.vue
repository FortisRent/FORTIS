<template>
    <div>
      <span v-for="(part, index) in parsedParts" :key="index">
        <router-link
          v-if="part.type === 'link'"
          :to="part.content"
          style="color: #408267; text-decoration: none;"
        >
          LINK
        </router-link>
        <span v-else>{{ part.content }}</span>
      </span>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      text: { type: String, required: true },
      link: { type: String, default: null },
    },
    computed: {
      parsedParts() {
        if (this.link) {
          const parts = this.text.split("[LINK]");
          return parts.map((part, index) => {
            if (index < parts.length - 1) {
              return [{ type: "text", content: part }, { type: "link", content: this.link }];
            }
            return { type: "text", content: part };
          }).flat();
        }
        return [{ type: "text", content: this.text }];
      },
    },
  };
  </script>
  