<template>
  <PageSection class="opacity-fetch" :class="time ? '' : 'opacity-fetch-active'">

    <!-- <PageContentTitle titulo="Consolidado">
      <template v-slot:btn>
        <ButtongetDados :getDados='getDados' />
      </template>
    </PageContentTitle>

    <Cards :time="time" :labels="labels" :valores="valores" /> -->

  </PageSection>
</template>

<script>
  // import {mapState} from 'vuex';
  // import service from '@/service.js'

  export default {
    // computed: { ...mapState(['cofre','periodo']) },

    components: {
      // ButtongetDados: () => import('@/components/Button_getDados'),
      // Cards:          () => import('@/components/Cards'),
    },

    data() {
      return {
        time: false,
        labels: ['ENTRADA', 'RETIRADA', 'SALDO'],
        valores: [0,0,0],
      }
    },

    mounted () {
      // if( !this.$store.getters.C_CarteiraPainel ){
        // this.getDados()
      // };
    },

    // methods: {
    //   getDados() {
    //     this.time = false;
    //     this.valores = [0,0,0]

    //     setTimeout(() => {

    //       var endPoint = "";
    //       endPoint += "consolidado";
    //       endPoint += `?usuario=${this.$store.getters.USUA_ID}`;
    //       endPoint += `&COCT_ID=${this.$store.getters.C_COCT_ID}`;
          
    //       service.cofre.busca(endPoint).then(({ STATUS, data, msg }) => {
    //         if (STATUS == "success") {
    //           const {ENTRADA, RETIRADA, SALDO} = data
    //           this.valores[0] = ENTRADA
    //           this.valores[1] = RETIRADA
    //           this.valores[2] = +SALDO

    //         } else if (STATUS == "error") {
    //           this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: msg });

    //         } else if (STATUS == "token") {
    //           this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: service.arrMessage, });
    //           this.$store.commit("SET_LOGIN", false);

    //         }

    //         this.time = true;
    //       });
    //     }, service.timeLoading);
    //   },
    // },
    
    // watch: {
    //   'cofre.COCT_ID'(newValue, oldValue) {
    //     this.getDados()
    //   },
    //   'periodo'(newValue, oldValue) {
    //     this.getDados()
    //   }
    // },
  }
</script>