<script>
// https://codesandbox.io/s/93m1lpjrvr?file=/src/components/PieChart.vue:0-998

import { HorizontalBar } from "vue-chartjs";
// import service from "@/service.js"

export default {
  extends: HorizontalBar,

  props: ['arrDados','color','arrLabels'],
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

  data() {
    return {
      datasets: [],
      labels: [],
    }
  },

  mounted() {
    this.renderGrafico()
  },

  methods: {
    renderGrafico(){
      var ColorBackground = []
      var ColorBorder     = []


      if(this.color == undefined){
        ColorBackground.push('rgba( 100, 100, 100, 0.9)')
        ColorBorder    .push('rgba( 100, 100, 100, 0.50)')

      } else if( this.color == 'financa') {
        ColorBackground.push('rgba(  40, 160,  75, 0.9)')
        ColorBorder    .push('rgba(  40, 160,  75, 0.5)')
        
        ColorBackground.push('rgba( 220,  50,  75, 0.9)')
        ColorBorder    .push('rgba( 220,  50,  75, 0.5)')
        
        ColorBackground.push('rgba( 100, 100, 100, 0.9)')
        ColorBorder    .push('rgba( 100, 100, 100, 0.5)')
        
        ColorBackground.push('rgba(  25, 125, 200, 0.9)')
        ColorBorder    .push('rgba(  25, 125, 200, 0.5)')

      }

      // --

      this.datasets = []

      if( Array.isArray(this.arrDados) ) {

        this.arrDados.map( ({label, valores}, index) => {
          this.datasets.push(
            {
              label: label,
              data: valores,
              "fill": false,
              "lineTension": 0.0,
              "backgroundColor": ColorBackground[index],
              "borderColor": ColorBorder[index]
            }
          );
        })

      } else {
        this.datasets.push(
          {
            label: 'Gráfico',
            data: this.arrDados.valores,
            "fill": false,
            "lineTension": 0.0,
            "backgroundColor": ColorBackground[0],
            "borderColor": ColorBorder[0]
          }
        )
      }
      
      // --

      this.labels = []

      if(this.labels){
        this.labels = this.arrLabels

      } else {
        this.arrDados.map( (value,index) => {
          this.labels.push(index)
        })

      }



      this.renderChart(
        {
          labels: this.labels,
          datasets: this.datasets
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
