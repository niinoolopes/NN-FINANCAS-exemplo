<template>
  <TemplateDefault title="Painel">

    <Alert v-if="$store.getters.F_CarteiraPainel" tipo="warning" texto="Selecione uma carteira no painel para visualizar informações!"/>

    <PageSection>
      <FFSectionGeralSaldo
        :getDados='getDados_FcarteiraSaldo'
        :time='F_carteiraSaldo.time'
        :labels='F_carteiraSaldo.labels'
        :valores='F_carteiraSaldo.valores'
      />
      <br>  
      <FFSectionGeralConsolidadoAno
        titulo='Finança consolidado ano'
        :getDados='getDados_FconsolidadoAno'
        :consolidadoAno='F_consolidadoAno'
        :box='["grafico"]'
      />
    </PageSection>


    <PageSection>
      <CCSectionGeralSaldo 
        titulo='Cofre saldo'
        :getDados='getDados_CcarteiraSaldo'
        :time='C_carteiraSaldo.time'
        :labels='C_carteiraSaldo.labels'
        :valores='C_carteiraSaldo.valores'
      />
    
      <br>

      <CCSectionGeralFluxoAno 
        titulo='Cofre fluxo ano'
        :getDados='getDados_CcarteiraGraficoAno'
        :dados='C_carteiraGraficoAno'
      />
    </PageSection>


    <PageSection>
      <IISectionGeralSaldo
        :getDados='getDados_IcarteiraSaldo'
        :time='I_carteiraSaldo.time'
        :labels='I_carteiraSaldo.labels'
        :valores='I_carteiraSaldo.valores'
      />
      <br>
      <IISectionGeralAnaliseAno 
        :getDados='getDados_IcarteiraAnaliseAno'
        :arrDados='I_analiseValoresAno'
      />
    </PageSection>

  </TemplateDefault>
</template>

<script>
  import service from "@/service.js"

  export default {
    components: { 
      // ButtongetDados: () => import('@/components/Button_getDados'),
      // Cards:          () => import('@/components/Cards'),

      FFSectionGeralSaldo:          () => import('@/pages/Financa/components/Section_Geral_saldo'),
      FFSectionGeralConsolidadoAno: () => import('@/pages/Financa/components/Section_Geral_consolidadoAno'),

      CCSectionGeralSaldo:    () => import('@/pages/Cofre/components/Section_Geral_saldo'),
      CCSectionGeralFluxoAno: () => import('@/pages/Cofre/components/Section_Geral_consolidadoAno'),

      IISectionGeralSaldo:          () => import('@/pages/Investimento/components/Section_Geral_saldo'),
      IISectionGeralAnaliseAno: () => import('@/pages/Investimento/components/Section_Geral_analiseAno'),
    },
 
    data() {
      return {
        F_carteiraSaldo: {
          time: false,
          labels: ['Receita', 'Despesa', 'Sobre', 'Estimado'],
          valores: [0,0,0,0]
        },
        F_consolidadoAno: {
          time: false,
          itemsTable: 'init',
          itemsGrafico: [
            {label: 'RECEITA',  valores:[0,0,0,0,0,0,0,0,0,0,0,0]},
            {label: 'DESPESA',  valores:[0,0,0,0,0,0,0,0,0,0,0,0]},
            {label: 'SOBRA',    valores:[0,0,0,0,0,0,0,0,0,0,0,0]},
            {label: 'ESTIMADO', valores:[0,0,0,0,0,0,0,0,0,0,0,0]}
          ],
        },

        C_carteiraSaldo: {
          time: false,
          labels: ['Entrada', 'Retirada', 'Saldo'],
          valores: [0,0,0,0]
        },
        C_carteiraGraficoAno: [
          {label: 'SALDO',    valores:[0,0,0,0,0,0,0,0,0,0,0,0]},
          {label: 'ENTRADA',  valores:[0,0,0,0,0,0,0,0,0,0,0,0]},
          {label: 'RETIRADA', valores:[0,0,0,0,0,0,0,0,0,0,0,0]},
        ],

        I_carteiraSaldo: {
          time: false,
          labels: ['Val. Aplicado', 'Val. Atual', 'Mês Rend.', 'Mês Dividendos', 'Mês JSCP', 'Total Rend.', 'Total Dividendos', 'Total JSCP'],
          valores: [0,0,0,0]
        },
        I_analiseValoresAno: {
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
      if( !this.$store.getters.F_CarteiraPainel ){
        setTimeout(() => { this.getDados() }, service.timeLoading);
      }
    },

    methods: {
      initComponentes(tipo = '') {
        var arrMeses = [0,0,0,0,0,0,0,0,0,0,0,0];

        if(tipo == '' || tipo == 'F_carteiraSaldo'){
          this.F_carteiraSaldo.time = false,
          this.F_carteiraSaldo.valores = [0,0,0,0]
        }
        if(tipo == '' || tipo == 'F_consolidadoAno'){
          this.F_consolidadoAno = {
            time: false,
            itemsTable: 'init',
            itemsGrafico: [
              {label: 'RECEITA',  valores:[0,0,0,0,0,0,0,0,0,0,0,0]},
              {label: 'DESPESA',  valores:[0,0,0,0,0,0,0,0,0,0,0,0]},
              {label: 'SOBRA',    valores:[0,0,0,0,0,0,0,0,0,0,0,0]},
              {label: 'ESTIMADO', valores:[0,0,0,0,0,0,0,0,0,0,0,0]}
            ],
          }
        }
        if(tipo == '' || tipo == 'C_carteiraSaldo'){
          this.C_carteiraSaldo.time = false,
          this.C_carteiraSaldo.valores = [0,0,0,0]
        }
        if(tipo == '' || tipo == 'C_carteiraGraficoAno'){
          this.C_carteiraGraficoAno.time = false
          this.C_carteiraGraficoAno[0].valores = arrMeses
          this.C_carteiraGraficoAno[1].valores = arrMeses
          this.C_carteiraGraficoAno[2].valores = arrMeses
        }
        if(tipo == '' || tipo == 'I_carteiraSaldo'){
          this.I_carteiraSaldo.time = false
          this.I_carteiraSaldo.valores = [0,0,0,0,0,0,0,0]
        }
        if(tipo == '' || tipo == 'I_carteiraAno'){
          this.I_analiseValoresAno.time = false
          this.I_analiseValoresAno.carteiraAno[0].valores = arrMeses
          this.I_analiseValoresAno.aporte[0].valores   = arrMeses
          this.I_analiseValoresAno.aporte[1].valores   = arrMeses
          this.I_analiseValoresAno.aporte[2].valores   = arrMeses
          this.I_analiseValoresAno.rendimento[0].valores  = arrMeses
          this.I_analiseValoresAno.rendimento[1].valores  = arrMeses
        }
      },
      
      // --

      getDados_FcarteiraSaldo()      { this.getDados('F_carteiraSaldo') },
      getDados_FconsolidadoAno()     { this.getDados('F_consolidadoAno') },
      getDados_CcarteiraSaldo()      { this.getDados('C_carteiraSaldo') },
      getDados_CcarteiraGraficoAno() { this.getDados('C_carteiraGraficoAno') },
      getDados_IcarteiraSaldo()      { this.getDados('I_carteiraSaldo') },
      getDados_IcarteiraAnaliseAno() { this.getDados('I_carteiraAno') },

      // -- 

      makeUrl(tipo) {
        var url = ''
        url += 'componente';
        url += `?usuario=${this.$store.getters.USUA_ID}`
        url += `&FINC_ID=${this.$store.getters.F_FINC_ID}`
        url += `&COCT_ID=${this.$store.getters.C_COCT_ID}`;
        url += `&INCT_ID=${this.$store.getters.I_INCT_ID}`
        url += `&data=${this.$store.getters.Periodo}`;
        url += `&dataAno=${this.$store.getters.Periodo}`;
        url += `&dataAte=${this.$store.getters.Periodo}`;

        if(tipo == 'F_carteiraSaldo') {
          url += `&retorno=F_carteiraSaldo`

        } else if(tipo == 'F_consolidadoAno') {
          url += `&retorno=F_anoConsolidado`

        } else if(tipo == 'C_carteiraSaldo') {
          url += `&retorno=C_carteiraSaldo`

        } else if(tipo == 'C_carteiraGraficoAno') {
          url += `&retorno=C_carteiraGraficoAno`;

        } else if(tipo == 'I_carteiraSaldo') {
          url += `&retorno=I_carteiraSaldo`;
          
        } else if(tipo == 'I_carteiraAno') {
          url += `&retorno=I_carteiraAno`;

        } else {
          url += `&retorno=F_carteiraSaldo|F_anoConsolidado|C_carteiraSaldo|C_carteiraGraficoAno|I_carteiraSaldo|I_carteiraAno`
        }
        return url
      },

      getDados(tipo = '') {
        this.initComponentes(tipo)

        setTimeout( () => {
          service.busca( this.makeUrl(tipo) )
          .then( ({STATUS, data, msg}) => {
            if(STATUS == 'success'){
              const {F_carteiraSaldo, F_anoConsolidado, C_carteiraSaldo, C_carteiraGraficoAno, I_carteiraSaldo, I_carteiraAno} = data

              if(F_carteiraSaldo) {
                this.F_carteiraSaldo.valores[0] = F_carteiraSaldo.RECEITA
                this.F_carteiraSaldo.valores[1] = F_carteiraSaldo.DESPESA
                this.F_carteiraSaldo.valores[2] = F_carteiraSaldo.SOBRA
                this.F_carteiraSaldo.valores[3] = F_carteiraSaldo.ESTIMADO
                this.F_carteiraSaldo.time = true
              }
              if(F_anoConsolidado) {
                this.F_consolidadoAno.itemsTable = F_anoConsolidado.items
                this.F_consolidadoAno.itemsGrafico[0].valores = F_anoConsolidado.itemsGrafico.RECEITA
                this.F_consolidadoAno.itemsGrafico[1].valores = F_anoConsolidado.itemsGrafico.DESPESA
                this.F_consolidadoAno.itemsGrafico[2].valores = F_anoConsolidado.itemsGrafico.SOBRA
                this.F_consolidadoAno.itemsGrafico[2].valores = F_anoConsolidado.itemsGrafico.ESTIMADO
                this.F_consolidadoAno.time = true
              }
              
              if(C_carteiraSaldo) {
                this.C_carteiraSaldo.valores = [C_carteiraSaldo.ENTRADA, C_carteiraSaldo.RETIRADA, C_carteiraSaldo.SALDO]
                this.C_carteiraSaldo.time = true
              }
              if(C_carteiraGraficoAno) {
                this.C_carteiraGraficoAno[0].valores = C_carteiraGraficoAno.ENTRADA
                this.C_carteiraGraficoAno[1].valores = C_carteiraGraficoAno.RETIRADA
                this.C_carteiraGraficoAno[2].valores = C_carteiraGraficoAno.SALDO
              }

              if(I_carteiraSaldo) {
                this.I_carteiraSaldo.valores[0] = I_carteiraSaldo.TOTAL
                this.I_carteiraSaldo.valores[1] = I_carteiraSaldo.BRUTO
                this.I_carteiraSaldo.valores[2] = I_carteiraSaldo.MES_RENDIMENTO
                this.I_carteiraSaldo.valores[3] = I_carteiraSaldo.MES_DIVIDENDO
                this.I_carteiraSaldo.valores[4] = I_carteiraSaldo.MES_JSCP
                this.I_carteiraSaldo.valores[5] = I_carteiraSaldo.TOTAL_RENDIMENTO
                this.I_carteiraSaldo.valores[6] = I_carteiraSaldo.TOTAL_DIVIDENDO
                this.I_carteiraSaldo.valores[7] = I_carteiraSaldo.TOTAL_JSCP
                this.I_carteiraSaldo.time = true
              }
              if(I_carteiraAno) {
                this.I_analiseValoresAno.carteiraAno[0].valores = I_carteiraAno.carteira
                this.I_analiseValoresAno.aporte[0].valores      = I_carteiraAno.aporte
                this.I_analiseValoresAno.aporte[1].valores      = I_carteiraAno.aporteCompra
                this.I_analiseValoresAno.aporte[2].valores      = I_carteiraAno.aporteVenda
                this.I_analiseValoresAno.rendimento[0].valores  = I_carteiraAno.rendimentoMes
                this.I_analiseValoresAno.rendimento[1].valores  = I_carteiraAno.rendimentoAno
                this.I_analiseValoresAno.time = false
                this.I_analiseValoresAno.dados = this.I_analiseValoresAno['carteiraAno']
              }
            } else if(STATUS == 'error'){
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: msg })

            } else if(STATUS == 'token'){
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: service.arrMessage })
              this.$store.commit('SET_LOGIN', false);
            }
            this.time = true
          })
        }, service.timeLoading)
      },
    },

    watch: {
      '$store.getters.F_FINC_ID'(newValue, oldValue) {
        this.getDados()
      },
      '$store.getters.Periodo'(newValue, oldValue) {
        this.getDados()
      }
    },
  }
</script>