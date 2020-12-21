<template>
  <TemplateDefault title="Análise ano">

    <Alert v-if="$store.getters.F_CarteiraPainel" tipo="warning" texto="Selecione uma carteira no painel para visualizar analise por meses!"/>

    <PageNav>
      <template v-slot:menu>
        <div class="nav nav-tabs d-flex over-x mb-1 mb-md-0" id="nav-tab" role="tablist">
          <a class="nav-item nav-link active" data-toggle="tab" @click="clickTipo('RECEITA')">Receita</a>
          <a class="nav-item nav-link"        data-toggle="tab" @click="clickTipo('DESPESA')">Despesa</a>
        </div>
      </template>
      <!-- <template v-slot:btn></template> -->
    </PageNav>

    <PageSection>
      <PageContentTitle :titulo="`Grupo`">
        <template v-slot:btn>
          <button 
            type="button" 
            class="btn-hover btn btn-sm btn-outline-info py-0 mr-2" 
            @click="getDados"><i class="fas fa-sync"></i></button>
        </template>
      </PageContentTitle>

      <div class="d-none d-lg-block">
        <GraficoBarV 
          FITP="1"
          :titulo="`Grupo`"
          :labels="FIGP_GRAFICO.labels"
          :valores="FIGP_GRAFICO.valores"
          :style="{
            height: ( FIGP_GRAFICO.labels.length <= 20 ) ? '200px' : '300px'
          }" 
        />
      </div>

      <br>

      <Table colspan='10' :timeTable='time' :itemsTable='FIGP' >
        <template v-slot:thead>
          <th class="th-m-120 text-left">Origem</th>
          <th class="th-m-120 text-right">Total</th>
          <th v-for="text in th" :key="text" class="th-m-120 text-right">{{text}}</th>
        </template>
        <template v-slot:tbody>
          <tr v-for="( arrValores , i ) in FIGP" :key="i">
            <td v-for="( text , j ) in arrValores" :key="j">
              <div v-if="j < 1"
                class="th-m-120 text-left">{{text}}</div>
              <div v-else 
                class="td-m-85 text-right" :class="text == 0 ? 'text-black-50' : ''">{{text | vReal}}</div>
            </td>
          </tr>
        </template>
      </Table>
    </PageSection>

    <PageSection>
      <PageContentTitle :titulo="`Categoria`" />

      <div class="d-none d-lg-block">
        <GraficoBarV 
          FITP="1"
          :titulo="`Categoria`"
          :labels="FICT_GRAFICO.labels"
          :valores="FICT_GRAFICO.valores"
          :style="{
            height: ( FICT_GRAFICO.labels.length <= 20 ) ? '200px' : '300px'
          }" 
        />
      </div>

      <br>

      <Table colspan='10' :timeTable='time' :itemsTable='FICT' >
        <template v-slot:thead>
          <th class="th-m-120 text-left">Origem</th>
          <th class="th-m-120 text-right">Total</th>
          <th v-for="text in th" :key="text" class="th-m-120 text-right">{{text}}</th>
        </template>
        <template v-slot:tbody>
          <tr v-for="(arrValores, i) in FICT" :key="i">
            <td v-for="( text , j ) in arrValores" :key="j">
              <div v-if="j < 1"
                class="th-m-120 text-left">{{text}}</div>
              <div v-else 
                class="td-m-85 text-right" :class="text == 0 ? 'text-black-50' : ''">{{text | vReal}}</div>
            </td>
          </tr>
        </template>
      </Table>
    </PageSection>

  </TemplateDefault>
</template>

<script>

  import service from "@/service.js"

  export default {
    components: { 
      Table:       () => import('@/components/Table'),
      GraficoBarV: () => import('@/components/Grafico/Financa/BarVertical'),
    },

    data() {
      return {
        time: false,
        th: service.arrMeses,
        FIGP: [],
        FIGP_GRAFICO: {labels: [], valores: []},
        FICT: [],
        FICT_GRAFICO: {labels: [], valores: []},
        dados: []
      }
    },

    mounted () {
      if( !this.$store.getters.F_CarteiraPainel ){
        setTimeout(() => { this.getDados() }, service.timeLoading);
      }
    },

    methods: {
      makeUrl() {
        var url = ''
        url += 'componente';
        url += `?usuario=${this.$store.getters.USUA_ID}`
        url += `&FINC_ID=${this.$store.getters.F_FINC_ID}`
        url += `&data=${this.$store.getters.Periodo}`;
        url += `&retorno=F_analiseAno`;
        return url
      },

      getDados() {
        setTimeout( () => {
          service.busca( this.makeUrl() )
          .then( ({STATUS, data, msg}) => {
            if(STATUS == 'success'){
              this.dados = data.F_analiseAno

              const {RECEITA} = data.F_analiseAno
              this.FIGP = RECEITA.FIGP
              this.FICT = RECEITA.FICT
              this.FIGP_GRAFICO.labels  = RECEITA.FIGP_GRAFICO.labels
              this.FIGP_GRAFICO.valores = RECEITA.FIGP_GRAFICO.valores
              this.FICT_GRAFICO.labels  = RECEITA.FICT_GRAFICO.labels
              this.FICT_GRAFICO.valores = RECEITA.FICT_GRAFICO.valores

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


      clickTipo(tipo){
        this.FIGP = this.dados[tipo].FIGP
        this.FICT = this.dados[tipo].FICT
        this.FIGP_GRAFICO.labels  = this.dados[tipo].FIGP_GRAFICO.labels
        this.FIGP_GRAFICO.valores = this.dados[tipo].FIGP_GRAFICO.valores
        this.FICT_GRAFICO.labels  = this.dados[tipo].FICT_GRAFICO.labels
        this.FICT_GRAFICO.valores = this.dados[tipo].FICT_GRAFICO.valores
      },
    },
  }
</script>