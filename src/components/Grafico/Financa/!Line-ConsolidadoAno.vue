<script>
// https://codesandbox.io/s/93m1lpjrvr?file=/src/components/PieChart.vue:0-998

import { Line } from "vue-chartjs";
import service from "@/service.js"

export default {
  extends: Line,

  props: ['arrDados'],
  /* 
   * o arrDados deve serguir o setuinte modelos de matriz:
   * 
   * arrDados = [
   *  { 
   *    label: 'Receita'
   *    valores: [1,2,3,4,5,6,7,8,9,10,11,12]
   *  },
   *  { 
   *    label: 'Despesa'
   *    valores: [1,2,3,4,5,6,7,8,9,10,11,12]
   *  }
   * ]
   *
  */

  // data() {
  //   return {}
  // },

  mounted() {
    this.renderGrafico()
  },

  methods: {
    renderGrafico(){
      var cores = [
        'rgba(040, 167, 069, 0.9)',
        'rgba(220, 053, 069, 0.9)',
        'rgba(052, 068, 064, 0.9)',
        'rgba(000, 125, 250, 0.9)',
      ]

      var datasets = []

      this.arrDados.map( ({label, valores}, index) => {
        
        datasets.push(
          {
            label: label,
            // 'backgroundColor': cores[index],
            data: valores,
            "fill": false,
            "lineTension": 0.0,
            "borderColor": cores[index]
          }
        );
      })
      
      this.renderChart(
        {
          labels: service.arrMeses,
          datasets: datasets
        },
        { 
          responsive: true, 
          maintainAspectRatio: false 
        }
      );
    }

  },

  watch: {
    'arrDados':{
      handler(val){
        this.renderGrafico()
       // do stuff
     },
     deep: true
    }
  },
};
</script>
