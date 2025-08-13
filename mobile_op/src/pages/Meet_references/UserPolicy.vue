<template>
    <q-page class="bg-white ">
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
            <h4 class="q-mb-sm text-primary q-ml-sm">Política e Privacidade</h4>
        </div>
      
        <q-separator class="bg-info"/>
        <div class="q-pa-sm">
            
            <spam style="margin-left: 40px;">Data de atualização: 15 de outubro de 2024</spam>
            <q-list>
                <p class="text-h6 q-mt-lg text-info">Seus dados são seus.</p>
                    <p class="text-primary">A proteção dos seus dados é muito
                        importante para o Meet Med. Respeitamos a
                        sua privacidade em nossa missão de
                        proporcionar um serviço cada vez melhor!
                        Essa Declaração de Privacidade descreve
                        como obtemos, armazenamos, utilizamos,
                        compartilhamos e protegemos as suas
                        informações.<br>

                        Recomendamos que você conheça as
                        nossas práticas como um todo, mas aqui
                        estão alguns pontos importantes para que
                        você entenda como seus dados são
                        tratados pelo MeetMed:
                    </p>
                </q-list>
            <div class="flex no-wrap text-primary q-mt-lg">
                   
                <q-icon name="arrow_forward" size="40px" color="black"/> 
                <p >Os dados que você cadastra em nossas plataformas são tratados de forma segura.</p>
            </div>
            <div class="flex no-wrap q-mt-xl text-primary">
            <q-icon name="arrow_forward" size="40px" color="black"/> 
            <p> Os dados completos de seus cartões não ficam armazenados em nossas bases e suas transações são feitas com o mais alto padrão de segurança.</p>
            </div>
        </div>
        </q-page>
    </template>
    
    
    <script>
    export default {
    
    
    
    
    }
    
    
    </script>