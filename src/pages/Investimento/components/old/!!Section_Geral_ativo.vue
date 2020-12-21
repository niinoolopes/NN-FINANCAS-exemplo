<template>
  <section class="w-100 mb-3">
    <PageContentTitle titulo="Consolidado por Ativo">
      <template v-slot:btn>
        <ButtongetDados :getDados='getDados' border='0px' />
      </template>
    </PageContentTitle>
  
    <Table colspan='8' :timeTable='time' :itemsTable='items' >
      <template v-slot:thead>
        <th scope="col" class="th-m-120 text-left" >Tipo</th>
        <th scope="col" class="th-m-85 text-right" >Aplicado</th>
        <th scope="col" class="th-m-85 text-right" >Bruto</th>
        <th scope="col" class="th-m-85 text-right" >Ganhos</th>
      </template>
      <template v-slot:tbody>
        <tr v-for="(item, i) in items" :key="i">
          <td class="td-m-120 text-left">{{item.DESCRICAO}}</td>
          <td class="td-m-85 text-right">{{item.TOTAL | vReal}}</td>
          <td class="td-m-85 text-right">{{item.BRUTO | vReal}}</td>
          <td class="td-m-85 text-right">{{ Number(item.TOTAL_DIVIDENDO) + Number(item.TOTAL_JSCP) + Number(item.TOTAL_RENDIMENTO) | vReal }}</td>
        </tr>
      </template>
    </Table>

    <!-- <div class="d-flex flex-wrap opacity-fetch" :class="time ? '' : 'opacity-fetch-active'">

      <div class="col-12 mb-3 mb-lg-0 col-lg-5 border over-x">
        <table class="table table-sm">
          <thead>
            <tr>
              <th scope="col" class="th-corretora">Corretora</th>
              <th scope="col" class="th-tipo">Tipo</th>
              <th scope="col" class="th-total-aplicado" width="125">Valor Aplicado</th>
              <th scope="col" class="th-total-bruto"    width="125">Valor Atual</th>
            </tr>
          </thead>

          <tbody v-if="items.length > 0">
            <tr v-for="(item, i) in items" :key="i">
              <td class="td-corretora">{{item.CORRETORA}}</td>
              <td class="td-corretora">{{item.DESCRICAO}}</td>
              <td class="td-total-aplicado">{{item.TOTAL | vReal}}</td>
              <td class="td-total-bruto">{{item.BRUTO | vReal}}</td>
            </tr>
          </tbody>

          <tbody v-else>
            <tr>
              <td colspan="16" class='text-black-50'>Não foi encontrato registros.</td>
            </tr>
          </tbody>

        </table>
      </div>

      
      <div class="d-none d-lg-block col-lg-7">
        <GraficoLine 
        :arrLabels="arrLabels"
        :arrDados="arrDados" 
        :styles="{
              height: (arrDados.length <= 2) ? '200px' : `${arrDados.length * 85}px`
          }"
        />
      </div>
        
    </div> -->

  </section>
</template>

<script>
  import service from '@/service.js'

  export default {
    components: {
      ButtongetDados: () => import('@/components/Button_getDados'),
      Table:          () => import('@/components/Table'),
      // GraficoLine:    () => import('@/components/Grafico/Bar-generico-vertical') , 
    },

    data() {
      return {
        time: false,
        items: [],
        arrLabels: [
          'Saldo Aplicado',
          'Saldo Atual',
        ],
        arrDados:  [{ label:[] }],
      }
    },

    mounted () {
      if( this.$store.getters.I_CarteiraPainel ){
        this.getDados()
      }
    },

    methods: {
      getDados() {
        this.time = false;
        this.items = []

        setTimeout( () => {

          var endPoint = '';
          endPoint += 'carteira-analise-ativo';
          endPoint += `?usuario=${this.$store.getters.USUA_ID}`;
          endPoint += `&dataAte=${this.$store.getters.Periodo}`;
          endPoint += `&INCT_ID=${this.$store.getters.I_INCT_ID}`;

          service.invest.busca(endPoint).then( ({STATUS, data, msg}) => {
            
            if(STATUS == 'success'){
              const {items, items_GRAFICO} = data
              this.items    = items
              this.arrDados = items_GRAFICO
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
      '$store.getters.I_INCT_ID'(newValue, oldValue) {
        this.getDados()
      },
      '$store.getters.Periodo'(newValue, oldValue) {
        this.getDados()
      },
    },
  }
</script>