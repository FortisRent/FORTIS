<template>
	<div style="overflow-x: hidden;">
		<HeaderLandingPage />
		<PresentationLandingPage />
		<ServicesLandingPage />
		<AdvantagesLandingPage />
		<ConversationLandingPage />
		<OfferEquipmentLandingPage />
		<PersonalizedServicesLandingPage />
		<FooterLandingPage />
	</div>
</template>
  
  <script>
	import HeaderLandingPage from 'src/components/LandingPageComponents/HeaderLandingPage.vue';
	import PresentationLandingPage from 'src/components/LandingPageComponents/PresentationLandingPage.vue';
	import ServicesLandingPage from 'src/components/LandingPageComponents/ServicesLandingPage.vue';
	import AdvantagesLandingPage from 'src/components/LandingPageComponents/AdvantagesLandingPage.vue';
	import ConversationLandingPage from 'src/components/LandingPageComponents/ConversationLandingPage.vue';
	import OfferEquipmentLandingPage from 'src/components/LandingPageComponents/OfferEquipmentLandingPage.vue';
	import PersonalizedServicesLandingPage from 'src/components/LandingPageComponents/PersonalizedServicesLandingPage.vue';
	import FooterLandingPage from 'src/components/LandingPageComponents/FooterLandingPage.vue';

  export default {
		components: {
			HeaderLandingPage,
			PresentationLandingPage,
			ServicesLandingPage,
			AdvantagesLandingPage,
			ConversationLandingPage,
			OfferEquipmentLandingPage,
			PersonalizedServicesLandingPage,
			FooterLandingPage
		}
	}
  </script>
  