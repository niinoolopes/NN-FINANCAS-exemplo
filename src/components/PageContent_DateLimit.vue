<template>
  <section class="col-12 px-1 py-2 mb-3 d-md-flex flex-md-wrap justify-content-between align-items-start shadow-sm">

    <div class="div-campos p-0 d-flex flex-wrap">
      <div class="col-12 col-sm-6 col-lg-3 p-0 pr-1 mb-1 m-lg-0">
        <input class="form-control form-control-sm m-0" type="date"   v-model="dataDe" @change="changeCampo" :disabled='campos.disabled'>
      </div>
      <div class="col-12 col-sm-6 col-lg-3 p-0 pr-1 mb-1 m-lg-0">
        <input class="form-control form-control-sm m-0" type="date"   v-model="dataAte" @change="changeCampo" :disabled='campos.disabled'>
      </div>
      <div class="col-12 col-sm-6 col-lg-3 p-0 pr-1 mb-1 m-lg-0">
        <input class="form-control form-control-sm m-0" type="number" v-model="limit"   @change="changeCampo" :disabled='campos.disabled'>
      </div>
    </div>

    <div>
      <ButtonclearDados
        :disabled='btn2'
        :limpar='limpar'
      />

      <ButtongetDados
        :disabled='btn1'
        :getDados='buscar'
        class="ml-1"
      />
    </div>

  </section>
</template>

<script>
  export default {
    props:['campos', 'setCampos', 'buscar'],

    components: {
      ButtongetDados:   () => import('@/components/Button_getDados'),
      ButtonclearDados: () => import('@/components/Button_clearDados'),
    },

    data() {
      return {
        dataDe: '',
        dataAte: '',
        limit: 30,
        btn1: false,
        btn2: false,
        selects: true,
      }
    },

    mounted () {
      this.btn1    = (this.campos.btnDados != undefined || this.campos.btnDados == true) ? this.campos.btnDados : false
      this.btn2    = (this.campos.btnClear != undefined || this.campos.btnClear == true) ? this.campos.btnClear : false
      this.selects = (this.campos.disabled != undefined || this.campos.disabled == true) ? this.campos.disabled : false
    },

    methods: {
      changeCampo() {
        this.setCampos({
          dataDe: this.dataDe,
          dataAte: this.dataAte,
          limit:  this.limit,
        })
      },

      limpar() {

        this.dataDe  = '';
        this.dataAte = '';
        this.limit   = '';

        // --

        setTimeout(() => {
          this.setCampos({
            dataDe: this.dataDe,
            dataAte: this.dataAte,
            limit: this.limit,
          })
        }, 75);

        // --

        setTimeout(() => {
          this.buscar()
        }, 75);

      }

    },

    watch: {
      'campos.dataDe'(newValue, oldValue) {
        if(newValue) this.dataDe = newValue
      },
      'campos.dataAte'(newValue, oldValue) {
        if(newValue) this.dataAte = newValue
      },
      'campos.limit'(newValue, oldValue) {
        if(newValue) this.limit = newValue
      }
    },
  }
</script>