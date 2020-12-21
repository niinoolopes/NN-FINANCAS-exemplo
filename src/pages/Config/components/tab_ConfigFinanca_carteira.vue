<template>
  <section>

    <PageContentNav>
        <template v-slot:menu> </template>

        <template v-slot:btn>
          <button 
            type="button"
            class="btn-hover btn btn-sm btn-outline-info py-0 mr-2"
            data-toggle="modal" data-target="#FinancaModalCarteira" 
            @click="setItem('novo')"><i class="fas fa-plus"></i></button>

          <button 
            type="button"
            class="btn-hover btn btn-sm btn-outline-info py-0"
            @click="getDados"><i class="fas fa-sync"></i></button>
        </template>
    </PageContentNav>
    
    <div class="border over-x opacity-fetch" :class="time ? '' : 'opacity-fetch-active'">
      <table class="table table-sm">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Descrição</th>
            <th scope="col">Status</th>
            <th scope="col">Painel</th>
            <th></th>
          </tr>
        </thead>
        
        <tbody v-if="itemsTable.length > 0">
          <tr v-for="(tr,i) in itemsTable" :key="i">
            <th scope="row">{{tr.FINC_ID}}</th>
            <td>{{tr.FINC_DESCRICAO}}</td>
            <td>{{tr.FINC_STATUS == 1? 'Ativa' : 'Inativa'}}</td>
            <td>{{tr.FINC_PAINEL == 1? 'x' : ''}}</td>
            <td>
              <i 
                class=" cursor-pointer far fa-file-alt"
                data-toggle="modal" 
                data-target="#FinancaModalCarteira" 
                @click="setItem(tr.FINC_ID)"></i>
            </td>
          </tr>
        </tbody>
        <tbody v-else>
          <td colspan="5">Não existe grupos cadastrados</td>
        </tbody>
        
      </table>
    </div>

    <div class="modal fade" id="FinancaModalCarteira">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header py-1 px-2">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="FinancaModalCarteira-close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form v-on:submit.prevent="salvar">
              <div class="row">
                <div class="form-group col-12">
                  <label for="form-FINC_DESCRICAO">Descrição</label>
                  <input id="form-FINC_DESCRICAO" class="form-control" type="text" v-model="itemModal.FINC_DESCRICAO" >
                </div>
              </div>
              
              <div v-if="itemModal.FINC_ID != 'novo'" class="row">
                <div class="form-group col-12 m-0">
                  <div class="form-group form-check m-0">
                    <input id="FINC_STATUS" type="checkbox" class="form-check-input" v-model="itemModal.FINC_STATUS">
                    <label for="FINC_STATUS" class="form-check-label">Carteira {{itemModal.FINC_STATUS ? 'Ativo' : 'Inativo'}}</label>
                  </div>
                </div>
              </div>
              
              <div v-if="itemModal.FINC_ID != 'novo'" class="row">
                <div class="form-group col-12 m-0">
                  <div class="form-group form-check m-0">
                    <input id="FINC_PAINEL" type="checkbox" class="form-check-input" v-model="itemModal.FINC_PAINEL">
                    <label for="FINC_PAINEL" class="form-check-label">Carteira {{itemModal.FINC_PAINEL ? 'principal' : 'não principal'}}</label>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer  p-2">
            <button type="button" class="btn btn-sm btn-outline-info" @click="salvar()">Salvar</button>
            <button v-if="itemModal.FINC_ID != 'novo'" type="button" class="btn btn-sm btn-outline-danger" @click="deletar()">Deletar</button>
            <button type="button" class="btn btn-sm btn-outline-secondary" data-dismiss="modal">Fechar</button>
          </div>
        </div>
      </div>
    </div> 
    
  </section>
</template>

<script>
  const empyt_carteira = {
    FINC_ID: 'novo',
    FINC_DESCRICAO: '',
    FINC_STATUS: 1,
    FINC_PAINEL: 0,
  }

  // import {mapState} from 'vuex';
  import service from '@/service.js'
  
  export default {

    components: {},

    data() {
      return {
        time: false,
        items: [],
        itemsTable: [],
        itemModal: {},

        carteiras: [],
      }
    },

    created () {
      this.getDados()
    },

    methods: {
      getDados(commit){
        this.items      = [];
        this.itemsTable = [];

        this.time = false;

        setTimeout( () => {
          let option = {}
          option.USUA_ID = this.$store.getters.USUA_ID

          service.config.financ.carteira.get(option).then( ({STATUS, data, msg}) => {
            if(STATUS == 'success'){
              this.items = data
              this.items.push(empyt_carteira)
              this.itemsTable = this.items.filter( c => c.FINC_ID != 'novo' )

              if(commit) this.$store.commit('FINANCA_CARTEIRA', this.itemsTable);
            }
            this.time = true
          })

        }, service.timeLoading)
      },
      
      setItem(FINC_ID){

        service.initForm([
          "FINC_DESCRICAO",
        ])

        this.itemModal = this.items.filter( i => i.FINC_ID == FINC_ID)[0]
      },

      salvar(){
        let data = {}
        data.FINC_DESCRICAO = this.itemModal.FINC_DESCRICAO
        data.FINC_ID        = this.itemModal.FINC_ID
        data.FINC_STATUS    = this.itemModal.FINC_STATUS ? 1 : 0
        data.FINC_PAINEL    = this.itemModal.FINC_PAINEL ? 1 : 0

        let option = {}
        option.USUA_ID = this.$store.getters.USUA_ID
        option.FINC_ID = this.itemModal.FINC_ID
        option.data = data

        let checkForm = {}
        checkForm.FINC_DESCRICAO = this.itemModal.FINC_DESCRICAO

        setTimeout( () => {
          if(service.checkForm(checkForm)){
            if(this.itemModal.FINC_ID == 'novo'){
              service.config.financ.carteira.post(option).then( ({STATUS, msg}) => {
                if (STATUS == "success") {
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Carteira cadastrado!' }) // message
                  this.getDados('commit')

                } else if (STATUS == "error") {
                  this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: msg });

                } else if (STATUS == "token") {
                  this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: service.arrMessage, });
                  this.$store.commit("SET_LOGIN", false);
                }
              })
            } else {
              service.config.financ.carteira.put(option).then( ({STATUS, msg}) => {
                if (STATUS == "success") {
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Carteira atualizada!' }) // message
                  this.getDados('commit')

                } else if (STATUS == "error") {
                  this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: msg });

                } else if (STATUS == "token") {
                  this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: service.arrMessage, });
                  this.$store.commit("SET_LOGIN", false);
                }
              })
            }

            this.$store.dispatch("F_Carteiras")
            service.closeModal('FinancaModalCarteira-close')
          }
        }, service.timeLoading)
      },

      deletar(){
        var comfirm = confirm("ao excluir será apagado permanentemente as informações, deseja continuar?")

        if(comfirm){
          let option = { FINC_ID: this.itemModal.FINC_ID }
          service.config.financ.carteira.del(option).then( ({STATUS, data, msg}) => {
            if (STATUS == "success") {
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Carteira excluido!' }) // message
              this.getDados()

            } else if (STATUS == "error") {
              this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: msg });

            } else if (STATUS == "token") {
              this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: service.arrMessage, });
              this.$store.commit("SET_LOGIN", false);
            }
          })
        }
        this.$store.dispatch("F_Carteiras")
        service.closeModal('InvestimentoModalCarteira-close')
      }
    },  
  }
</script>