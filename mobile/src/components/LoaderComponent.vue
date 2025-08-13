<template>
  <div>
    <q-skeleton type="QSlider" :animation="wave" height="6vh" width="75vw" />
    <q-separator class="empty-space" />

    <q-skeleton type="QSlider" :animation="wave" height="5vh" width="65vw" />
    <q-separator class="empty-space" />

    <q-skeleton type="QSlider" :animation="wave" height="6vh" width="75vw" />
    <q-separator class="empty-space" />

    <q-skeleton type="QSlider" :animation="wave" height="5vh" width="65vw" />
    <q-separator class="empty-space" />

    <q-skeleton type="QSlider" :animation="wave" height="6vh" width="75vw" />
    <q-separator class="empty-space" />

    <q-skeleton type="QSlider" :animation="wave" height="5vh" width="65vw" />
    <q-separator class="empty-space" />

    <q-skeleton type="QSlider" :animation="wave" height="6vh" width="75vw" />
    <q-separator class="empty-space" />

    <q-skeleton type="QSlider" :animation="wave" height="5vh" width="65vw" />
    <q-separator class="empty-space" />

    <q-skeleton type="QSlider" :animation="wave" height="6vh" width="75vw" />
    <q-separator class="empty-space" />

    <q-skeleton type="QSlider" :animation="wave" height="5vh" width="65vw" />
    <q-separator class="empty-space" />
  </div>
</template>

<script>
    export default {
        name: 'LoaderComponent',
    }
</script>

<style>
	.item {
		background-image: linear-gradient(to right, #292929, #464646);
	}

	.empty-space {
    border: none;
    width: 0;
		margin-bottom: 15px;
	}
</style>