<template>
	<q-item clickable @click="$emit('click')">
		<q-item-section>
			<q-item-label overline> #{{id}}</q-item-label>
			<q-item-label>{{ title }}</q-item-label>
			<q-item-label caption> {{ body }} </q-item-label>
			<q-item-label overline> {{ created_at }}</q-item-label>
		</q-item-section>

		<q-item-section side top>
			<q-item-label caption> {{ last_interaction }} </q-item-label>
			<q-chip clickable :color="color" :text-color="text_color" :icon="icon"> {{ quantity }} </q-chip>
		</q-item-section>
	</q-item>
	<q-separator spaced />
</template>
  
<script>
	import { defineComponent } from 'vue'
	
	export default defineComponent({
		props: {
			id: {
				type: String,
				required: true
			},
			title: {
				type: String,
				required: true
			},
			body: {
				type: String,
				required: true
			},
			created_at: {
				type: String,
				required: true
			},
			last_interaction: {
				type: String,
				required: true
			},
			color: {
				type: String,
				required: true,
				default: 'blue'
			},
			text_color: {
				type: String,
				required: true,
				default: 'white'
			},
			icon: {
				type: String,
				required: true,
				default: 'message'
			},
			quantity: {
				type: String,
				default: '0'
			},
		}
	})
</script>