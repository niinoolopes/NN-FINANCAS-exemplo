<template>
  <section class="opacity-fetch" :class="time ? '' : 'opacity-fetch-active'">
    <div class="d-flex flex-wrap">
      <article v-for="(item,i) in items" :key="i" class="col-12 col-sm-6 col-lg-3 col-xl-2 p-1 mb-2" >

        <div class="p-2 border border-ligaht rounded shadow-sm cursor-pointer">

          <header class="pl-2 py-2 mb-2">
            <p class="m-0 h5">{{item.INAV_CODIGO}} <small>({{item.INAV_ID}})</small></p>
            <p class="m-0 small">{{item.INCR_DESCRICAO}}</p>
          </header>

          <hr class="my-1">

          <div @click="setItemModal(i)" class="d-flex flex-wrap" data-toggle="modal" data-target="#Investimento-ativo-rendaFixa-item">
            <span class="p-0 col-6">
              <p class="m-0 small subtitulo text-black-50">Quantidade</p>
              <p class="font-weight-light h6 ml-1">{{item.COTAS}}</p>
            </span>
            <span class="p-0 col-6">
              <p class="m-0 small subtitulo text-black-50">Preço Médio</p>
              <p class="font-weight-light h6 ml-1">{{item.PRECO_MEDIO}}</p>
            </span>
            <span class="p-0 col-6">
              <p class="m-0 small subtitulo text-black-50">Valor Aplicado</p>
              <p class="font-weight-light h6 ml-1">{{item.TOTAL}}</p>
            </span>
            <span class="p-0 col-6">
              <p class="m-0 small subtitulo text-black-50">Valor Bruto</p>
              <p class="font-weight-light h6 ml-1">{{item.BRUTO}}</p>
            </span>
          </div>
        </div>

      </article>
    </div>

    <p v-if="items.length == 0">Não foi encontrato registros</p>

    <div v-if="itemModal != 'init'" class="modal fade" id="Investimento-ativo-rendaFixa-item">
      <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
          <div class="modal-header py-1 px-2">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="Investimento-ativo-rendaFixa-item">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body py-0 opacity-fetch" :class="time ? '' : 'opacity-fetch-active'">
            <div class="">
              <p class="text-center pt-2 m-0 h5">{{itemModal.INAT_DESCRICAO}}</p>

              <p class="text-center pt-2 mb-1 h3 font-weight-lighter">{{itemModal.INAV_CODIGO}} <small>({{itemModal.INAV_ID}})</small></p>

              <p class="text-center p-0 mb-3 h1 font-weight-bold">{{itemModal.BRUTO | vReal}}</p>

              <div class="bg-light d-flex flex-wrap border rounded mx-auto mb-2">
                <div :class="textTitulo">Resultado Geral</div>

                <div :class="textLeft">Valor Aplicado </div>
                <div :class="textRight"><strong>{{itemModal.TOTAL | vReal}}</strong> </div>

                <div :class="textLeft">Valor Bruto </div>
                <div :class="textRight"><strong>{{itemModal.BRUTO | vReal}}</strong> </div>

                <div :class="textLeft">Quantidade </div>
                <div :class="textRight"><strong>{{itemModal.COTAS | vReal}}</strong> </div>

                <div :class="textLeft">Preço Médio </div>
                <div :class="textRight"><strong>{{itemModal.PRECO_MEDIO | vReal}}</strong> </div>

                <div :class="textLeft">Valorização por cota</div>
                <div :class="textRight"><strong>{{itemModal.VALORIZACAO_UNIDADE | vReal}}</strong> </div>

                <div :class="textLeft">Valorização total</div>
                <div :class="textRight"><strong>{{itemModal.VALORIZACAO_TOTAL | vReal}}</strong> </div>

                <div :class="textLeft">Dividendo </div>
                <div :class="textRight"><strong>{{itemModal.TOTAL_DIVIDENDO | vReal}}</strong> </div>

                <div :class="textLeft">JSCP </div>
                <div :class="textRight"><strong>{{itemModal.TOTAL_JSCP | vReal}}</strong> </div>
              </div>

              <div class="bg-light d-flex flex-wrap border rounded mx-auto mb-2">
                <div :class="textTitulo">Resultado Mês</div>

                <div :class="textLeft">Dividendo </div>
                <div :class="textRight"><strong>{{itemModal.MES_DIVIDENDO | vReal}}</strong></div>

                <div :class="textLeft">JSCP </div>
                <div :class="textRight"><strong>{{itemModal.MES_JSCP | vReal}}</strong></div>
              </div>

              <div class="bg-light d-flex flex-wrap border rounded mx-auto mb-2">
                <div :class="textTitulo">Detalhes Ativo</div>

                <div :class="textLeft">Carteira</div>
                <div :class="textRight"><strong>{{itemModal.INCT_DESCRICAO}}</strong> ({{itemModal.INCT_ID}})</div>

                <div :class="textLeft">Corretora</div>
                <div :class="textRight"><strong>{{itemModal.INCR_DESCRICAO}}</strong> ({{itemModal.INCR_ID}})</div>
                
                <div :class="textLeft">Liquides</div>
                <div :class="textRight"><strong>{{itemModal.INAV_LIQUIDEZ}}</strong> </div>

                <div :class="textLeft">Data Venc.</div>
                <div :class="textRight"><strong>{{itemModal.INAV_VENC | convertDate}}</strong> </div>

                <div :class="textLeft">STATUS ativo</div>
                <div :class="textRight"><strong>{{itemModal.INAV_STATUS ? 'Ativo' : 'Inativo'}}</strong> </div>
              </div>

              <div class="bg-light d-flex flex-wrap border rounded mx-auto mb-2">
                <div :class="textTitulo">Outros</div>

                <div :class="textLeft">Total Compra </div>
                <div :class="textRight"><strong>{{itemModal.TOTAL_COMPRA | vReal}}</strong> </div>

                <div :class="textLeft">Total Venda </div>
                <div :class="textRight"><strong>{{itemModal.TOTAL_VENDA | vReal}}</strong> </div>

                <div :class="textLeft">Cotas Compra </div>
                <div :class="textRight"><strong>{{itemModal.COTAS_COMPRA | vReal}}</strong> </div>

                <div :class="textLeft">Cotas Venda </div>
                <div :class="textRight"><strong>{{itemModal.COTAS_VENDA | vReal}}</strong> </div>
              </div>
            </div>
          </div>
          <div class="modal-footer p-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" data-dismiss="modal">Fechar</button>
          </div>
        </div>
      </div>
    </div>

  </section>
</template>

<script>
  export default {
    props: ['items', 'time'],

    data() {
      return {
        textTitulo:'col-12 my-1 text-left font-weight-normal border-bottom',
        textLeft:  'col-6  my-1 text-left small text-black-50',
        textRight: 'col-6  my-1 text-right',
        itemModal: 'init'
      }
    },
    
    mounted () {
      },
    
    methods: {
      setItemModal(i) {
        this.itemModal = this.items[i]
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
</style>