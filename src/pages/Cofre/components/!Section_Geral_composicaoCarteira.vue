<template>
  <PageSection>

    <PageContentTitle titulo="Composição">
      <template v-slot:btn>
        <ButtongetDados :getDados='getDados' />
      </template>
    </PageContentTitle>

    <div class="border over-x opacity-fetch" :class="time ? '' : 'opacity-fetch-active'" style="height: 225px">
      <table class="table table-sm">
        <thead>
          <tr>
            <th scope="col" class="th-proposito">Proposito</th>
            <th scope="col" class="th-valor">Entrada</th>
            <th scope="col" class="th-valor">Retirada</th>
            <th scope="col" class="th-valor">Saldo</th>
            <th scope="col" class="th-valor">Percentual</th>
          </tr>
        </thead>  

        <tbody v-if="registros.itemsTable.length > 0">
          <tr v-for="(item, i) in registros.itemsTable" :key="i">
            <td class="td-data"> {{ item.COIT_PROPOSITO }}</td>
            <td class="td-valor">{{ item.COIT_ENTRADA  | vReal }}</td>
            <td class="td-valor">{{ item.COIT_RETIRADA | vReal }}</td>
            <td class="td-valor">{{ item.COIT_SALDO    | vReal }}</td>
            <td class="td-valor">{{ item.COIT_PERCENTUAL }}</td>
          </tr>
        </tbody>

        <tbody v-else>
          <tr>
            <td colspan="5" class='text-black-50'>Não foi encontrato registros.</td>
          </tr>
        </tbody>
        
      </table>
    </div>

  </PageSection>
</template>

<script>
  const GRAFICO = {
    'labels': [],
    'valores': [],
    'percentual': [],
  };

  import service from '@/service.js'
  import {mapState} from 'vuex';

  export default {

    components: {
      ButtongetDados: () => import('@/components/Button_getDados'),
    },

    computed: { ...mapState(['cofre','periodo']) },
    
    data() {
      return {
        registros:{
          itemsTable: [],
          itemGrafico: GRAFICO,
        },
        time: false,
      }
    },

    mounted () {
      if( !this.$store.getters.C_CarteiraPainel ){
        this.getDados()
      }
    },

    methods: {
      getDados() {
        this.registros.itemsTable  = []
        this.registros.itemGrafico = GRAFICO
        this.time = false;

        setTimeout(() => {

          var endPoint = "";
          endPoint += "composicao-carteira";
          endPoint += `?usuario=${this.$store.getters.USUA_ID}`;
          endPoint += `&COCT_ID=${this.$store.getters.C_COCT_ID}`;
          
          service.cofre.busca(endPoint).then(({ STATUS, data, msg }) => {
            if (STATUS == "success") {
              var {consolidado, consolidadoGrafico} = data
              this.registros.itemsTable  = consolidado
              this.registros.itemGrafico = consolidadoGrafico

            } else if (STATUS == "error") {
              this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: msg });

            } else if (STATUS == "token") {
              this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: service.arrMessage, });
              this.$store.commit("SET_LOGIN", false);

            }

            this.time = true;
          });
        }, service.timeLoading);
      },

    },
    
    watch: {
      'cofre.COCT_ID'(newValue, oldValue) {
        this.getDados()
      },
      'periodo'(newValue, oldValue) {
        this.getDados()
      }
    },
  }
</script>

<style scoped>
  .th-valor ,
  .td-valor {
    min-width: 60px;
    text-align: center;
  }
  .th-proposito ,
  .td-proposito {
    min-width: 60px;
    text-align: center;
  }
</style>
