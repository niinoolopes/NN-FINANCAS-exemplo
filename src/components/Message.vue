<template>
  <div 
    id="box-message" 
    class="col-10 col-sm-8 col-md-6 col-lg-4 col-xl-3 mt-3 mr-3 p-3 rounded"
    :class="msg.active ? `active ${msg.border}` : '' ">

    <h3>Mensagem</h3>

    <hr class="m-0 mb-2">

    <span>{{msg.texto}}</span>

  </div>
</template>

<script>
  import { mapState } from 'vuex';

  export default {
    data() {
      return {
        msg: {}
      }
    },

    computed: {
      ...mapState(['message'])
    },

    created () {},

    mounted () {},

    methods: {
      get(){
        this.msg = this.message
        this.msg.border = (this.message.type == 'ok') ? 'box-success' : 'box-danger'
      },

      end(){
        const time = this.msg.time == 'long' ? 4500 : 2500
        setTimeout(() => {
          this.$store.commit('SET_MESSAGE',{ ...this.msg, active: false })
        }, time);
      }
    },

    watch: {
      message(newValue, oldValue) {
        this.get()
      },
      msg(newValue, oldValue) {
        if(newValue.active) this.end()
      }
    },
  }
</script>

<style scoped>
  #box-message{
    position: absolute;
    top: 0;
    right: 0;
    z-index: 1051;
    transition: 0.3s;
    border-width: 1px;
    border-style: solid;
    opacity: 0;
    top: -25px;
    visibility: hidden;
    background: #fff;
  }
  #box-message.active{
    opacity: 1;
    top: 0;
    visibility: visible;
  }
  .box-success{
    background: hsla(135, 60%, 97%, 1) !important;
    border-color: hsla(135, 60%, 20%, 1);
    box-shadow: inset 0 0 0.75rem 0 hsla(135, 60%, 20%, .35), 0 0.5rem 1rem hsla(135, 60%, 20%, .35);
  }
  .box-danger{
    background: hsla(355, 70%, 97%, 1) !important;
    border-color: hsla(355, 70%, 20%, 1);
    box-shadow: inset 0 0 0.75rem 0 hsla(355, 70%, 20%, .35), 0 0.5rem 1rem hsla(355, 70%, 20%, .35);

  }
</style>