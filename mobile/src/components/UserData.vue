<template>
	<div class="user-data flex column q-pa-sm" style="gap: 0.25rem;">
		<h5 class="reset-styles text-primary">{{ name }}</h5>
		<span class="reset-styles text-subtitle2  text-secondary">{{ email }}</span>
		<span class="reset-styles text-subtitle2 text-secondary">{{ celular }}</span>
	</div>
</template>
  
<script>
	import { defineComponent } from 'vue'
	
	export default defineComponent({
		props: {
			name: {
				type: String,
				required: true
			},
		
			celular: {
				type: String,
				default: '(xx) x xxxx-xxxx'
			},

			email: {
				type: String,
				required: true
			}
		}
	})
</script>

<style>
.reset-styles {
	padding: 0;
	margin: 0;
	box-sizing: border-box;
}
</style>