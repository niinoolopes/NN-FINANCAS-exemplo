<template>
  <TemplateDefault title="Geral">

    <Alert v-if="!$store.getters.I_CarteiraPainel" tipo="warning" texto="Selecione uma carteira no painel para visualizar as ordens!"/>

    <PageSection>
      <IISectionGeralSaldo
        :getDados='getDados_IcarteiraSaldo'
        :time='IIcarteiraSaldo.time'
        :labels='IIcarteiraSaldo.labels'
        :valores='IIcarteiraSaldo.valores'
      />
    </PageSection>
    
    <PageSection>
      <IISectionGeralAnaliseAno 
        :getDados='getDados_IcarteiraAnaliseAno'
        :arrDados='IIanaliseValoresAno'
      />
    </PageSection>


    <PageSection>
      <PageContentTitle titulo='Composições' >
        <template v-slot:btn>
          <ButtongetDados :getDados='getDados_IcarteiraComposicoes' border='0px' />
        </template>
      </PageContentTitle>

      <IISectionGeralComposicao class='mb-3'
        height='175px'
        titulo='Tipo Investimento'
        :time='II_carteiraAnaliseTipo.time'
        :items='II_carteiraAnaliseTipo.INTP.items'
        :items_GRAFICO='II_carteiraAnaliseTipo.INTP.items_GRAFICO'
       />

      <IISectionGeralComposicao class='mb-3'
        height='175px'
        titulo='Tipo Ativo'
        :time='II_carteiraAnaliseTipo.time'
        :items='II_carteiraAnaliseTipo.INAT.items'
        :items_GRAFICO='II_carteiraAnaliseTipo.INAT.items_GRAFICO'
       />

      <IISectionGeralComposicao 
        height='250px'
        titulo='Ativo'
        :time='II_carteiraAnaliseTipo.time'
        :items='II_carteiraAnaliseTipo.INAV.items'
        :items_GRAFICO='II_carteiraAnaliseTipo.INAV.items_GRAFICO'
       />
    </PageSection>

  </TemplateDefault>
</template>

<script>
  import service from "@/service.js"

  export default {
    components: {
      ButtongetDados: () => import('@/components/Button_getDados'),
      IISectionGeralSaldo:      () => import('@/pages/Investimento/components/Section_Geral_saldo'),
      IISectionGeralComposicao: () => import('@/pages/Investimento/components/Section_Geral_composicoes'),
      IISectionGeralAnaliseAno: () => import('@/pages/Investimento/components/Section_Geral_analiseAno'),
    },

    data() {
      return {
        arrMeses: [],
        IIcarteiraSaldo: {
          time: false,
          labels: ['Val. Aplicado', 'Val. Atual', 'Mês Rend.', 'Mês Dividendos', 'Mês JSCP', 'Total Rend.', 'Total Dividendos', 'Total JSCP'],
          valores: [0,0,0,0,0,0,0]
        },
        II_carteiraAnaliseTipo: {
          time: false,
          INTP: {items: [], items_GRAFICO: {labels:[], valores:[]} },
          INAV: {items: [], items_GRAFICO: {labels:[], valores:[]} },
          INAT: {items: [], items_GRAFICO: {labels:[], valores:[]} },
        },
        IIanaliseValoresAno: {
          time: false,
          tipo: 'carteiraAno',
          dados: [],
          carteiraAno: [ {label: 'Carteira ano', valores: [0,0,0,0,0,0,0,0,0,0,0,0]} ],
          aporte:[ 
            {label: 'Aporte', valores: [0,0,0,0,0,0,0,0,0,0,0,0]},
            {label: 'Compra', valores: [0,0,0,0,0,0,0,0,0,0,0,0]},
            {label: 'Venda', valores: [0,0,0,0,0,0,0,0,0,0,0,0]} 
          ],
          rendimento: [ 
            {label: 'Mês', valores: [0,0,0,0,0,0,0,0,0,0,0,0]},
            {label: 'Ano', valores: [0,0,0,0,0,0,0,0,0,0,0,0]},
          ],
        },
      }
    },

    mounted () {
      this.arrMeses = service.arrMeses

      if( this.$store.getters.I_CarteiraPainel ){
        setTimeout(() => { this.getDados() }, service.timeLoading);
      }
    },

    methods: {
      getDados_IcarteiraSaldo()       { this.getDados('I_carteiraSaldo') },
      getDados_IcarteiraAnaliseAno()  { this.getDados('I_carteiraAno') },
      getDados_IcarteiraComposicoes() { this.getDados('I_carteiraAnaliseTipo') }, 

      // -- 
      
      initComponentes(tipo = '') {
        var arrMeses = [0,0,0,0,0,0,0,0,0,0,0,0];

        if(tipo == '' || tipo == 'I_carteiraSaldo'){
          this.IIcarteiraSaldo.time = false
          this.IIcarteiraSaldo.valores = [0,0,0,0,0,0,0]
        }
        if(tipo == '' || tipo == 'I_carteiraAnaliseTipo'){
          this.II_carteiraAnaliseTipo.time = false,
          this.II_carteiraAnaliseTipo.INTP =  {items: [], items_GRAFICO: {labels:[], valores:[]} }
          this.II_carteiraAnaliseTipo.INAV =  {items: [], items_GRAFICO: {labels:[], valores:[]} }
          this.II_carteiraAnaliseTipo.INAT =  {items: [], items_GRAFICO: {labels:[], valores:[]} }
        }
        if(tipo == '' || tipo == 'I_carteiraAno'){
          this.IIanaliseValoresAno.time = false
          this.IIanaliseValoresAno.carteiraAno[0].valores = arrMeses
          this.IIanaliseValoresAno.aporte[0].valores   = arrMeses
          this.IIanaliseValoresAno.aporte[1].valores      = arrMeses
          this.IIanaliseValoresAno.aporte[2].valores      = arrMeses
          this.IIanaliseValoresAno.rendimento[0].valores  = arrMeses
          this.IIanaliseValoresAno.rendimento[1].valores  = arrMeses
        }
      },

      makeUrl(tipo) {
        var url = ''
        url += 'componente';
        url += `?usuario=${this.$store.getters.USUA_ID}`
        url += `&INCT_ID=${this.$store.getters.I_INCT_ID}`
        url += `&dataAte=${this.$store.getters.Periodo}`;

        if(tipo == 'I_carteiraSaldo') {
          url += `&retorno=I_carteiraSaldo`;

        } else if(tipo == 'I_carteiraAnaliseTipo') {
          url += `&retorno=I_carteiraAnaliseTipo`;

        } else if(tipo == 'I_carteiraAno') {
          url += `&retorno=I_carteiraAno`;

        } else {
          url += `&retorno=I_carteiraSaldo|I_carteiraAnaliseTipo|I_carteiraAno`;
        }
        
        return url
      },

      getDados(tipo = '') {
        this.initComponentes(tipo)
        
        setTimeout( () => {
          service.busca( this.makeUrl(tipo) )
          .then( ({STATUS, data, msg}) => {
            if(STATUS == 'success'){
              const {I_carteiraSaldo, I_carteiraAnaliseTipo, I_carteiraAno} = data

              if(I_carteiraSaldo) {
                this.IIcarteiraSaldo.valores[0] = I_carteiraSaldo.TOTAL
                this.IIcarteiraSaldo.valores[1] = I_carteiraSaldo.BRUTO
                this.IIcarteiraSaldo.valores[2] = I_carteiraSaldo.MES_RENDIMENTO
                this.IIcarteiraSaldo.valores[3] = I_carteiraSaldo.MES_DIVIDENDO
                this.IIcarteiraSaldo.valores[4] = I_carteiraSaldo.MES_JSCP
                this.IIcarteiraSaldo.valores[5] = I_carteiraSaldo.TOTAL_RENDIMENTO
                this.IIcarteiraSaldo.valores[6] = I_carteiraSaldo.TOTAL_DIVIDENDO
                this.IIcarteiraSaldo.valores[7] = I_carteiraSaldo.TOTAL_JSCP
                this.IIcarteiraSaldo.time = true
              }
              
              if(I_carteiraAno) {
                this.IIanaliseValoresAno.carteiraAno[0].valores = I_carteiraAno.carteira
                this.IIanaliseValoresAno.aporte[0].valores      = I_carteiraAno.aporte
                this.IIanaliseValoresAno.aporte[1].valores      = I_carteiraAno.aporteCompra
                this.IIanaliseValoresAno.aporte[2].valores      = I_carteiraAno.aporteVenda
                this.IIanaliseValoresAno.rendimento[0].valores  = I_carteiraAno.rendimentoMes
                this.IIanaliseValoresAno.rendimento[1].valores  = I_carteiraAno.rendimentoAno
                this.IIanaliseValoresAno.time = false
                this.IIanaliseValoresAno.dados = this.IIanaliseValoresAno['carteiraAno']
              }

              if(I_carteiraAnaliseTipo) {
                this.II_carteiraAnaliseTipo.INTP = I_carteiraAnaliseTipo.INTP
                this.II_carteiraAnaliseTipo.INAV = I_carteiraAnaliseTipo.INAV
                this.II_carteiraAnaliseTipo.INAT = I_carteiraAnaliseTipo.INAT
                this.II_carteiraAnaliseTipo.time = true
              }
            }
            else if(STATUS == 'error'){
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: msg })
            }
            else if(STATUS == 'token'){
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: service.arrMessage })
              this.$store.commit('SET_LOGIN', false);
            }
            this.time = true
          })
        }, service.timeLoading)
      },
    },
  }
</script>