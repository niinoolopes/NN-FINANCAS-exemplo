<template>
  <section>
    
    <PageContentNav>
        <template v-slot:menu>
          <div class="col-12 col-sm-6 col-lg-4 col-xl-3 p-0 pr-1 mb-1 m-sm-0">
            <select class="form-control form-control-sm mb-2 m-sm-0" v-model="FINC_ID">
              <option value="0">Selecione...</option>
              <option v-for="(c,i) in  $store.getters.F_CarteirasAtivas" :key="i" :value="c.FINC_ID">{{c.FINC_DESCRICAO}}</option>
            </select>
          </div>
          <div class="col-12 col-sm-6 col-lg-4 col-xl-3 p-0 pr-1 mb-1 m-sm-0">
            <select class="form-control form-control-sm mb-2 m-sm-0" v-model="FIGP_ID">
              <option value="0">Selecione...</option>
              <option v-for="(g,i) in $store.getters.F_GruposAtivos(FINC_ID)" :key="i" :value="g.FIGP_ID">{{g.FIGP_DESCRICAO}}</option>
            </select>
          </div>
        </template>

        <template v-slot:btn>
          <button 
            type="button" 
            class="btn-hover btn btn-sm btn-outline-info py-0 mr-2" 
            :disabled="btns"
            data-toggle="modal" data-target="#FinancaModalCategoria"
            @click="setItem('novo')"> <i class="fas fa-plus" ></i></button>

          <button 
            type="button" 
            class="btn-hover btn btn-sm btn-outline-info py-0" 
            :disabled="btns"
            @click="getDados()"><i class="fas fa-sync"></i></button>

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
            <th scope="row">{{item.FICT_ID}}</th>
            <td>{{item.FIGP_DESCRICAO}} // {{item.FICT_DESCRICAO}}</td>
            <td>{{item.FICT_STATUS ? 'Ativo' : 'Inativo'}}</td>
            <td>
              <i
               class=" cursor-pointer far fa-file-alt"
               data-toggle="modal" 
               data-target="#FinancaModalCategoria"
               @click="setItem(item.FICT_ID)" ></i>
            </td>
          </tr>
        </tbody>
        <tbody v-else>
          <td colspan="5">Não existe carteiras cadastradas</td>
        </tbody>

      </table>
    </div>
    
    <div class="modal fade" id="FinancaModalCategoria">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header py-1 px-2">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="FinancaModalCategoria-close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form v-on:submit.prevent="salvar">
              <div class="row">
                <div class="form-group col-12 col-sm-6">
                  <label for="form-FIGP_ID">Grupo</label>
                  <select id="form-FIGP_ID" class="form-control form-control-sm" v-model="itemModal.FIGP_ID">
                    <option value="">Selecione...</option>
                    <option v-for="(g,i) in $store.getters.F_GruposAtivos(FINC_ID)" :key="i" :value="g.FIGP_ID">{{g.FIGP_DESCRICAO}}</option>
                  </select>
                </div>
              </div>

              <div class="row">
                <div class="form-group col-12">
                  <label for="form-FICT_DESCRICAO">Descrição</label>
                  <input id="form-FICT_DESCRICAO" class="form-control form-control-sm" type="text" v-model="itemModal.FICT_DESCRICAO" >
                </div> 
                <div v-if="itemModal.FICT_ID != 'novo'" class="form-group col-12">
                  <div class="form-group form-check m-0">
                    <input id="form-FICT_STATUS" type="checkbox" class="form-check-input" v-model="itemModal.FICT_STATUS">
                    <label for="form-FICT_STATUS" class="form-check-label">Categoria <strong>{{itemModal.FICT_STATUS ? 'Ativa' : 'Inativa'}}</strong></label>
                  </div>
                </div>
                <div class="form-group col-12">
                  <label for="form-FICT_OBS">Obs sugestão</label>
                  <textarea id="form-FICT_OBS" class="form-control form-control-sm" v-model="itemModal.FICT_OBS" placeholder="Escreva bastante"></textarea>
                </div>
                <div class="form-group col-12">
                  <div class="form-group form-check m-0">
                    <input id="form-FICT_ADD_COFRE" type="checkbox" class="form-check-input" v-model="itemModal.FICT_ADD_COFRE">
                    <label for="form-FICT_ADD_COFRE" class="form-check-label">Adicionar em cofre: <strong>{{itemModal.FICT_ADD_COFRE ? 'Sim' : 'Não'}}</strong></label>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer p-2">
            <button type="button" class="btn btn-sm btn-outline-info" @click="salvar">Salvar</button>
            <button v-if="itemModal.FICT_ID != 'novo'" type="button" class="btn btn-sm btn-outline-danger" @click="deletar">Deletar</button>
            <button type="button" class="btn btn-sm btn-outline-secondary" data-dismiss="modal">Fechar</button>
          </div>
        </div>
      </div>
    </div>

  </section>
</template>

<script>
  const empyt_categoria = {
    FICT_ID: 'novo',
    FICT_DESCRICAO: '',
    FICT_OBS: '',
    FICT_ADD_COFRE: 0,
    FICT_STATUS: 1,
    FIGP_ID: null
  }

  import {mapState} from 'vuex'
  import service from '@/service.js'

  export default {

    components: {},

    computed: { ...mapState(['usuario']) },

    data() {
      return {
        time: false,
        categorias: [],
        items: [],

        FINC_ID: 0,
        FIGP_ID: 0,
        btns: true,

        itemModal: {},

        filtro: {
          FINC_ID: 0,
          FIGP_ID: 0,
          btn: true
        },
      }
    },
    
    mounted () {
      if( !this.$store.getters.F_CarteiraPainel ){
        this.FINC_ID = this.$store.getters.F_FINC_ID

        let grupos = this.$store.getters.F_GruposAtivos(this.FINC_ID)
        if(grupos.length) this.FIGP_ID = grupos[0].FIGP_ID

        if(this.FIGP_ID) {
          this.getDados()
          this.btns = false
        }
      }
    },

    methods: {
      getDados(commit){
        this.categorias = [];
        this.time = false;

        setTimeout( () => {
          let option = {}
          option.USUA_ID = this.usuario.USUA_ID
          option.FINC_ID = this.FINC_ID
          option.FIGP_ID = this.FIGP_ID

          service.config.financ.categoria.get(option).then( ({STATUS, data, msg}) => {
            if(STATUS == 'success'){
              this.categorias = data
              this.categorias.push(empyt_categoria)
              this.items = this.categorias.filter( g => g.FICT_ID != 'novo' )

              if(commit) this.$store.commit('FINANCA_CATEGORIA', this.items);
            }
            this.time = true
          })

        }, service.timeLoading)
      },

      setItem(FICT_ID){
        
        service.initForm([
          'FICT_DESCRICAO',
          'FIGP_ID',
        ])

        this.itemModal = this.categorias.filter( i => i.FICT_ID == FICT_ID)[0]
      },

      salvar(){
        let data = {}
        data.FICT_DESCRICAO = this.itemModal.FICT_DESCRICAO
        data.FICT_OBS       = this.itemModal.FICT_OBS
        data.FICT_ADD_COFRE = this.itemModal.FICT_ADD_COFRE ? 1 : 0
        data.FICT_STATUS    = this.itemModal.FICT_STATUS ? 1 : 0
        data.FIGP_ID        = this.itemModal.FIGP_ID
        data.FICT_ID        = this.itemModal.FICT_ID

        let option = {}
        option.USUA_ID = this.usuario.USUA_ID
        option.FICT_ID = this.itemModal.FICT_ID
        option.data = data

        let checkForm = {}
        checkForm.FIGP_ID = this.itemModal.FIGP_ID
        checkForm.FICT_DESCRICAO = this.itemModal.FICT_DESCRICAO

        setTimeout( () => {
          if(service.checkForm(checkForm)){
            if(this.itemModal.FICT_ID == 'novo'){
              service.config.financ.categoria.post(option).then( ({STATUS, msg}) => {
                if(STATUS == 'success'){
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Categoria cadastrada!' }) // message
                  this.getDados('commit')
                }else{
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: 'Erro ao cadastrar, tente novamente!' }) // message
                }
              })
            } else {
              service.config.financ.categoria.put(option).then( ({STATUS, msg}) => {
                if(STATUS == 'success'){
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Categoria atualizada!' }) // message
                  this.getDados('commit')
                }else{
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: 'Erro ao atualizar, tente novamente!' }) // message
                }
              })
            }
            this.$store.dispatch("F_Categorias")
            service.closeModal('FinancaModalCategoria-close')
          }
        }, service.timeLoading)
      },

      deletar(){
        var comfirm = confirm("ao excluir será apagado permanentemente as informações, deseja continuar?")

        if(comfirm){
          let option = {}
          option.FICT_ID = this.itemModal.FICT_ID
          
          service.config.financ.categoria.del(option).then( ({STATUS, data, msg}) => {
            if(STATUS == 'success'){
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Categoria excluida!' }) // message
              this.getDados();

            }else{
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: 'Erro ao excluir, tente novamente!' }) // message
            }
          })
        }
        this.$store.dispatch("F_Categorias")
        service.closeModal('FinancaModalCategoria-close')
      }

    },

    watch: {
      'FINC_ID'(newValue, oldValue) {
        this.FINC_ID = newValue

        if( newValue == 0){
          this.btns = true
        }else{
          this.btns = false
          this.getDados()
        }
      },
      'FIGP_ID'(newValue, oldValue) {
        this.FIGP_ID = newValue

        if( newValue == 0){
          this.btns = true
        }else{
          this.btns = false
          this.getDados()
        }
      }
    },
  }
</script>

<style scoped>

</style>