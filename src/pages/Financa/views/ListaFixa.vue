<template>
  <TemplateDefault title="Lista fixa">
    
    <Alert v-if="$store.getters.F_CarteiraPainel" tipo="warning" texto="Selecione uma carteira no painel!"/>

    <PageNav>
      <!-- <template v-slot:menu></template> -->
      <template v-slot:btn>
        <ButtonplusItem :setItem='setItem' novo='novo' target='FinancaModal-ListaFixa' class="mr-1"/>
        <ButtongetDados :getDados='getDados' class="mr-1" />
        <Buttonellipsis target='modalAcoes' />
      </template>
    </PageNav>


    <PageSection>
      <Filtro :itemsFiltro="itemsFiltro" :setFiltro="setFiltro"/>

      <TableListaFixa :time="time"  :items="itemsTable"  :setItem="setItem" />
    </PageSection> 

    <ModalFormListaFixa :FNLF="FNLF" :getDados="getDados" />

    <Modal modal='modal-md' :salvar="acaoDo" idItem="modalAcoes" :timeModal="true" idModal="modalAcoes" >
      <Column class="col-12 col-md-6">
        <label class="m-0" for="FINC_ID"><small>Ação</small></label>
        <select class="form-control form-control-sm" v-model="acao.acao">
          <option value="0">Selecione ...</option>
          <option value="salvar">Salvar</option>
          <option value="deletar">Deletar</option>
          <option value="inserir">Inserir</option>
        </select>
      </Column>
      
      <Column class="d-none d-md-block col-md-6"></Column>

      <Column class="form-group col-12 col-md-6" :class="acao.acao == 'salvar' ? '' : 'd-none'">
        <label class="m-0" for="FNIS_ID"><small>Situação</small></label>
        <select class="form-control form-control-sm" v-model="acao.FNIS_ID">
          <option value="0">Selecione...</option>
          <option v-for="(s,i) in $store.getters.F_Situacoes" :key="i" :value="s.FNIS_ID" >{{s.FNIS_DESCRICAO}}</option>
        </select>
      </Column>
      
      <Column class="form-group col-12 col-md-6" :class="acao.acao == 'salvar' ? '' : 'd-none'">
        <label class="m-0" for="FNIT_DATA"><small>Data</small></label>
        <input class="form-control form-control-sm" type="month" v-model="acao.FNIT_DATA">
      </Column>
    </Modal>

  </TemplateDefault>
</template>

<script>
  import service from "@/service.js";

  export default {

    components: { 
      ButtongetDados:     () => import('@/components/Button_getDados'),
      ButtonplusItem:     () => import('@/components/Button_plusItemModal'),
      Buttonellipsis:     () => import('@/components/Button_ellipsis'),
      Filtro:             () => import('@/pages/Financa/components/Section_Filtro_table'),
      TableListaFixa:     () => import('@/pages/Financa/components/Table_ListaFixa'),
      ModalFormListaFixa: () => import('@/pages/Financa/components/Modal_Form_ListaFixa'),
      Modal:              () => import('@/components/Modal'),
    },
    
    data() {
      return {
        items: [],
        itemsTable: {},
        itemsFiltro: [],

        FNLF: 'init',
        time: false,

        acao: {
          acao: 0,
          FNIS_ID: 0,
          FNIT_DATA: 0,
        }
      }
    },
    
    created () {
      if( !this.$store.getters.F_CarteiraPainel ){
        setTimeout(() => { this.getDados() }, service.timeLoading);
      }
    },

    methods: {
      makeUrl() {
        var url = ''
        url += 'lista'
        url += `?usuario=${this.$store.getters.USUA_ID}`
        url += `&FINC_ID=${this.$store.getters.F_FINC_ID}`
        url += `&retorno=F_listaFixa`
        return url
      },

      getDados() {
        this.items      = [];
        this.itemsTable = 'init';
        this.time = false;

        setTimeout(() => {
          service.busca( this.makeUrl() ).then(({ STATUS, data, msg }) => {
            if (STATUS == "success") {
              var {items, itemsFiltro} = data.F_listaFixa
              items = service.finc.formatItemsToTable( items )
              this.items       = items;
              this.itemsTable  = items;
              this.itemsFiltro = itemsFiltro

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

      setItem(FNLF){
        this.FNLF = FNLF;
      },

      setFiltro(arrFiltro){
        this.itemsTable = service.finc.filtroItems(arrFiltro, this.items.filter( i => i.FNLF_ID != 'novo'))
      },

      // AÇÃO

      acaoOpen(){
        this.acao.acao = 0;
      },

      acaoDo(){
        let items = this.items.filter( i => i.selected )

        if(this.acao.acao == 'deletar'){
          if(!confirm('Ao excluir não será possivel restaurar, você confirma essa ação?')){
            return false;
          }
        }

        setTimeout(() => {
          items.map( item => {

            if(this.acao.FNIT_DATA){ // Quando preencher o campo atualiza ao enviar
              var itemData = new Date(item.FNIT_DATA)
              var dia = itemData.getDate()
              var newData = `${this.acao.FNIT_DATA}-${dia}`
              item.FNIT_DATA = newData
            }

            if(this.acao.FNIS_ID){ // Quando preencher o campo atualiza ao enviar
              item.FNIS_ID = this.acao.FNIS_ID
            }

            let data = {}
            data.FNIT_VALOR = item.FNIT_VALOR
            data.FNIT_DATA = item.FNIT_DATA
            data.FNIT_OBS = item.FNIT_OBS
            data.FNIT_STATUS = item.FNIT_STATUS
            data.FNIS_ID = item.FNIS_ID
            data.FITP_ID = item.FITP_ID
            data.FIGP_ID = item.FIGP_ID
            data.FICT_ID = item.FICT_ID
            data.FINC_ID = item.FINC_ID
            data.USUA_ID = item.USUA_ID

            let option = {}
            option.USUA_ID = this.$store.getters.USUA_ID
            option.FNLF_ID = item.FNLF_ID
            option.data = data

            if(this.acao.acao == 'inserir') service.finc.item.post(option)
            if(this.acao.acao == 'salvar' ) service.finc.listaFixa.put(option)
            if(this.acao.acao == 'deletar') service.finc.listaFixa.del(option)

          })
        }, service.timeLoading);


        setTimeout(() => {  

          this.getDados()  
          
          this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Seleção processada' }) // message

          document.getElementById('modalAcoes-close').click()

        }, service.timeLoading);

      },
    },

    watch: {
      '$store.getters.F_FINC_ID'(newValue, oldValue) {
        this.getDados()
      },
      '$store.getters.Periodo'(newValue, oldValue) {
        this.getDados()
      }
    },
  }
</script>

<style scoped>
  .form-check-input {
    position: unset !important;
  }
</style>