<template>
  <div v-if="!isMobile">
    <q-btn-dropdown
      flat
      dense
      stretch
      color="blue-8"
      label="Andamento"
      @click="onMainClick"
      size="1.25rem"
      content-style="color: black; font-size: 0.875rem"
    >
      <q-list>
        <StatusItem icon="warning" color="blue-7" name="Abertos" quantity="3" />

        <StatusItem
          icon="schedule"
          color="blue-8"
          name="Em andamento"
          quantity="2"
        />

        <StatusItem
          icon="folder"
          color="blue-9"
          name="Arquivados"
          quantity="2"
        />

        <StatusItem
          icon="check"
          color="blue-10"
          name="Concluídos"
          quantity="6"
        />
      </q-list>
    </q-btn-dropdown>
  </div>

  <q-list v-else class="list-filters">
    <q-item class="reset-styles list-filters-item">
      <q-item-section>Aberto</q-item-section>
    </q-item>
    <q-item class="reset-styles list-filters-item">
      <q-item-section class="reset-styles">
        <q-item-section>Andamento</q-item-section>
      </q-item-section>
    </q-item>
		<q-item class="reset-styles list-filters-item">
			<q-item-section>Arquivados</q-item-section>
		</q-item>
		<q-item class="reset-styles list-filters-item">
			<q-item-section>Concluídos</q-item-section>
		</q-item>
  </q-list>
</template>

<script>
import StatusItem from './StatusItem.vue';

export default {
  components: {
    StatusItem,
  },
  computed: {
    isMobile() {
      return this.$q.screen.lt.md;
    },
  },
};
</script>

<style>
	.reset-styles {
		padding: 0;
		margin: 0;
		box-sizing: border-box;
	}

	.list-filters {
		display: flex;
		gap: 0.25rem;
		align-items: center;
		justify-content: center;
	}
	
	.list-filters-item {
		min-height: 20px;
		border-radius: 8px;

		padding: 0.25rem 0.5rem;
		color: #121417;
		background-color: #F2F2F5;
		font-size: 0.875rem;
		font-weight: 500;
	}
</style>