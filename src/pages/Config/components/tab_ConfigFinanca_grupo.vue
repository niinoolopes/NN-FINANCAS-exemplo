<template>
  <section>
    <PageContentNav>
        <template v-slot:menu>
          <div class="col-12 col-sm-6 col-lg-4 col-xl-3 p-0 pr-1 mb-1 m-sm-0">
            <select class="form-control form-control-sm mb-2 m-sm-0" v-model="FINC_ID">
              <option value="0">Selecione...</option>
              <option v-for="(c,i) in $store.getters.F_CarteirasAtivas" :key="i" :value="c.FINC_ID">{{c.FINC_DESCRICAO}}</option>
            </select>
          </div>
        </template>

        <template v-slot:btn>
          <button 
            type="button" 
            class="btn-hover btn btn-sm btn-outline-info py-0 mr-2" 
            :disabled="btns"
            data-toggle="modal" data-target="#FinancaModalGrupo" 
            @click="setItem('novo')"><i class="fas fa-plus"></i></button>

          <button 
            type="button" 
            class="btn-hover btn btn-sm btn-outline-info py-0" 
            :disabled="btns"
            @click="getLista(FINC_ID)"><i class="fas fa-sync"></i></button>
        </template>
    </PageContentNav>
  
    <div class="border over-x opacity-fetch" :class="time ? '' : 'opacity-fetch-active'">
      <table class="table table-sm">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Descrição</th>
            <th scope="col">Tipo</th>
            <th scope="col">Status</th>
            <th></th>
          </tr>
        </thead>

        <tbody v-if="items.length > 0">
          <tr v-for="(item,i) in items" :key="i">
            <th scope="row">{{item.FIGP_ID}}</th>
            <td>{{item.FIGP_DESCRICAO}}</td>
            <td>{{item.FITP_DESCRICAO}}</td>
            <td>{{item.FIGP_STATUS ? 'Ativo' : 'Inativo'}}</td>
            <td>
              <i
                class=" cursor-pointer far fa-file-alt"
                data-toggle="modal" 
                data-target="#FinancaModalGrupo" 
                @click="setItem(item.FIGP_ID)"></i>
            </td>
          </tr>
        </tbody>
        <tbody v-else>
          <td colspan="5">Não existe grupos cadastrados</td>
        </tbody>

      </table>
    </div>

    <div class="modal fade" key="modal-grupo" id="FinancaModalGrupo">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          
          <div class="modal-header py-1 px-2">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="FinancaModalGrupo-close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          
          <div class="modal-body">
            <form v-on:submit.prevent="salvar">

              <div class="row">
                <div class="col-12 col-sm-6">
                  <label for="form-FINC_ID">Carteira</label>
                  <select id="form-FINC_ID" class="form-control form-control-sm mb-2" v-model="itemModal.FINC_ID">
                    <option value="">Selecione...</option>
                    <option v-for="(c,i) in $store.getters.F_CarteirasAtivas" :key="i" :value="c.FINC_ID">{{c.FINC_DESCRICAO}}</option>
                  </select>
                </div>

                <div class="col-12 col-sm-6">
                  <label for="form-FITP_ID">Tipo</label>
                  <select id="form-FITP_ID" class="form-control form-control-sm mb-2" v-model="itemModal.FITP_ID">
                    <option value="">Selecione...</option>
                    <option v-for="(t,i) in $store.getters.F_TiposAtivos" :key="i" :value="t.FITP_ID">{{t.FITP_DESCRICAO}}</option>
                  </select>
                </div> 

                <div class="form-group col-12">
                  <label for="form-FIGP_DESCRICAO">Descrição</label>
                  <input id="form-FIGP_DESCRICAO" class="form-control" type="text" v-model="itemModal.FIGP_DESCRICAO" >
                </div>
              </div>

              <div class="row">
                <div class="form-group col-12 m-0">
                  <div class="form-group form-check m-0">
                    <input id="form-FIGP_STATUS" type="checkbox" class="form-check-input" v-model="itemModal.FIGP_STATUS">
                    <label for="form-FIGP_STATUS" class="form-check-label">Grupo {{itemModal.FIGP_STATUS ? 'Ativo' : 'Inativo'}}</label>
                  </div>
                </div>
              </div>
              
            </form>
          </div>
          
          <div class="modal-footer p-2">
            <button type="button" class="btn btn-sm btn-outline-info" @click="salvar()">Salvar</button>
            <button v-if="itemModal.FIGP_ID != 'novo'" type="button" class="btn btn-sm btn-outline-danger" @click="deletar()">Deletar</button>
            <button type="button" class="btn btn-sm btn-outline-secondary" data-dismiss="modal">Fechar</button>
          </div>
          
        </div>
      </div>
    </div>

  </section>
</template>

<script>
  const empyt_grupo = {
    FIGP_ID: 'novo',
    FIGP_DESCRICAO: '',
    FIGP_STATUS: 1,
    FITP_ID: null,
    FINC_ID: null
  }

  import {mapState} from 'vuex'
  import service from '@/service.js'

  export default {

    components: { },

    computed: { ...mapState(['usuario']) },
    
    data() {
      return {
        time: false,
        grupos: [],
        items: [],

        FINC_ID: 0,
        btns: true,

        itemModal: {},
      }
    },

    mounted () {
      if( !this.$store.getters.F_CarteiraPainel ){

        this.FINC_ID = this.$store.getters.F_FINC_ID

        if(this.FINC_ID) {
          this.getLista()
          this.btns = 1
        }
      }
    },

    methods: {
      getLista(){
        this.grupos = [];
        this.time = false;

        setTimeout( () => {
          let option = {}
          option.USUA_ID = this.usuario.USUA_ID
          option.FINC_ID = this.FINC_ID

          service.config.financ.grupo.get(option).then( ({STATUS, data, msg}) => {
            if(STATUS == 'success'){
              this.grupos = data
              this.grupos.push(empyt_grupo)
              this.items = this.grupos.filter( g => g.FIGP_ID != 'novo' )
            }
            this.time = true
          })

        }, service.timeLoading)
      },

      setItem(FIGP_ID){
        
        service.initForm([
          'FIGP_DESCRICAO',
          'FITP_ID',
          'FINC_ID',
        ])

        this.itemModal = this.grupos.filter( i => i.FIGP_ID == FIGP_ID)[0]
        this.initForm();
      },

      salvar(){
        let data = {}
        data.FIGP_DESCRICAO = this.itemModal.FIGP_DESCRICAO
        data.FIGP_ID        = this.itemModal.FIGP_ID
        data.FIGP_STATUS    = this.itemModal.FIGP_STATUS ? 1 : 0
        data.FITP_ID        = this.itemModal.FITP_ID
        data.FINC_ID        = this.itemModal.FINC_ID

        let option = {}
        option.USUA_ID = this.usuario.USUA_ID
        option.FIGP_ID = this.itemModal.FIGP_ID
        option.data = data

        let checkForm = {}
        checkForm.FINC_ID = this.itemModal.FINC_ID
        checkForm.FITP_ID = this.itemModal.FITP_ID
        checkForm.FIGP_DESCRICAO = this.itemModal.FIGP_DESCRICAO

        setTimeout( () => {
          if(service.checkForm(checkForm)){
            if(this.itemModal.FIGP_ID == 'novo'){
              service.config.financ.grupo.post(option).then( ({STATUS, msg}) => {
                if(STATUS == 'success'){
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Grupo cadastrado!' }) // message
                  this.getLista()
                }else{
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: 'Erro ao cadastrar, tente novamente!' }) // message
                }
              })
            } else {
              service.config.financ.grupo.put(option).then( ({STATUS, msg}) => {
                if(STATUS == 'success'){
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Grupo atualizada!' }) // message
                  this.getLista()
                }else{
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: 'Erro ao atualizar, tente novamente!' }) // message
                }
              })
            }
            this.$store.dispatch("F_Grupos")
            service.closeModal('FinancaModalGrupo-close')
          }
        }, service.timeLoading)
      },

      deletar(){
        var comfirm = confirm("ao excluir será apagado permanentemente as informações, deseja continuar?")

        if(comfirm){
          let option = {}
          option.FIGP_ID = this.itemModal.FIGP_ID
          
          service.config.financ.grupo.del(option).then( ({STATUS, data, msg}) => {
            if(STATUS == 'success'){
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Grupo excluido!' }) // message
              this.getLista();

            }else{
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: 'Erro ao excluir, tente novamente!' }) // message
            }
          })
        }
        this.$store.dispatch("F_Grupos")
        service.closeModal('FinancaModalGrupo-close')
      }
    },

    watch: {
      'FINC_ID'(newValue, oldValue) {
        if( newValue == 0){
          this.btns = true
          this.items = []
          this.grupos = []

        }else{
          this.btns = false
          this.getLista()

        }
      }
    },

  }
</script>

<style scoped>

</style>