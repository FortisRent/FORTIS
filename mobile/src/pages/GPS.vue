<template>
  <q-page>
    <p>Localização atual: <strong>{{ positionText }}</strong></p>
    <div id="map"></div>
  </q-page>
</template>

<script>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { Geolocation } from '@capacitor/geolocation'
import L from 'leaflet'
import markerIcon from 'leaflet/dist/images/marker-icon.png'
import markerShadow from 'leaflet/dist/images/marker-shadow.png'

export default {
  setup() {
    const positionText = ref('Determining...')
    let map = null
    let marker = null
    let geoId = null

    const customIcon = L.icon({
      iconUrl: markerIcon,
      shadowUrl: markerShadow,
      iconSize: [25, 41],
      iconAnchor: [12, 41],
      popupAnchor: [1, -34],
      shadowSize: [41, 41],
    })

    function initializeMap(lat, lng) {
      if (!map) {
        map = L.map('map').setView([lat, lng], 18)

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '&copy; OpenStreetMap contributors',
        }).addTo(map)

        // Add marker with custom icon
        marker = L.marker([lat, lng], { icon: customIcon }).addTo(map)
      } else {
        // Update marker position
        marker.setLatLng([lat, lng])
        map.setView([lat, lng])
      }
    }

    async function getCurrentPosition() {
      try {
        const newPosition = await Geolocation.getCurrentPosition()
        const { latitude, longitude } = newPosition.coords
        positionText.value = `Lat: ${latitude}, Lng: ${longitude}`
        initializeMap(latitude, longitude)
      } catch (error) {
        positionText.value = 'Unable to get location'
        console.error(error)
      }
    }

    onMounted(() => {
      getCurrentPosition()

      geoId = Geolocation.watchPosition({}, (newPosition, err) => {
        if (err) {
          console.error(err)
          positionText.value = 'Error retrieving position'
          return
        }

        if (newPosition?.coords) {
          const { latitude, longitude } = newPosition.coords
          positionText.value = `Lat: ${latitude}, Lng: ${longitude}`
          initializeMap(latitude, longitude)
        }
      })
    })

    onBeforeUnmount(() => {
      if (geoId) {
        Geolocation.clearWatch({ id: geoId })
      }
    })

    return {
      positionText,
    }
  },
}
</script>

<style>
@import 'leaflet/dist/leaflet.css';

#map {
  height: 90vh;
  width: 100%;
  margin-top: 10px;
}
</style>