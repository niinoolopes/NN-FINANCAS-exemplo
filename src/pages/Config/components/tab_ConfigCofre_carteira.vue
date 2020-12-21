<template>
  <section>

    <PageContentNav>
        <template v-slot:menu> </template>

        <template v-slot:btn>
          <button 
            type="button"
            class="btn-hover btn btn-sm btn-outline-info py-0 mr-2"
            data-toggle="modal" data-target="#CofreModalCarteira" 
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
            <th scope="row">{{tr.COCT_ID}}</th>
            <td>{{tr.COCT_DESCRICAO}}</td>
            <td>{{tr.COCT_STATUS == 1? 'Ativa' : 'Inativa'}}</td>
            <td>{{tr.COCT_PAINEL == 1? 'x' : ''}}</td>
            <td>
              <i class=" cursor-pointer far fa-file-alt"
                data-toggle="modal" 
                data-target="#CofreModalCarteira" 
                @click="setItem(tr.COCT_ID)"></i>
            </td>
          </tr>
        </tbody>

        <tbody v-else>
          <td colspan="5">Não existe dados cadastrados</td>
        </tbody>
        
      </table>
    </div>

    <div class="modal fade" id="CofreModalCarteira">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          
          <div class="modal-header py-1 px-2">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="CofreModalCarteira-close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <div class="modal-body">
            <form v-on:submit.prevent="salvar">
              <div class="row">
                <div class="form-group col-12">
                  <label for="form-COCT_DESCRICAO">Descrição</label>
                  <input id="form-COCT_DESCRICAO" class="form-control" type="text" v-model="itemModal.COCT_DESCRICAO" >
                </div>
              </div>
              
              <div v-if="itemModal.COCT_ID != 'novo'" class="row">
                <div class="form-group col-12 m-0">
                  <div class="form-group form-check m-0">
                    <input id="COCT_STATUS" type="checkbox" class="form-check-input" v-model="itemModal.COCT_STATUS">
                    <label for="COCT_STATUS" class="form-check-label">Carteira {{itemModal.COCT_STATUS ? 'Ativo' : 'Inativo'}}</label>
                  </div>
                </div>
              </div>
              
              <div v-if="itemModal.COCT_ID != 'novo'" class="row">
                <div class="form-group col-12 m-0">
                  <div class="form-group form-check m-0">
                    <input id="COCT_PAINEL" type="checkbox" class="form-check-input" v-model="itemModal.COCT_PAINEL">
                    <label for="COCT_PAINEL" class="form-check-label">Carteira {{itemModal.COCT_PAINEL ? 'principal' : 'não principal'}}</label>
                  </div>
                </div>
              </div>
            </form>
          </div>

          <div class="modal-footer  p-2">
            <button type="button" class="btn btn-sm btn-outline-info" @click="salvar()">Salvar</button>
            <button v-if="itemModal.COCT_ID != 'novo'" type="button" class="btn btn-sm btn-outline-danger" @click="deletar()">Deletar</button>
            <button type="button" class="btn btn-sm btn-outline-secondary" data-dismiss="modal">Fechar</button>
          </div>

        </div>
      </div>
    </div>
    
  </section>
</template>

<script>
  const empyt_carteira = {
    COCT_ID: 'novo',
    COCT_DESCRICAO: '',
    COCT_PAINEL: 0,
    COCT_STATUS: 1,
    USUA_ID: '',
  }

  import {mapState} from 'vuex';
  import service from '@/service.js'
  
  export default {

    components: {},

    computed: { ...mapState(['usuario']) },

    data() {
      return {
        items: [],
        itemsTable: [],
        itemModal: {},

        time: false,
        carteiras: [],
      }
    },

    created () {
      this.getDados()
    },

    methods: {
      getDados(commit = false){
        this.items      = []
        this.itemsTable = []
        this.time = false

        setTimeout( () => {
          var options = {};
          options.USUA_ID = this.$store.getters.USUA_ID;

          service.config.cofre.carteira.get(options).then( ({STATUS, data, msg}) => {
            
            if(STATUS == 'success'){
              var items = data
              items.push(empyt_carteira)
              this.items      = items
              this.itemsTable = items.filter( c => c.COCT_ID != 'novo' )

              if(commit) this.$store.commit('COFRE_CARTEIRA', this.itemsTable);
            }
            else if(STATUS == 'erro'){
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
      
      setItem(COCT_ID){

        service.initForm([
          "COCT_DESCRICAO",
        ])

        this.itemModal = this.items.filter( i => i.COCT_ID == COCT_ID)[0]
      },

      salvar(){
        let data = {}
        data.COCT_ID        = this.itemModal.COCT_ID
        data.COCT_DESCRICAO = this.itemModal.COCT_DESCRICAO
        data.COCT_PAINEL    = this.itemModal.COCT_PAINEL ? 1 : 0
        data.COCT_STATUS    = this.itemModal.COCT_STATUS ? 1 : 0
        data.USUA_ID        = this.$store.getters.USUA_ID

        let option = {}
        option.USUA_ID = this.$store.getters.USUA_ID
        option.COCT_ID = this.itemModal.COCT_ID
        option.data = data

        let checkForm = {}
        checkForm.COCT_DESCRICAO = this.itemModal.COCT_DESCRICAO

        setTimeout( () => {
          if(service.checkForm(checkForm)){

            if(this.itemModal.COCT_ID == 'novo'){
              service.config.cofre.carteira.post(option).then( ({STATUS, msg}) => {
                if(STATUS == 'success'){
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Carteira cadastrado!' }) // message
                  this.getDados(true)
                }
                else if(STATUS == 'erro'){
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: msg })
                }
                else if(STATUS == 'token'){
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: service.arrMessage })
                  this.$store.commit('SET_LOGIN', false);
                }
              })
            } 
            else {
              service.config.cofre.carteira.put(option).then( ({STATUS, msg}) => {
                if(STATUS == 'success'){
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Carteira atualizada!' }) // message
                  this.getDados(true)
                }
                else if(STATUS == 'erro'){
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: msg })
                }
                else if(STATUS == 'token'){
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: service.arrMessage })
                  this.$store.commit('SET_LOGIN', false);
                }
              })
            }
        //     this.$store.dispatch("F_Carteiras")
            service.closeModal('CofreModalCarteira-close')
          }
        }, service.timeLoading)
      },

      deletar(){
        var comfirm = confirm("ao excluir será apagado permanentemente as informações, deseja continuar?")

        if(comfirm){
          let option = { FINC_ID: this.itemModal.FINC_ID }
          service.config.financ.carteira.del(option).then( ({STATUS, data, msg}) => {
            if(STATUS == 'success'){
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Carteira excluido!' }) // message
              this.getDados();

            }else{
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: 'Erro ao excluir, tente novamente!' }) // message
            }
          })
        }
        this.$store.dispatch("F_Carteiras")
        service.closeModal('InvestimentoModalCarteira-close')
      }
    },  
  }
</script>