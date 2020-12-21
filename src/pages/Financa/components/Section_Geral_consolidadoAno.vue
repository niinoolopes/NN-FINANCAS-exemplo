<template>
  <div>

    <PageContentTitle :titulo='titulo' >
      <template v-slot:btn>
        <ButtongetDados :getDados='getDados' border='0px' />
      </template>
    </PageContentTitle>

    <template v-if='elements.indexOf("tabela") >= 0'>
      <Table colspan='13' :timeTable='consolidadoAno.time' :itemsTable='consolidadoAno.itemsTable' >
        <template v-slot:thead>
          <th scope="col" class="th-descricao">Situação</th>
          <th scope="col" class="th-mes">Janeiro</th>
          <th scope="col" class="th-mes">Fevereiro</th>
          <th scope="col" class="th-mes">Março</th>
          <th scope="col" class="th-mes">Maio</th>
          <th scope="col" class="th-mes">Abril</th>
          <th scope="col" class="th-mes">Junho</th>
          <th scope="col" class="th-mes">Julho</th>
          <th scope="col" class="th-mes">Agosto</th>
          <th scope="col" class="th-mes">Setembro</th>
          <th scope="col" class="th-mes">Outubro</th>
          <th scope="col" class="th-mes">Novembro</th>
          <th scope="col" class="th-mes">Dezembro</th>
        </template>
        <template v-slot:tbody> 
          <tr v-for="(item, i) in consolidadoAno.itemsTable" :key="i">
            <td v-for="(value, j) in item" 
              :key="j" 
              :class="j == 0 ? `td-${i+1} text-left` : `td-${i+1} text-right`">
              <span v-if="j==0">{{ value }}</span>
              <span v-else><small>{{ value | vReal }}</small></span>
            </td>
          </tr>
        </template>
      </Table>
    </template>

    <template v-if='elements.indexOf("grafico") >= 0'>

      <br>

      <GraficoLine
        :arrLabels="arrMeses"
        :arrDados="consolidadoAno.itemsGrafico" 
        color="financa"
        :style="{height: '200px'}"
      />
    </template>

  </div>
</template>

<script>
  import service from "@/service.js"

  export default {
    props:['titulo','getDados','consolidadoAno','box'],

    components: {
      ButtongetDados: () => import('@/components/Button_getDados'),
      Table:          () => import('@/components/Table'),
      GraficoLine:    () => import('@/components/Grafico/Line-generico'),
    },

    data() {
      return {
        arrMeses: [],
        elements: ['tabela','grafico']
      }
    },

    created () {
      if(Array.isArray(this.box)){
        this.elements = this.box
      }

      this.arrMeses = service.arrMeses;
    },
  }
</script>

<style scoped>
  .td-1 {
    color: rgba(  40, 160, 70, 0.9);
    font-size: unset;
  }
  .td-2 {
    color: rgba(220, 50, 75, 0.9);
    font-size: unset;
  }
  .td-3 {
    color: rgba(100, 100, 100, 0.9);
    font-size: unset;
  }
  .td-4 {
    color: rgba(25, 125, 200, 0.9);
    font-size: unset;
  }
</style>