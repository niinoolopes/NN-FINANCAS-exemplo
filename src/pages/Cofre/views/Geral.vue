<template>
  <TemplateDefault title="Geral">

    <Alert v-if="$store.getters.C_CarteiraPainel" tipo="warning" texto="Selecione uma carteira no painel!"/>

    <!--
    <PageNav>
      <template v-slot:menu></template>
      <template v-slot:btn></template>
    </PageNav> 
    -->

    <PageSection>
      <CCSectionGeralSaldo 
        titulo='Cofre saldo'
        :getDados='getDados_CCarteiraSaldo'
        :time='C_carteiraSaldo.time'
        :labels='C_carteiraSaldo.labels'
        :valores='C_carteiraSaldo.valores'
      />
    </PageSection>

    <PageSection>
      <CCSectionGeralFluxoAno 
        titulo='Cofre fluxo ano'
        :getDados='getDados_CCarteiraGraficoAno'
        :time='carteiraGraficoAno.time'
        :dados='carteiraGraficoAno'
      />
    </PageSection>

    <PageSection class="d-flex">
      <Column class="col-12 col-lg-8 p-0 pr-lg-1">
          <PageContentTitle titulo="Composição" />

          <Table colspan='5' :timeTable='timeComposicao' :itemsTable='composicaoItems' >
            <template v-slot:thead>
              <th scope="col" class="th-proposito">Proposito</th>
              <th scope="col" class="th-valor">Entrada</th>
              <th scope="col" class="th-valor">Retirada</th>
              <th scope="col" class="th-valor">Saldo</th>
              <th scope="col" class="th-valor">Percentual</th>
            </template>
            <template v-slot:tbody>
              <tr v-for="(item, i) in composicaoItems" :key="i">
                <td class="td-data"> {{ item.COIT_PROPOSITO }}</td>
                <td class="td-valor">{{ item.COIT_ENTRADA  | vReal }}</td>
                <td class="td-valor">{{ item.COIT_RETIRADA | vReal }}</td>
                <td class="td-valor">{{ item.COIT_SALDO    | vReal }}</td>
                <td class="td-valor">{{ item.COIT_PERCENTUAL }}</td>
              </tr>
            </template>
          </Table>
      </Column>

      <Column class="col-12 col-md-4 p-0 pl-lg-1 d-none d-lg-block">
          <PageContentTitle titulo="">
            <template v-slot:btn>
              <ButtongetDados :getDados='getDados_CarteiraComposicao' border='0px' />
            </template>
          </PageContentTitle>

          <GraficoPie 
            :arrDados="composicaoGrafico" 
            :style="{height: '225px'}" 
          />
      </Column>
    </PageSection>

  </TemplateDefault>
</template>

<script>
  import service from '@/service.js'

  export default {
    components: { 
      ButtongetDados:        () => import('@/components/Button_getDados'),
      Table:                 () => import('@/components/Table'),
      GraficoPie:            () => import('@/components/Grafico/chat_Pie-cofre'),
      CCSectionGeralSaldo:   () => import('@/pages/Cofre/components/Section_Geral_saldo'),
      CCSectionGeralFluxoAno:() => import('@/pages/Cofre/components/Section_Geral_consolidadoAno'),
    },

    
    data() {
      return {
        C_carteiraSaldo: {
          time: false,
          labels: ['Entrada', 'Retirada', 'Saldo'],
          valores: [0,0,0,0]
        },
        
        arrMeses: [],
        carteiraGraficoAno: [
          {label: 'Entrada',    valores:[0,0,0,0,0,0,0,0,0,0,0,0]},
          {label: 'Retirada',  valores:[0,0,0,0,0,0,0,0,0,0,0,0]},
          {label: 'Saldo', valores:[0,0,0,0,0,0,0,0,0,0,0,0]},
        ],

        timeComposicao: false,
        composicaoItems: [],
        composicaoGrafico: {
          'labels': [],
          'valores': [],
          'percentual': [],
        },

      }
    },

    mounted () {
      this.arrMeses = service.arrMeses;

      if( this.$store.getters.I_CarteiraPainel ){
        this.initComponentes()

        setTimeout(() => { this.getDados() }, service.timeLoading);
      }
    },

    methods: {
      initComponentes(tipo = '') {

        if(tipo == '' || tipo == 'C_carteiraSaldo'){
          this.C_carteiraSaldo.time = false,
          this.C_carteiraSaldo.valores = [0,0,0,0]
        }
        if(tipo == '' || tipo == 'carteiraGraficoAno'){
          this.carteiraGraficoAno.time = false,
          this.carteiraGraficoAno[0].valores = [0,0,0,0,0,0,0,0,0,0,0,0]
          this.carteiraGraficoAno[1].valores = [0,0,0,0,0,0,0,0,0,0,0,0]
          this.carteiraGraficoAno[2].valores = [0,0,0,0,0,0,0,0,0,0,0,0]
        }
        if(tipo == '' || tipo == 'carteiraComposicao'){
          this.timeComposicao = false;
          this.composicaoItems = []
          this.composicaoGrafico = {
            'labels': [],
            'valores': [],
            'percentual': [],
          }
        }
      },

      getDados_CCarteiraSaldo() {
        this.getDados('C_carteiraSaldo')
      },

      getDados_CCarteiraGraficoAno() {
        this.getDados('carteiraGraficoAno')
      },

      getDados_CarteiraComposicao(){
        this.getDados('carteiraComposicao')
      },

      makeUrl(tipo) {
        var url = ''
        url += 'componente';
        url += `?usuario=${this.$store.getters.USUA_ID}`;
        url += `&dataAte=${this.$store.getters.Periodo}`;
        url += `&dataAno=${this.$store.getters.Periodo}`;
        url += `&COCT_ID=${this.$store.getters.C_COCT_ID}`;

        if(tipo == 'C_carteiraSaldo') {
          url += `&retorno=C_carteiraSaldo`;

        } else if(tipo == 'carteiraGraficoAno') {
          url += `&retorno=C_carteiraGraficoAno`;

        } else if(tipo == 'carteiraComposicao') {
          url += `&retorno=C_carteiraComposicao`;

        } else {
          url += `&retorno=C_carteiraSaldo|C_carteiraGraficoAno|C_carteiraComposicao`;
        }

        return url
      },

      getDados(tipo = '') {
        this.initComponentes(tipo)

        setTimeout( () => {
          service.busca( this.makeUrl(tipo) )
          .then( ({STATUS, data, msg}) => {
            if(STATUS == 'success'){
              const {C_carteiraSaldo, C_carteiraGraficoAno, C_carteiraComposicao} = data

              if(C_carteiraSaldo) {
                this.C_carteiraSaldo.valores = [C_carteiraSaldo.ENTRADA, C_carteiraSaldo.RETIRADA, C_carteiraSaldo.SALDO]
                this.C_carteiraSaldo.time = true;
              }

              if(C_carteiraGraficoAno) {
                this.carteiraGraficoAno[0].valores = C_carteiraGraficoAno.ENTRADA
                this.carteiraGraficoAno[1].valores = C_carteiraGraficoAno.RETIRADA
                this.carteiraGraficoAno[2].valores = C_carteiraGraficoAno.SALDO
              }

              if(C_carteiraComposicao){
                this.composicaoItems   = C_carteiraComposicao.items
                this.composicaoGrafico = C_carteiraComposicao.consolidadoGrafico
                this.timeComposicao = true;
              }
            }
            else if(STATUS == 'error'){
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: msg })
            }
            else if(STATUS == 'token'){
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: service.arrMessage })
              this.$store.commit('SET_LOGIN', false);
            }

          })
        }, service.timeLoading)
      },
    },
    
    watch: {
      '$store.getters.C_COCT_ID'(newValue, oldValue) {
        this.getDados()
      },
      '$store.getters.Periodo'(newValue, oldValue) {
        this.getDados()
      },
    },
  }
</script>