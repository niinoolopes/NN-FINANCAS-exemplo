<template>
  <PageNav>
    <template v-slot:menu>
      <div v-if="camposBusca.itemsTipo !== undefined && Array.isArray(camposBusca.itemsTipo)" class="px-1 col-6 col-sm-4 col-md-2">
          <label class="m-0" for="form-INTP_ID"><small>Tipo de busca</small></label>
          <select class="form-control form-control-sm" id="form-INTP_ID" v-model="camposBusca.tipo" @change="changeBusca(camposBusca)">
            <option v-for="(e,i) in camposBusca.itemsTipo" :key='i' :value="e.value">{{e.label}}</option>
          </select>
      </div>

      <div class="px-1 col-6 col-sm-4 col-md-2 opacity-input" :class="camposBuscaBoolean ? 'opacity-input-active' : ''">
        <label class="m-0" for="dataDe"><small>Data De</small></label>
        <input class="form-control form-control-sm m-0" type="date" id='dataDe' v-model="camposBusca.dataDe" :disabled='camposBuscaBoolean'>
      </div>

      <div class="px-1 col-6 col-sm-4 col-md-2 opacity-input" :class="camposBuscaBoolean ? 'opacity-input-active' : ''">
        <label class="m-0" for="dataAte"><small>Data Ate</small></label>
        <input class="form-control form-control-sm m-0" type="date" id='dataAte' v-model="camposBusca.dataAte" :disabled='camposBuscaBoolean'>
      </div>

      <div class="px-1 col-6 col-sm-4 col-md-2 opacity-input" :class="camposBuscaBoolean ? 'opacity-input-active' : ''">
        <label class="m-0" for="limit"><small>Limite</small></label>
        <input class="form-control form-control-sm m-0" type="number" id='limit' v-model="camposBusca.limit"   :disabled='camposBuscaBoolean'>
      </div>
    </template>
    
    <template v-slot:btn>
      
    <template v-if='btn_getDados !== undefined && typeof(btn_getDados) == `function`'>
      <ButtongetDados :disabled='false' :getDados='btn_getDados' class="mr-1 mb-2" />
    </template>

    <template v-if='btn_plus !== undefined && btn_plus.tipo == `link`' >
      <ButtonplusItemLink :to='btn_plus.to' :disabled='btn_plus.disabled' class="mb-2"/>
    </template>
    
    <template v-if='btn_plus !== undefined && btn_plus.tipo == `modal`' >
      <ButtonplusItemModal :target='btn_plus.target' :novo='btn_plus.novo' :setItem='btn_plus.setItem' class="mb-2"/>
    </template>

    </template>
  </PageNav>
</template>

<script>
  export default {
    props:['changeBusca', 'camposBusca', 'camposBuscaBoolean', 'btn_getDados', 'btn_plus'],

    components: {
      ButtonplusItemLink:  () => import('@/components/Button_plusItemLink'),
      ButtonplusItemModal: () => import('@/components/Button_plusItemModal'),
      ButtongetDados:      () => import('@/components/Button_getDados'),
    },
  }
</script>