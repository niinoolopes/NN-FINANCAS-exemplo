<template>
  <div class="border over-x opacity-fetch" :class="time ? '' : 'opacity-fetch-active'" >
    <table class="table table-sm">
      <thead>
        <tr>
          <th scope="col" class="th-data">Data</th>
          <th scope="col" class="th-tipo">Tipo</th>
          <th scope="col" class="th-situacao">Situação</th>
          <th scope="col" class="th-grupo">Grupo</th>
          <th scope="col" class="th-categoria">Categoria</th>
          <th scope="col" class="th-valor">Valor</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item,i) in items" :key="i">
          <td class="td-data">{{ item.FNIT_DATA | convertDate }}</td>
          <td class="td-tipo" :class="item.FITP_CSS">{{ item.FITP_DESCRICAO }}</td>
          <td class="td-situacao" :class="item.FNIS_CSS"> {{ item.FNIS_DESCRICAO }} </td>
          <td class="td-grupo" >{{ item.FIGP_DESCRICAO }}</td>
          <td class="td-categoria">{{ item.FICT_DESCRICAO }}</td>
          <td class="td-valor">{{ item.FNIT_VALOR }}</td>
          <td>
            <div class="pt-1 d-flex justify-content-center">
              <i @click="setItem(item.FNIT_ID)" class="text-dark cursor-pointer fas fa-share"></i>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>

  export default {
    props: ['time','items','setItem'],
  }
</script>

<style scoped>
.th-grupo ,
.td-grupo {
  min-width: 100px;
  text-align: center;
}
.th-categoria ,
.td-categoria {
  min-width: 100px;
  text-align: center;
}
.th-valor ,
.td-valor {
  min-width: 60px;
}
.th-obs ,
.td-obs {
  min-width: 175px;
  text-align: left;
}
.td-obs {
  overflow: hidden;
}
.th-usuario ,
.td-usuario {
  text-align: center;
}
</style>
