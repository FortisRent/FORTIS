<template>
	<q-item clickable v-close-popup @click="onItemClick">
		<q-item-section avatar>
			<q-avatar :icon="icon" :color="color" text-color="white" />
		</q-item-section>
		<q-item-section>
			<q-item-label>{{ name }}</q-item-label>
			<q-item-label caption> {{ quantity }}</q-item-label>
		</q-item-section>
	</q-item>
</template>
  
<script>
	import { defineComponent } from 'vue'
	
	export default defineComponent({
		props: {
			icon: {
				type: String,
				required: true
			},
			color: {
				type: String,
				required: true
			},
			name: {
				type: String,
				required: true
			},
			quantity: {
				type: String,
				default: '0'
			},
		}
	})
</script>