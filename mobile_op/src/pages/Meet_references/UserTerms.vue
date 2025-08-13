<template>
<q-page class="bg-white">
	<q-btn
				flat
				round
				icon="chevron_left"
				class="text-primary"
				color="secondary"
				size="18px"
				style="position: absolute; left: 10px;"
				@click="$router.go(-1)"
			/>
	<div class="flex justify-between ">
		<h4 class="q-mb-sm text-primary q-ml-sm">Termos de Uso</h4>
	</div>
	
	<q-separator class="bg-info"/>
	
	<div class="q-pa-sm">
		
		<spam style="margin-left: 40px;" class="text-primary">Data de atualização: 9 de dezembro de 2024</spam>
		<q-list>
			<p class="text-h6 q-mt-lg text-info">Introdução</p>
			<p class="text-primary">O Meet Med respeita sua privacidade e se compromete a proteger os dados pessoais coletados. Esta Política de Privacidade explica como processamos seus dados em conformidade com a Lei Geral de Proteção de Dados (LGPD) e outras legislações aplicáveis no Brasil. </p>
			<p class="text-primary">Ao utilizar o aplicativo, você concorda com os termos desta política.</p>

			<p  class="text-h6 q-mt-lg text-info">Geral </p>
			<p class="text-primary">Nesse Aviso deixa claro de como usamos os bancos de dados coletados pela empresa e afiliadas. Os Termos é aplicado para todos os Usuários, Profissionais da área da sáude ou pacientes (usuários). </p>

			<p class="text-h6 q-mt-lg text-info">Coleta de dados </p>

			<p class="text-primary"><strong>Informações Pessoais</strong></p>
			<p class="text-primary"> - Nome completo;</p>
			<p class="text-primary"> - Data de nascimento;</p>
			<p class="text-primary"> - Endereço de e-mail;</p>
			<p class="text-primary"> - Telefone;</p>
			<p class="text-primary"> - CPF ou outros documentos de identificação, se necessário;</p>

			<p class="text-primary"><strong>Dados de Saúde</strong></p>
			<p class="text-primary"> - Histórico médico;</p>
			<p class="text-primary"> - Sintomas relatados;</p>
			<p class="text-primary"> - Resultados de exames, diagnósticos e prescrições;</p>
			<p class="text-primary"> - Sua localização, mediante autorização prévia, para o encontro de clínicas e médicos próximos;</p>
			<p class="text-primary"> - Fotos e arquivos, mediante autorização prévia, como fotos de perfil, capas e logomarcas;</p>

			<p class="text-primary"><strong>Dados de Uso</strong></p>
			<p class="text-primary"> - Informações sobre como o aplicativo é usado (tempo de uso, interações, etc.);</p>
			<p class="text-primary"> - Dados de localização, se autorizados pelo usuário;</p>

			<p class="text-h6 q-mt-lg text-info">COMPARTILHAMENTO DE INFORMAÇÕES </p>
			<p class="text-primary"> O aplicativo Meet Med pode compartilhar suas informações pessoais com terceiros em algumas circunstâncias, como:</p>
			<p class="text-primary"> - Sistema de pagamento final (Stripe), mediante os seus <a href="https://stripe.com/br/privacy" target="_blank">termos de privacidade</a>;</p>
			<p class="text-primary"> - Verificação do CRM do profissional (CFM, conselho federal da medicina);</p>
			<p class="text-primary"> - INPI verificação do CNPJ da empresa afiliada ao aplicativo;</p>

			<p class="text-h6 q-mt-lg text-info">FINALIDADE DO USO DOS DADOS</p>
			<p class="text-primary"><strong>Os dados coletados serão utilizados para as seguintes finalidades:</strong></p>
			<p class="text-primary">Fornecer e personalizar os serviços do aplicativo </p>
			<p class="text-primary">Facilitar o agendamento de consultas e exames</p>
			<p class="text-primary">Monitorar e gerenciar a saúde do usuário</p>
			<p class="text-primary">Comunicar informações relevantes, como lembretes e resultados</p>
			<p class="text-primary">Melhorar nossos serviços através de análises de uso</p>

			<p class="text-h6 q-mt-lg text-info">SEGURANÇA DOS DADOS</p>
			<p class="text-primary">Adotamos medidas de segurança técnicas, administrativas e organizacionais para proteger suas informações contra acesso não autorizado, perda, alteração ou destruição.</p>

			<p class="text-primary">Dados criptografados em trânsito e em repouso.</p>
			<p class="text-primary">Acesso restrito apenas a funcionários autorizados.</p>
			<p class="text-primary">Monitoramento constante para identificar vulnerabilidades.</p>

			<p class="text-h6 q-mt-lg text-info">DIREITOS DOS USUÁRIOS</p>
			<p class="text-primary"><strong>Você tem os seguintes direitos em relação aos seus dados:</strong></p>
			<p class="text-primary">Solicitar acesso às suas informações;</p>
			<p class="text-primary">Corrigir dados incompletos, inexatos ou desatualizados;</p>
			<p class="text-primary">Solicitar a exclusão de dados pessoais, exceto quando seu armazenamento for exigido por lei;</p>
			<p class="text-primary">Revogar o consentimento para o uso de dados a qualquer momento;</p>
			<p class="text-primary">Portar os dados para outro fornecedor de serviço, quando aplicável;</p>
			<p class="text-primary">Para exercer esses direitos, entre em contato pelo e-mail <a href="mailto:suporte@meetmed.com.br">suporte@meetmed.com.br</a>;</p>

			<p class="text-h6 q-mt-lg text-info">COOKIES E TECNOLOGIAS SEMELHANTES</p>
			<p class="text-primary"><strong>Utilizamos cookies e tecnologias semelhantes para:</strong></p>
			<p class="text-primary">Melhorar sua experiência no aplicativo;</p>
			<p class="text-primary">Personalizar conteúdo e funcionalidades;</p>
			<p class="text-primary">Analisar o uso do aplicativo;</p>

			<p class="text-primary">Você pode gerenciar suas preferências de cookies diretamente nas configurações do aplicativo.</p>

			<p class="text-h6 q-mt-lg text-info">RETENÇÃO DE DADOS</p>
			<p class="text-primary">Manteremos seus dados pessoais apenas pelo tempo necessário para atender às finalidades descritas nesta política ou conforme exigido por lei. Após esse período, os dados serão anonimizados ou excluídos de maneira segura.</p>

			<p class="text-h6 q-mt-lg text-info">ALTERAÇÕES NA POLÍTICA DE PRIVACIDADE</p>
			<p class="text-primary">Podemos atualizar esta política periodicamente. Notificaremos os usuários sobre mudanças significativas, seja por meio de e-mail, notificações no aplicativo ou outra forma.</p>

			<p class="text-h6 q-mt-lg text-info">ENTRE EM CONTATO CONOSCO</p>
			<p class="text-primary">Se tiver dúvidas ou preocupações sobre esta Política de Privacidade ou sobre o uso de seus dados, entre em contato pelo e-mail <a href="mailto:suporte@meetmed.com.br">suporte@meetmed.com.br</a> ou pelo telefone <a href="tel:+5534999277343">+55 (34) 99927-7343</a>.</p>
		</q-list>
	</div>
	</q-page>
</template>