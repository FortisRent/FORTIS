<template>
  <q-item 
    clickable 
    @click="$emit('click')"
    :class="{ 'selected-budget': selected }"
    class="budget-item"
  >
    <q-item-section v-if="!isMobile">
      <q-item-label
        class="text-primary"
        style="font-size: 1.125rem; font-weight: 600"
      >
        #{{ identifier }}</q-item-label
      >
      <q-item-label class="text-subtitle1 text-secondary">{{
        user_name
      }}</q-item-label>
      <q-item-label
        v-if="this.client_name"
        class="text-subtitle1 text-secondary"
        >{{ client_name }}</q-item-label
      >
    </q-item-section>

    <q-item-section v-else class="item-section-mobile">
      <q-avatar
				v-if="!this.client_name"
        size="42px"
        color="primary"
        text-color="white"
        class="text-subtitle1"
      >
        {{ user_name.slice(0, 2).toUpperCase() }}
      </q-avatar>
			<q-avatar
				v-else
        size="42px"
        color="primary"
        text-color="white"
        class="text-subtitle1"
      >
			{{ console.log(index) }}
        {{ client_name.slice(0, 2).toUpperCase() }}
      </q-avatar>
      <div>
        <q-item-label
          class="text-primary"
          style="font-size: 1.125rem; font-weight: 600"
        >
          #{{ identifier }}</q-item-label
        >
        <q-item-label class="text-subtitle1 text-secondary">{{
          user_name
        }}</q-item-label>
        <q-item-label
          v-if="this.client_name"
          class="text-subtitle1 text-secondary"
          >{{ client_name }}</q-item-label
        >
      </div>
    </q-item-section>

    <!-- <q-item-section side top>
			<q-item-label caption> {{ last_interaction }} </q-item-label>
			<q-chip clickable :color="color" :text-color="text_color" :icon="icon"> {{ quantity }} </q-chip>
		</q-item-section> -->
  </q-item>
  <q-separator spaced />
</template>

<script>
import { defineComponent } from 'vue';
export default defineComponent({
  name: 'BudgetItem',
  emits: ['click'],
  props: {
    uuid: {
      type: String,
      required: true,
    },
    identifier: {
      type: String,
      required: true,
    },
    client_name: {
      type: String,
      required: false,
    },
    user_name: {
      type: String,
      required: false,
    },
    selected: {
      type: Boolean,
      default: false,
    },
    color: {
      type: String,
      required: true,
      default: 'blue',
    },
    text_color: {
      type: String,
      required: true,
      default: 'orange',
    },
    icon: {
      type: String,
      required: true,
      default: 'message',
    },
  },
  computed: {
    isMobile() {
      return this.$q.screen.lt.md;
    },
  },
});
</script>

<style scoped>
.item-section-mobile {
  display: flex;
  flex-direction: row;
  justify-content: flex-start;
  gap: 0.5rem;
}

.budget-item {
  transition: all 0.3s ease;
  border-radius: 8px;
  margin: 4px 0;
}

.budget-item:hover {
  background-color: rgba(25, 118, 210, 0.05);
}

.selected-budget {
  background-color: rgba(25, 118, 210, 0.1) !important;
  border-left: 4px solid #1976d2;
  font-weight: 500;
}

.selected-budget .q-item-label {
  color: #1976d2 !important;
}

.selected-budget .q-avatar {
  background-color: #1976d2 !important;
}

/* Indicador visual adicional para seleção */
.selected-budget::after {
  content: '';
  position: absolute;
  right: 8px;
  top: 50%;
  transform: translateY(-50%);
  width: 8px;
  height: 8px;
  background-color: #1976d2;
  border-radius: 50%;
}
</style>
