<template>
  <section class="opacity-fetch" :class="time ? '' : 'opacity-fetch-active'">

    <template v-if='Array.isArray(items)'>
      <div v-if="items.length != 0" class="d-flex flex-wrap">
        <article v-for="(item,i) in items" :key="i" class="col-12 col-sm-6 col-lg-3 col-xl-2 p-1 mb-0"
        :class=" item.COTAS == 0 ? 'opacity-active' : ''" 
        >
          <div class="p-2 border border-ligaht rounded shadow-sm cursor-pointer">

            <header class="pl-2 py-2 mb-0">
              <p class="m-0 h5">
                <router-link :to="{name: 'InvestimentoAtivoAdm', params:{ INAV_ID: item.INAV_ID }}" class="text-decoration-none text-dark">
                  {{item.INAV_CODIGO}} <small>({{item.INAV_ID}})</small>
                </router-link>
              </p>
              <p class="m-0 small">{{item.INCR_DESCRICAO}}</p>
            </header>

            <hr v-if='detalheItem' class="my-1">

            <div v-if='detalheItem' @click="setItemModal(i)" class="d-flex flex-wrap" data-toggle="modal" data-target="#Investimento-ativo-rendaVariavel-item">
              <span class="p-0 col-6">
                <p class="m-0 small subtitulo text-black-50">Quantidade</p>
                <p class="font-weight-light h6 ml-1 mb-0">{{item.COTAS}}</p>
              </span>
              <span class="p-0 col-6">
                <p class="m-0 small subtitulo text-black-50">Preço Médio</p>
                <p class="font-weight-light h6 ml-1 mb-0">{{item.PRECO_MEDIO}}</p>
              </span>
              <span class="p-0 col-6">
                <p class="m-0 small subtitulo text-black-50">Valor Aplicado</p>
                <p class="font-weight-light h6 ml-1 mb-0">{{item.TOTAL}}</p>
              </span>
              <span class="p-0 col-6">
                <p class="m-0 small subtitulo text-black-50">Valor Bruto</p>
                <p class="font-weight-light h6 ml-1 mb-0">{{item.BRUTO}}</p>
              </span>
            </div>
          </div>

        </article>
      </div>

      <Modal v-if="items.length != 0" modal='modal-lg' :timeModal="time" idModal="Investimento-ativo-rendaVariavel-item">
        <Column class="col-12">
          <p class="text-center pt-2 m-0 h5">{{itemModal.INAT_DESCRICAO}}</p>
          <p class="text-center pt-2 mb-1 h3 font-weight-lighter">
            <span>{{itemModal.INAV_CODIGO}} <small>({{itemModal.INAV_ID}})</small></span>
          </p>
          <p class="text-center p-0 mb-3 h1 font-weight-bold">{{itemModal.BRUTO | vReal}}</p>
        </Column>

        <Column v-for='(e,i) in itemModalTeste' :key='i' class="col-12 col-md-4">
          <div class="bg-light border rounded" style="height: 100%">
            <p :class="textTitulo">{{e.tituloDesc}}</p>

            <p v-for='(j,k) in e.dados' :key='k' class="w-100 m-0 d-flex align-items-center">
              <span :class="textLeft">{{j.desc}}</span>
              <span :class="textRight"><strong>{{j.value}}</strong> </span>
            </p>

          </div>
        </Column>
      </Modal>

      <p v-if="items.length == 0">Não foi encontrato registros</p>

    </template>
    

    <p v-else>Buscando dados...</p>

  </section>
</template>

<script>
  export default {
    props: ['items', 'time', 'detalheItem'],
      
    components: { 
      Modal: () => import('@/components/Modal'),
    },

    data() {
      return {
        textTitulo:'col-12 px-1 my-1 text-left font-weight-normal border-bottom',
        textLeft:  'col-6A  px-1 my-1 text-left small text-black-50',
        textRight: 'col  px-1 my-1 text-right',
        itemModal: {},
        itemModalTeste: []
      }
    },
    
    methods: {
      setItemModal(i) {
        this.itemModal = this.items[i]

        this.itemModalTeste = [];
        
        this.itemModalTeste.push({
          tituloDesc: 'Valores',
          dados:[
            { desc: 'Valor Aplicado'    , value: this.itemModal.TOTAL },
            { desc: 'Valor Bruto'       , value: this.itemModal.BRUTO },
            { desc: 'Cotas'             , value: this.itemModal.COTAS },
            { desc: 'Preço médio'       , value: this.itemModal.PRECO_MEDIO },
            { desc: 'Ultíma cotação'    , value: this.itemModal.PRECO_COTACAO },
            { desc: 'Valorização cota'  , value: this.itemModal.VALORIZACAO_UNIDADE },
            { desc: 'Valorização total' , value: this.itemModal.VALORIZACAO_TOTAL },
          ]
        })

        this.itemModalTeste.push({
          tituloDesc: 'Rendimentos',
          dados:[
            { desc: 'Total Dividendo'  , value: this.itemModal.TOTAL_DIVIDENDO  },
            { desc: 'Total JSCP'       , value: this.itemModal.TOTAL_JSCP       },
            { desc: 'Total Rendimento' , value: this.itemModal.TOTAL_RENDIMENTO },
            { desc: 'Mês Dividendo'    , value: this.itemModal.MES_DIVIDENDO    },
            { desc: 'Mês JSCP'         , value: this.itemModal.MES_JSCP         },
            { desc: 'Mês Rendimento'   , value: this.itemModal.MES_RENDIMENTO   },
          ]
        })
        
        this.itemModalTeste.push({
          tituloDesc: 'Lucro Venda',
          dados:[
            { desc: 'Valor Compra'      , value: this.itemModal.TOTAL_COMPRA },
            { desc: 'Valor Venda'       , value: this.itemModal.TOTAL_VENDA  },
            { desc: 'Cotas Compra'      , value: this.itemModal.COTAS_COMPRA },
            { desc: 'Cotas Venda'       , value: this.itemModal.COTAS_VENDA  },
            { desc: 'Lucro Venda Saldo' , value: this.itemModal.LUCRO_VENDA  },
          ]
        })

        this.itemModalTeste.push({
          tituloDesc: 'Ativo',
          dados:[
            { desc: 'Tipo Investimento', value: this.itemModal.INTP_DESCRICAO },
            { desc: 'Tipo Ativo'       , value: this.itemModal.INAT_DESCRICAO },
            { desc: 'Ativo'            , value: this.itemModal.INAV_CODIGO    },
            { desc: 'Descrição'        , value: this.itemModal.INAV_DESCRICAO },
            { desc: 'CNPJ'             , value: this.itemModal.INAV_CPNJ      },
            { desc: 'Site'             , value: this.itemModal.INAV_SITE      },
            { desc: 'Liquidez'         , value: this.itemModal.INAV_LIQUIDEZ  },
            { desc: 'Data Venc.'       , value: this.itemModal.INAV_VENC      },
          ]
        })

        this.itemModalTeste.push({
          tituloDesc: 'Outros detalhes',
          dados:[
            { desc: 'Proprietário' , value: this.itemModal.USUA_NOME      },
            { desc: 'Carteira'     , value: this.itemModal.INCT_DESCRICAO },
            { desc: 'Coretora'     , value: this.itemModal.INCR_DESCRICAO },
          ]
        })

        this.itemModalTeste.push({
          tituloDesc: 'Valores Históricos',
          dados:[
            { desc: 'Total Compra' , value: this.itemModal.H_TOTAL_COMPRA },
            { desc: 'Total Venda'  , value: this.itemModal.H_TOTAL_VENDA  },
            { desc: 'Cotas Compra' , value: this.itemModal.H_COTAS_COMPRA },
            { desc: 'Cotas Venda'  , value: this.itemModal.H_COTAS_VENDA  },
          ]
        })

      }
    },

  }
</script>

<style scoped>
  article header{
    border-left: 0.25rem solid;
    border-radius: 7px;
  }
  article .subtitulo {
    font-size: 10px;
  }
  article .total {
    font-size: 18px;
  }

  article {
    transition: 0.3s;
    opacity: 0.75;
    max-width: 200px;
  }
  article.opacity-active {
    opacity: 0.50;
  }
  article:hover {
    opacity: 1;
  }
</style>