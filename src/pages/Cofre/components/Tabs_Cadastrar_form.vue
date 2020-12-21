<template>
  <section>
    <form>
      <div class="row">

        <div class="px-1 px-md-3 mb-2 form-group col-6 col-md-4 col-xl-2">
          <label for="form-COCT_ID">Carteira</label>
          <select class="form-control form-control-sm" id="form-COCT_ID" v-model="item.COCT_ID" @change="getProposito">
            <option value="">Selecione...</option>
            <option v-for="(c,i) in $store.getters.C_CarteirasAtivas" :key="i" :value="c.COCT_ID">{{c.COCT_DESCRICAO}}</option>
          </select>
        </div>
        
        <div class="px-1 px-md-3 mb-2 form-group col-6 col-md-4 col-xl-2">
          <label for="form-COTP_ID">Tipo</label>
          <select class="form-control form-control-sm" id="form-COTP_ID" v-model="item.COTP_ID">
            <option value="">Selecione...</option>
            <option v-for="(t,i) in $store.getters.C_Tipos" :key="i" :value="t.COTP_ID">{{t.COTP_DESCRICAO}}</option>
          </select>
        </div>

        <div class="px-1 px-md-3 mb-2 form-group col-6 col-md-4 col-xl-2">
          <label for="form-COIT_VALOR">Valor</label>
          <input class="form-control form-control-sm" type="number" step="0.01" id="form-COIT_VALOR" v-model="item.COIT_VALOR">
        </div>
        
        <div class="px-1 px-md-3 mb-2 form-group col-6 col-md-4 col-xl-2">
          <label for="form-COIT_DATA">Data</label>
          <input class="form-control form-control-sm" type="date" id="form-COIT_DATA" v-model="item.COIT_DATA">
        </div>

        <div class="px-1 px-md-3 mb-2 form-group col-12 col-md-12">
          <label for="form-COIT_OBS">Observação</label>
          <textarea class="form-control form-control-sm" id="form-COIT_OBS" v-model="item.COIT_OBS" rows="5" ></textarea>
        </div>
    
        <div class="px-1 px-md-3 mb-2 form-group col-12">
          <div class="form-group form-check m-0">
            <input type="checkbox" class="form-check-input" id="form-COIT_STATUS" v-model="item.COIT_STATUS">
            <label class="form-check-label" for="form-COIT_STATUS">{{item.COIT_STATUS ? 'Registro Ativo' : 'Registro Inativo'}}</label>
          </div>
        </div>

        <div class="px-1 px-md-3 mb-2 form-group col-12">
          <label for="form-COIT_PROPOSITO">Proposito</label>

          <div class="row">

            <div class="col-6 col-md-4 col-xl-3 px-1 px-md-2 m-0 mb-2">
              <div class="p-1 p-md-2 border d-flex justify-content-center align-items-center">
                <input class="form-control form-control-sm" type="text" v-model="item.COIT_PROPOSITO">
              </div>
            </div>

            <div v-for="(text,i) in arrProposito.LISTA" :key="i" class="col-6 col-md-4 col-xl-3 px-1 px-md-2 m-0 mb-2">
              <div class="p-1 p-md-2 border d-flex justify-content-center align-items-center">
                <input class="mr-2" :id="`texto-${i}`" type="radio" :value="text.COIT_PROPOSITO" v-model="item.COIT_PROPOSITO">
                <label class="m-0" :for="`texto-${i}`">{{text.COIT_PROPOSITO}}</label>
              </div>
            </div>

          </div>
        </div>

      </div>

      <hr>
      
      <button type="button" class="btn btn-sm btn-outline-info mr-2" :disabled="item.COIT_OBS ? false : true" @click="salvar">Salvar</button>
      <button type="reset" class="btn btn-sm btn-outline-info mr-2">Limpar</button>
    </form>
  </section>
</template>

<script>
  import service from '@/service.js'

  export default {
    
    data() {
      return {
        item: {
          COCT_ID: '',
          USUA_ID: '',
          COTP_ID: '',
          COIT_VALOR: 0,
          COIT_DATA: service.dataHoje(),
          COIT_OBS: '',
          COIT_STATUS: 1,
          COIT_PROPOSITO: '',
        },
        arrProposito: {
          COCT_ID: 0,
          LISTA: []
        },
      }
    },
    
    mounted () {
      if( !this.$store.getters.C_CarteiraPainel ){
        this.item.COCT_ID = this.$store.getters.C_COCT_ID
        this.getProposito()
      }
    },

    methods: {
      salvar(){
        let data = {}
        data.COCT_ID        = this.item.COCT_ID
        data.COTP_ID        = this.item.COTP_ID
        data.COIT_VALOR     = this.item.COIT_VALOR
        data.COIT_DATA      = this.item.COIT_DATA
        data.COIT_OBS       = this.item.COIT_OBS
        data.COIT_STATUS    = this.item.COIT_STATUS ? 1 : 0
        data.COIT_PROPOSITO = this.item.COIT_PROPOSITO
        data.USUA_ID        = this.$store.getters.USUA_ID

        let option = {}
        option.USUA_ID = this.$store.getters.USUA_ID
        option.COCT_ID = this.item.COCT_ID
        option.data = data

        let checkForm = {}
        checkForm.COCT_ID    = this.item.COCT_ID
        checkForm.COTP_ID    = this.item.COTP_ID
        checkForm.COIT_VALOR = this.item.COIT_VALOR
        checkForm.COIT_DATA  = this.item.COIT_DATA
        checkForm.COIT_OBS   = this.item.COIT_OBS

        setTimeout( () => {
          if( service.checkForm(checkForm) ){
            
            service.cofre.item.post(option).then( ({STATUS, data, msg}) => {

              if(STATUS == 'success'){
                  this.item = {
                    COCT_ID: '',
                    USUA_ID: '',
                    COTP_ID: '',
                    COIT_VALOR: 0,
                    COIT_DATA: service.dataHoje(),
                    COIT_OBS: '',
                    COIT_STATUS: 1,
                    COIT_PROPOSITO: '',
                  }
                
                  service.initForm([
                  'COCT_ID',
                  'COTP_ID',
                  'COIT_VALOR',
                  'COIT_DATA',
                  'COIT_OBS',
                  ])
                
                this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Item cadastrado!' }) // message

              } else if (STATUS == "error") {
                this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: msg });

              } else if (STATUS == "token") {
                this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: service.arrMessage, });
                this.$store.commit("SET_LOGIN", false);

              }

              this.time = true;
            })
          }
        }, service.timeLoading)
      },

      getProposito(){
        if(this.item.COCT_ID > 0 && this.arrProposito.COCT_ID != this.item.COCT_ID){

          // sempre que mudar no FORM a carteira para qual sera gravado o ITEM
          // busca os propositos relationados a carteira selecionada
          this.arrProposito.COCT_ID = this.item.COCT_ID;


          var endPoint = "";
          endPoint += "proposito";
          endPoint += `?usuario=${this.$store.getters.USUA_ID}`;
          endPoint += `&COCT_ID=${this.item.COCT_ID}`;

          service.cofre.busca(endPoint).then( ({ STATUS, data, msg }) => {
            if (STATUS == "success") {
              this.arrProposito.LISTA = data

            } else if (STATUS == "error") {
              this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: msg });

            } else if (STATUS == "token") {
              this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: service.arrMessage, });
              this.$store.commit("SET_LOGIN", false);

            }
          });
        }
      },

    },
  }
</script>