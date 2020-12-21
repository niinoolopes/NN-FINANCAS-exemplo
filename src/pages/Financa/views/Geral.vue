<template>
  <TemplateDefault title="Geral">

    <Alert v-if="$store.getters.F_CarteiraPainel" tipo="warning" texto="Selecione uma carteira no painel para visualizar informações gerais!"/>

    <PageSection>
      <FFSectionGeralSaldo
        :getDados='getDados_FcarteiraSaldo'
        :time='carteiraSaldo.time'
        :labels='carteiraSaldo.labels'
        :valores='carteiraSaldo.valores'
      />
    </PageSection>


    <PageSection>
      <PageContentTitle titulo='Receitas Mês' >
        <template v-slot:btn>
          <ButtongetDados :getDados='getDados_Fgeral' border='0px' />
        </template>
      </PageContentTitle>

      <div class="pt-2 d-flex flex-wrap opacity-fetch" :class="geral.time ? '' : 'opacity-fetch-active'">
        <Column class="col-12 col-md-6 over-y over-x">
          <TableGeral :time='geral.time' :items='geral.RECEITA.FIGP' descricao='Receita grupo' />
        </Column>

        <Column class="col-12 col-md-6 over-y over-x">
          <TableGeral :time='geral.time' :items='geral.RECEITA.FICT' descricao='Receita categoria' />
        </Column>

        <Column class="col-12 col-md-6 over-y over-x">
          <TableGeral :time='geral.time' :items='geral.DESPESA.FIGP' descricao='Despesa grupo' />
        </Column>

        <Column class="col-12 col-md-6 over-y over-x">
          <TableGeral :time='geral.time' :items='geral.DESPESA.FICT' descricao='Despesa categoria' />
        </Column>
      </div>

      <!-- <PageContentTitle titulo='Despesas Mês' >
      </PageContentTitle>

      <div class="pt-2 d-flex flex-wrap over-y opacity-fetch" :class="geral.time ? '' : 'opacity-fetch-active'">
        <Column class="col-12 col-lg-7 over-x">
          < !-- <TableGeral :tr="trs" /> -- >
          <Table colspan='6' :timeTable='geral.time' :itemsTable='geral.DESPESA.itemsTable' >
            <template v-slot:thead>
              <th class="text-left " scope="col">Descrição</th>
              <th class="text-right" scope="col">Total</th>
              <th class="text-right" scope="col">%</th>
              <th class="text-right" scope="col">Pago</th>
              <th class="text-right" scope="col">Pendente</th>
              <th class="text-right" scope="col">Talvez</th>
            </template>
            <template v-slot:tbody> 
              <tr v-for="(item,i) in geral.DESPESA.itemsTable" :key="i">
                <td class="text-left ">{{item.DESCRICAO}}</td>
                <td class="text-right">{{item.TOTAL | vReal}}</td>
                <td class="text-right">{{item.PERCENTUAL}}</td>
                <td class="text-right">{{item.PAGO | vReal}}</td>
                <td class="text-right">{{item.PENDENTE | vReal}}</td>
                <td class="text-right">{{item.TALVEZ | vReal}}</td>
              </tr>
            </template>
          </Table>
        </Column>

        <Column class="d-none d-lg-block col-lg-5">
          <GraficoBar 
            :labels="geral.DESPESA.itemsGrafico.labels"
            :valores="geral.DESPESA.itemsGrafico.valores"
            :tipo="'Receita'"
            :title="`Receita por Grupo`"
            :styles="{
                height: (geral.DESPESA.itemsGrafico.labels.length < 5) ? '175px' : `${geral.DESPESA.itemsGrafico.labels.length * 35}px`
            }" 
          />
        </Column> 
      </div> -->
    </PageSection>


    <PageSection>
      <FFSectionGeralConsolidadoAno
        titulo='Consolidado Ano'
        :getDados='getDados_FconsolidadoAno'
        :consolidadoAno='consolidadoAno'
        :box='["tabela","grafico"]'
      />
    </PageSection>

  </TemplateDefault>
</template>

<script>
  import service from "@/service.js"

  export default {
    components: { 
      TableGeral:                   () => import('@/pages/Financa/components/Table_Geral'),
      FFSectionGeralSaldo:          () => import('@/pages/Financa/components/Section_Geral_saldo'),
      FFSectionGeralConsolidadoAno: () => import('@/pages/Financa/components/Section_Geral_consolidadoAno'),
    },

    data() {
      return {
        arrMeses: [],
        carteiraSaldo: {
          time: false,
          labels: ['Receita', 'Despesa', 'Sobre', 'Estimado'],
          valores: [0,0,0,0]
        },
        consolidadoAno: {
          time: false,
          itemsTable: 'init',
          itemsGrafico: [
            {label: 'RECEITA',  valores:[0,0,0,0,0,0,0,0,0,0,0,0]},
            {label: 'DESPESA',  valores:[0,0,0,0,0,0,0,0,0,0,0,0]},
            {label: 'SOBRA',    valores:[0,0,0,0,0,0,0,0,0,0,0,0]},
            {label: 'ESTIMADO', valores:[0,0,0,0,0,0,0,0,0,0,0,0]}
          ],
        },
        geral: {
            time: false,
            RECEITA: [],
            DESPESA: [],
          }
      }
    },
    
    created () {
      this.arrMeses = service.arrMeses;
    },

    mounted () {
      if( !this.$store.getters.F_CarteiraPainel ){
        setTimeout(() => { this.getDados() }, service.timeLoading);
      }
    },

    methods: {
      getDados_FcarteiraSaldo() { this.getDados('carteiraSaldo') },
      getDados_FconsolidadoAno() { this.getDados('consolidadoAno') },
      getDados_Fgeral() { this.getDados('geral') },

      // -- 

      initComponentes(tipo = '') {
        if(tipo == '' || tipo == 'carteiraSaldo'){
          this.carteiraSaldo.time = false,
          this.carteiraSaldo.valores = [0,0,0,0]
        }
        if(tipo == '' || tipo == 'consolidadoAno'){
          this.consolidadoAno = {
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
        if(tipo == '' || tipo == 'geral'){
          this.geral = {
            time: false,
            RECEITA: [],
            DESPESA: [],
          }
        }
      },
      
      // -- 

      makeUrl(tipo) {
        var url = ''
        url += 'componente';
        url += `?usuario=${this.$store.getters.USUA_ID}`
        url += `&FINC_ID=${this.$store.getters.F_FINC_ID}`
        url += `&data=${this.$store.getters.Periodo}`;
        url += `&dataAno=${this.$store.getters.Periodo}`;

        if(tipo == 'carteiraSaldo') {
          url += `&retorno=F_carteiraSaldo`;

        } else if(tipo == 'consolidadoAno') {
          url += `&retorno=F_anoConsolidado`;

        } else if(tipo == 'geral') {
          url += `&retorno=F_mesGeral`;

        } else {
          url += `&retorno=F_carteiraSaldo|F_anoConsolidado|F_mesGeral`;
        }

        return url
      },

      getDados(tipo = '') {
        this.initComponentes(tipo)

        setTimeout( () => {
          service.busca( this.makeUrl(tipo) )
          .then( ({STATUS, data, msg}) => {
            if(STATUS == 'success'){
              const {F_carteiraSaldo, F_anoConsolidado, F_mesGeral} = data

              if(F_carteiraSaldo) {
                this.carteiraSaldo.valores[0] = F_carteiraSaldo.RECEITA
                this.carteiraSaldo.valores[1] = F_carteiraSaldo.DESPESA
                this.carteiraSaldo.valores[2] = F_carteiraSaldo.SOBRA
                this.carteiraSaldo.valores[3] = F_carteiraSaldo.ESTIMADO
                this.carteiraSaldo.time = true
              }
              
              if(F_anoConsolidado) {
                this.consolidadoAno.itemsTable = F_anoConsolidado.items
                this.consolidadoAno.itemsGrafico[0].valores = F_anoConsolidado.itemsGrafico.RECEITA
                this.consolidadoAno.itemsGrafico[1].valores = F_anoConsolidado.itemsGrafico.DESPESA
                this.consolidadoAno.itemsGrafico[2].valores = F_anoConsolidado.itemsGrafico.SOBRA
                this.consolidadoAno.itemsGrafico[2].valores = F_anoConsolidado.itemsGrafico.ESTIMADO
                this.consolidadoAno.time = true
              }
              
              if(F_mesGeral) {
                this.geral.RECEITA = F_mesGeral.RECEITA
                this.geral.DESPESA = F_mesGeral.DESPESA
                this.geral.time = true
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

<style scoped>
  .td-1 {
    color: rgba(  40, 160, 70, 0.9);
    font-size: unset;
  }
  .td-2 {
    color: rgba(220, 50, 75, 0.9);
    font-size: unset;
  }
  .td-3 {
    color: rgba(100, 100, 100, 0.9);
    font-size: unset;
  }
  .td-4 {
    color: rgba(25, 125, 200, 0.9);
    font-size: unset;
  }
</style>
