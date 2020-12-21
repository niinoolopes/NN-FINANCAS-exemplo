<template>
  <section>
    
    <PageContentNav>
      <template v-slot:menu> </template>
      <template v-slot:btn>

        <ButtongetDados :getDados='getDados' />
      </template>
    </PageContentNav>

    <Table colspan='3' :timeTable='timeTable' :itemsTable='itemsTable' >
      <template v-slot:thead>
          <th scope="col" class="responsivo text-center">#</th>
          <th scope="col" class="th-m-100 text-left"    >Descrição</th>
          <th scope="col" class="th-m-100 text-center"  >Status</th>
          <!-- <th></th> -->
      </template>
      <template v-slot:tbody>
        <tr v-for="(item,i) in itemsTable" :key="i">
          <th class="responsivo text-center">{{item.INTP_ID}}</th>
          <td class="td-m-120 text-left"    >{{item.INTP_DESCRICAO}} <small>({{item.INTP_ID}})</small></td>
          <td class="th-m-100 text-center"  >{{item.INTP_STATUS == 1? 'Ativa' : 'Inativa'}}</td>
        </tr>
      </template>
    </Table>
        
    <!-- <div class="modal fade" id="InvestimentoModalTipo">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header py-1 px-2">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="InvestimentoModalTipo-close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form>
              <div class="row">

                <div class="mb-2 col-12">
                  <label for="form-INTP_DESCRICAO">Descrição</label>
                  <input id="form-INTP_DESCRICAO" class="form-control form-control-sm mb-md-2" type="text" v-model="itemModal.INTP_DESCRICAO" >
                </div>

                <div v-if="itemModal.INTP_ID != 'novo'" class="col-12">
                  <div class="form-check mt-2 mb-0">
                    <input id="form-INTP_STATUS" type="checkbox" class="form-check-input" v-model="itemModal.INTP_STATUS">
                    <label for="form-INTP_STATUS" class="form-check-label">Ativo {{itemModal.INTP_STATUS ? 'Ativo' : 'Inativo'}}</label>
                  </div>
                </div>

              </div>
              
            </form>
          </div>
          <div class="modal-footer  p-2">
            <button type="button" class="btn btn-sm btn-outline-info" @click="salvar()">Salvar</button>
            <button type="button" class="btn btn-sm btn-outline-secondary" data-dismiss="modal">Fechar</button>
          </div>
        </div>
      </div>
    </div> -->

  </section>
</template>

<script>
  // const empyt_tipo = {
  //   INTP_ID: 'novo',
  //   INTP_DESCRICAO: "",
  //   INTP_STATUS: 1,
  // }

  import service from "@/service.js"
  import {mapState} from 'vuex'

  export default {

    components: {
      ButtongetDados:    () => import('@/components/Button_getDados'),

      Table: () => import('@/components/Table'),
    },


    computed: { ...mapState(['usuario']) },

    data() {
      return {
        timeTable: false,
        itemsTable: [],
      }
    },

    created () {
      this.getDados();
    },

    methods: {
      getDados(){
        this.timeTable = false;
        this.itemsTable = [];
        
        setTimeout( () => {
          let option = { USUA_ID: this.usuario.USUA_ID }
          service.config.invest.tipo.get(option).then( ({STATUS, data, msg}) => {
            if(STATUS == 'success'){
              this.itemsTable = data
            }
            else if(STATUS == 'error') {
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: msg })
            }
            else if(STATUS == 'token') {
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: service.arrMessage })
              this.$store.commit('SET_LOGIN', false);
            }
            this.timeTable = true;
          })
        }, service.timeLoading)
      },
    },
  }
</script>