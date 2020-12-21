<template>
  <Table colspan='11' :timeTable='time' :itemsTable='items' >
    <template v-slot:thead>
      <th scope="col" class="th-m-100 text-left" >Corretora</th>
      <th scope="col" class="th-m-120 text-left" >Tipo</th>
      <th scope="col" class="th-m-100 text-left" >Tipo Ativo</th>
      <th scope="col" class="th-m-100 text-left" >Ativo</th>
      <th scope="col" class="th-m-85  text-left" >Liquidez</th>
      <th scope="col" class="th-m-85  text-left" >Data Venc</th>
      <th scope="col" class="th-m-85 text-center">Quantidade</th>
      <th scope="col" class="th-m-85 text-right" >Bruto</th>
      <th scope="col" class="th-m-85 text-right" >Aplicado</th>
      <th scope="col" class="th-m-85 text-right" >Rend. Total</th>
      <th scope="col" class="th-m-85 text-right" >Div. Total</th>
      <th scope="col" class="th-m-85 text-right" >JSCP Total</th>
      <th scope="col" class="th-m-85 text-right" >Preco Médio</th>
    </template>
    <template v-slot:tbody>
      <tr v-for="(item, i) in items" :key="i" :style="{opacity: (item.COTAS > 0) ? '1' : '0.5'}">
        <td class="td-m-100 text-left" >{{item.INCR_DESCRICAO}} <small>({{item.INCR_ID}})</small></td>
        <td class="td-m-120 text-left" >{{item.INTP_DESCRICAO}} <small>({{item.INTP_ID}})</small></td>
        <td class="td-m-100 text-left" >{{item.INAT_DESCRICAO}} <small>({{item.INAT_ID}})</small></td>
        <td class="td-m-100 text-left" >
          <router-link :to="{name: 'InvestimentoAtivoAdm', params:{ INAV_ID: item.INAV_ID }}">
            {{item.INAV_CODIGO}} <small>({{item.INAV_ID}})</small>
          </router-link>
        </td>
        <td class="td-m-85 text-left"  >{{item.INAV_LIQUIDEZ}}</td>
        <td class="td-m-85 text-left"  >{{item.INAV_VENC | convertDate}}</td>
        <td class="td-m-85 text-center">{{item.COTAS}}</td>
        <td class="td-m-85 text-right" >{{item.BRUTO | vReal }}</td>
        <td class="td-m-85 text-right" >{{item.TOTAL | vReal }}</td>
        <td class="td-m-85 text-right" >{{item.TOTAL_RENDIMENTO | vReal }}</td>
        <td class="td-m-85 text-right" >{{item.TOTAL_DIVIDENDO | vReal }}</td>
        <td class="td-m-85 text-right" >{{item.TOTAL_JSCP | vReal }}</td>
        <td class="td-m-85 text-right" >{{item.PRECO_MEDIO | vReal }}</td>
      </tr>
    </template>
  </Table>
</template>

<script>
  export default {
    props: ['items', 'time'],
    
    components: {
      Table:      () => import('@/components/Table'),
    },
  }
</script>