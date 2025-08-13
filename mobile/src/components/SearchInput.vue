<template>
	<q-input bottom-slots v-model="text" label="Procurar" maxlength="32" standout input-style="font-size: 1rem;">
		<template v-slot:append>
			<q-icon name="search" />
		</template>
	</q-input>
</template>

<script>
	export default {
		data() {
			return {
				text: '',
			};
		}
	}
</script>