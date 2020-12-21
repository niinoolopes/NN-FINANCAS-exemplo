<template>
  <section class="px-1 py-2 border-1 shadow-sm">
    <PageContentTitle :titulo='titulo' />
    
    <div class="mt-2 d-flex flex-wrap over-y">
      <Column class="col-12 col-lg-8 col-xl-6">
        <Table colspan='8' :timeTable='time' :itemsTable='items' >
          <template v-slot:thead>
            <th scope="col" class="th-m-120 text-left" >Tipo</th>
            <th scope="col" class="th-m-85 text-right" >Aplicado</th>
            <th scope="col" class="th-m-85 text-right" >Bruto</th>
            <th scope="col" class="th-m-85 text-right" >Mês Rend.</th>
            <th scope="col" class="th-m-85 text-right" >Mês Divi.</th>
            <th scope="col" class="th-m-85 text-right" >Mês JSCP</th>
            <th scope="col" class="th-m-85 text-right" >Total Rend.</th>
            <th scope="col" class="th-m-85 text-right" >Total Divi.</th>
            <th scope="col" class="th-m-85 text-right" >Total JSCP</th>
          </template>
          <template v-slot:tbody>
            <tr v-for="(item, i) in items" :key="i">
              <td class="td-m-120 text-left">{{item.DESCRICAO}}</td>
              <td class="td-m-85 text-right">{{item.TOTAL | vReal}}</td>
              <td class="td-m-85 text-right">{{item.BRUTO | vReal}}</td>
              <td class="td-m-85 text-right" style="background-color: hsl(188 78% 41% / 0.15)">{{item.MES_RENDIMENTO | vReal}}</td>
              <td class="td-m-85 text-right" style="background-color: hsl(188 78% 41% / 0.15)">{{item.MES_DIVIDENDO | vReal}}</td>
              <td class="td-m-85 text-right" style="background-color: hsl(188 78% 41% / 0.15)">{{item.MES_JSCP | vReal}}</td>
              <td class="td-m-85 text-right" style="background-color: hsl(208 7% 46% / 0.15)">{{item.TOTAL_RENDIMENTO | vReal}}</td>
              <td class="td-m-85 text-right" style="background-color: hsl(208 7% 46% / 0.15)">{{item.TOTAL_DIVIDENDO | vReal}}</td>
              <td class="td-m-85 text-right" style="background-color: hsl(208 7% 46% / 0.15)">{{item.TOTAL_JSCP | vReal}}</td>
            </tr>
          </template>
        </Table>
      </Column>

      <Column class="d-none d-lg-block col-md-6 col-lg-4 col-xl-6"
        v-if='arrDados.labels.length > 0'
      >
        <GraficoPie 
          :arrDados="arrDados" 
          :styles="{maxHeight: height}"
        />
      </Column>
    </div>
  </section>
</template>

<script>
  export default {
    props:['titulo', 'time', 'items', 'items_GRAFICO', 'height'],

    components: {
      Table:      () => import('@/components/Table'),
      GraficoPie: () => import('@/components/Grafico/investimento/Pie-Composicoes') , 
    },

    data() {
      return {
        arrDados: {
          labels: ['...'],
          valores: [0],
        }
      }
    },

    watch: {
      'time'(newValue, oldValue) {
        if(newValue) {
          this.arrDados.labels  = this.items_GRAFICO.labels.splice(0,10)
          this.arrDados.valores = this.items_GRAFICO.valores.splice(0,10)
        }
      }
    },
  }
</script>