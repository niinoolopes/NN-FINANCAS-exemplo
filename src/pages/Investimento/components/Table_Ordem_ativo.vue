<template>
  <Table colspan='8' :timeTable='time' :itemsTable='items' >
    <template v-slot:thead>
      <th class="responsivo text-center" scope="col">#</th>
      <!-- <th class="th-m-100 text-left"  scope="col">Corretora</th> -->
      <th class="th-m-120 text-left"  scope="col">Tipo Invest.</th>
      <th class="th-m-100 text-left"  scope="col">Tipo Ativo</th>
      <th class="th-m-100 text-left"  scope="col">Ativo</th>
      <th class="th-m-85  text-left"  scope="col">C/V</th>
      <th class="th-m-85 text-center" scope="col">Quantidade</th>
      <th class="th-m-85 text-center" scope="col">Preço Uni</th>
      <th class="th-m-85 text-center" scope="col">Preço Total</th>
      <th class="th-m-85 text-center" scope="col">ID Oper.</th>
      <th class="th-m-85 text-center" scope="col">Data Oper.</th>
      <th class="th-m-85 text-center" scope="col">Status</th>
      <th v-if="btn"></th>
    </template>
    <template v-slot:tbody>
      <tr v-for="(item,i) in items" :key="i">
        <td class="responsivo text-center">{{item.INIT_ID}}</td>
        <!-- <td class="th-m-100 text-left"    >{{item.INCR_DESCRICAO}} <small>({{item.INCR_ID}})</small></td> -->
        <td class="td-m-120 text-left"    >{{item.INTP_DESCRICAO}} <small>({{item.INTP_ID}})</small></td>
        <td class="td-m-100 text-left"    >{{item.INAT_DESCRICAO}} <small>({{item.INAT_ID}})</small></td>
        <td class="td-m-100 text-left" >
          <router-link :to="{name: 'InvestimentoAtivoAdm', params:{ INAV_ID: item.INAV_ID }}">
            {{item.INAV_CODIGO}} <small>({{item.INAV_ID}})</small>
          </router-link>
        </td>
        <td class="td-m-85  text-left"    >{{item.INIT_CV == 'C' ? 'Compra' : 'Venda'}} <small>({{item.INIT_CV}})</small></td>
        <td class="td-m-85 text-center"   >{{item.INIT_COTAS}}</td>
        <td class="td-m-85 text-center"   >{{item.INIT_PRECO_UNICO | vReal}}</td>
        <td class="td-m-85 text-center"   >{{item.INIT_PRECO_TOTAL | vReal}}</td>
        <td class="td-m-85 text-center"   >{{item.INOD_ID}}</td>
        <td class="td-m-85 text-center"   >{{item.INOD_DATA | convertDate}}</td>
        <td class="td-m-85 text-center"   >{{item.INIT_STATUS ? 'Ativo' : 'Inativo'}}</td>
        <td>
          <div class="d-flex">
            <TdiconEdit v-if="btn.indexOf('modal') >= 0"
              target='InvestimentoOrdemItem'
              :setItem='setItem'
              :ID='item.INIT_ID'
            /> 

            <TdiconShare v-if="btn.indexOf('linkOrdem') >= 0"
              to='InvestimentoOrdemAdm'
              :params='{ INOD_ID: item.INOD_ID }'
            />
          </div>
        </td>
      </tr>
    </template>
  </Table>
</template>

<script>
  export default {
    props: ['items', 'setItem', 'time', 'btn'],

    components: {
      Table:       () => import('@/components/Table'),
      TdiconEdit:  () => import('@/components/Td_iconEdit'),
      TdiconShare: () => import('@/components/Td_iconShare'),
    },
  }
</script>