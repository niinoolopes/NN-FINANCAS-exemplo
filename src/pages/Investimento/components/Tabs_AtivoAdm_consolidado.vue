<template>
  <section class="w-100">
    <PageSection>
      <IISectionGeralSaldo
        titulo='Saldo ativo'
        :getDados='getDados_IAtivoSaldo'
        :time='IIcarteiraSaldo.time'
        :labels='IIcarteiraSaldo.labels'
        :valores='IIcarteiraSaldo.valores'
      />
      <br>

      <IISectionGeralAnaliseAno 
        :getDados='getDados_IAtivoAnaliseAno'
        :arrDados='IIanaliseValoresAno'
      />
    </PageSection>

    <PageSection>
      <PageContentTitle titulo="Consolidado por Corretoras">
        <template v-slot:btn>
          <ButtongetDados :getDados='getDados_IAtivoComposicoes' border='0px' />
        </template>
      </PageContentTitle>
      
      <FiltroItem :itemsFiltro='itemsFiltro' :setFiltro='setFiltro' />

      <article v-for="ativo in arrDadosCorretora" :key="`${ativo.INCR_DESCRICAO}-geral`" class="mb-3 p-2 border">
        <p>Corretora: <b>{{ativo.INCR_DESCRICAO}}</b></p>

        <div class="d-flex flex-wrap">
          <Column v-for='(e, i) in ativo.dados' :key='i' class="col-12 col-md-4 col-xl-3">
            <div class="p-1 bg-light border rounded" style="height: 100%">
              <p :class="textTitulo">{{e.tituloDesc}}</p>

              <p v-for='(j,k) in e.dados' :key='k' class="w-100 m-0 d-flex align-items-center">
                <span :class="textLeft">{{j.desc}}</span>
                <span :class="textRight"><strong>{{j.value}}</strong> </span>
              </p>
            </div>
          </Column>
        </div>

      </article>

      <template v-if='arrLucroVenda.length > 0'>
        <hr>

        <PageContentTitle titulo="Saldo 'lucro venda'" />

        <article v-for="ativo in arrLucroVenda" :key="`${ativo.INCR_DESCRICAO}-lucro-venda`" class="mb-3 p-2 border">
          <p>Corretora <b>{{ativo.INCR_DESCRICAO}}</b></p>

          <div class="d-flex flex-wrap">
            <Column v-for='(e,i) in ativo.dados' :key='i' class="col-6 col-md-3 col-xl-3">
              <div class="p-1 bg-light border rounded" style="height: 100%">
                <p class="m-0">Data: <strong>{{e.INOD_DATA | convertDate}}</strong></p>
                <p class="m-0">Saldo: <strong>{{e.LUCRO_VENDA}}</strong></p>
                <p class="m-0">IR Darf: <strong>{{e.IR}}</strong></p>
              </div>
            </Column>
          </div>
        </article>
      </template>
      
    </PageSection>

  </section>
</template>

<script>
  import service from '@/service.js'

  export default {
    props: ['INAV_ID'],

    components: {
      ButtongetDados: () => import('@/components/Button_getDados'),
      FiltroItem:     () => import('@/pages/Investimento/components/Section_filtro_item'),

      IISectionGeralSaldo:      () => import('@/pages/Investimento/components/Section_Geral_saldo'),
      IISectionGeralAnaliseAno: () => import('@/pages/Investimento/components/Section_Geral_analiseAno'),
    },

    data() {
      return {
        textTitulo:'col-12 px-1 my-1 text-left font-weight-normal border-bottom',
        textLeft:  'col-6A  px-1 my-1 text-left small text-black-50',
        textRight: 'col  px-1 my-1 text-right',

        time: false,
        items: [],
        itemsTable: [],
        itemsFiltro: [],
        arrDadosCorretora: [],
        arrLucroVenda: [],
        
        IIcarteiraSaldo: {
          time: false,
          labels: ['Val. Aplicado', 'Val. Atual', 'Mês Rend.', 'Mês Dividendos', 'Mês JSCP', 'Total Rend.', 'Total Dividendos', 'Total JSCP'],
          valores: [0,0,0,0,0,0,0]
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
      if( this.$store.getters.I_CarteiraPainel ){
        setTimeout(() => { this.getDados() }, service.timeLoading);
      }
    },

    methods: {
      getDados_IAtivoSaldo()       { this.getDados('I_carteiraSaldo') },
      getDados_IAtivoAnaliseAno()  { this.getDados('I_carteiraAno') },
      getDados_IAtivoComposicoes() { this.getDados('I_carteiraComposicao') }, 

      // --
      
      initComponentes(tipo = '') {
        var arrMeses = [0,0,0,0,0,0,0,0,0,0,0,0];

        if(tipo == '' || tipo == 'I_carteiraSaldo'){
          this.IIcarteiraSaldo.valores = [0,0,0,0,0,0,0]
          this.IIcarteiraSaldo.time = false
        }
        if(tipo == '' || tipo == 'I_carteiraAno'){
          this.IIanaliseValoresAno.time = false
          this.IIanaliseValoresAno.carteiraAno[0].valores = arrMeses
          this.IIanaliseValoresAno.aporte[0].valores      = arrMeses
          this.IIanaliseValoresAno.aporte[1].valores      = arrMeses
          this.IIanaliseValoresAno.aporte[2].valores      = arrMeses
          this.IIanaliseValoresAno.rendimento[0].valores  = arrMeses
          this.IIanaliseValoresAno.rendimento[1].valores  = arrMeses
        }
        if(tipo == '' || tipo == 'I_carteiraComposicao'){
          this.time = false
          this.items = []
          this.itemsTable = []
          this.itemsFiltro = {}
            
          this.arrDadosCorretora = [];
          this.arrLucroVenda = [];

        }
      },
      
      makeUrl(tipo) {
        var url = ''
        url += 'componente'
        url += `?usuario=${this.$store.getters.USUA_ID}`
        url += `&dataAte=${this.$store.getters.Periodo}`;
        url += `&INCT_ID=${this.$store.getters.I_INCT_ID}`
        url += `&INAV_ID=${this.INAV_ID}`

        if(tipo == 'I_carteiraSaldo') {
          url += `&retorno=I_carteiraSaldo`;

          } else if(tipo == 'I_carteiraAno') {
            url += `&retorno=I_carteiraAno`;

        } else if(tipo == 'I_carteiraComposicao') {
          url += `&retorno=I_carteiraComposicao`;

        } else {
          url += '&retorno=I_carteiraSaldo|I_carteiraAno|I_carteiraComposicao'
        }
        
        return url
      },

      getDados(tipo = '') {
        this.initComponentes(tipo)

        service.busca( this.makeUrl(tipo) )
        .then( ({STATUS, data, msg}) => {
          if(STATUS == 'success'){
            let {I_carteiraComposicao, I_carteiraSaldo, I_carteiraAno} = data
            if(I_carteiraComposicao){
              const {items, itemsFiltro} = I_carteiraComposicao
              this.items = items
              this.itemsTable = items
              this.itemsFiltro = itemsFiltro
              this.renderDados()
            }
            if(I_carteiraSaldo){
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
            if(I_carteiraAno){
              this.IIanaliseValoresAno.carteiraAno[0].valores = I_carteiraAno.carteira
              this.IIanaliseValoresAno.aporte[0].valores      = I_carteiraAno.aporte
              this.IIanaliseValoresAno.aporte[1].valores      = I_carteiraAno.aporteCompra
              this.IIanaliseValoresAno.aporte[2].valores      = I_carteiraAno.aporteVenda
              this.IIanaliseValoresAno.rendimento[0].valores  = I_carteiraAno.rendimentoMes
              this.IIanaliseValoresAno.rendimento[1].valores  = I_carteiraAno.rendimentoAno
              this.IIanaliseValoresAno.time = false
              this.IIanaliseValoresAno.dados = this.IIanaliseValoresAno['carteiraAno']
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
      },

      apuraPercentual (ativo){
        var op = 0;
        var bruto    = Number(ativo.BRUTO)
        var aplicado = Number(ativo.TOTAL)

        if( bruto > 0 && aplicado > 0) {
          op = ((bruto / aplicado) - 1)  * 100
          op = Number(op.toFixed(2))
        } 

        return `${op}%`
      },

      setFiltro(campos) {
        this.itemsTable = service.invest.filtroItem(campos, this.items)
        this.renderDados()
      },

      renderDados() {
        this.arrDadosCorretora = [];
        this.arrLucroVenda = [];

        this.itemsTable.map( e => {
          if(e.H_LUCRO_VENDA.length){
            this.arrLucroVenda.push({
              INCR_DESCRICAO:  e.INCR_DESCRICAO,
              dados: e.H_LUCRO_VENDA
            })
          }
          this.arrDadosCorretora.push({
            INCR_DESCRICAO:  e.INCR_DESCRICAO,
            dados: [
              {
                tituloDesc: 'Valores',
                dados:[
                  { desc: 'Valor Aplicado'    , value: e.TOTAL },
                  { desc: 'Valor Bruto'       , value: e.BRUTO },
                  { desc: 'Cotas'             , value: e.COTAS },
                  { desc: 'Preço médio'       , value: e.PRECO_MEDIO },
                  { desc: 'Ultíma cotação'    , value: e.PRECO_COTACAO },
                  { desc: 'Valorização cota'  , value: e.VALORIZACAO_UNIDADE },
                  { desc: 'Valorização total' , value: e.VALORIZACAO_TOTAL },
                ]
              },
              {
                tituloDesc: 'Lucro Venda',
                dados:[
                  { desc: 'Valor Compra'      , value: e.TOTAL_COMPRA },
                  { desc: 'Valor Venda'       , value: e.TOTAL_VENDA  },
                  { desc: 'Cotas Compra'      , value: e.COTAS_COMPRA },
                  { desc: 'Cotas Venda'       , value: e.COTAS_VENDA  },
                  { desc: 'Lucro Venda Saldo' , value: e.LUCRO_VENDA  },
                ]
              },
              {
                tituloDesc: 'Rendimentos',
                dados:[
                  { desc: 'Total Dividendo'  , value: e.TOTAL_DIVIDENDO  },
                  { desc: 'Total JSCP'       , value: e.TOTAL_JSCP       },
                  { desc: 'Total Rendimento' , value: e.TOTAL_RENDIMENTO },
                  { desc: 'Mês Dividendo'    , value: e.MES_DIVIDENDO    },
                  { desc: 'Mês JSCP'         , value: e.MES_JSCP         },
                  { desc: 'Mês Rendimento'   , value: e.MES_RENDIMENTO   },
                ]
              },
              {
                tituloDesc: 'Valores Históricos',
                dados:[
                  { desc: 'Total Compra' , value: e.H_TOTAL_COMPRA },
                  { desc: 'Total Venda'  , value: e.H_TOTAL_VENDA  },
                  { desc: 'Cotas Compra' , value: e.H_COTAS_COMPRA },
                  { desc: 'Cotas Venda'  , value: e.H_COTAS_VENDA  },
                ]
              }
            ]
          })
        })
      },

    },
    
    // watch: {
    //   'investimento.INCT_ID'(newValue, oldValue) {
    //     this.getDados()
    //   },
    //   'periodo'(newValue, oldValue) {
    //     this.getDados()
    //   }
    // },
  }
</script>