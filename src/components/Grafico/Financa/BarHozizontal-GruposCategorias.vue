<script>
// https://codesandbox.io/s/93m1lpjrvr?file=/src/components/PieChart.vue:0-998

import { HorizontalBar } from "vue-chartjs";

export default {
  extends: HorizontalBar,

  data() {
    return {
      label: [],
      valor: [],
    }
  },

  props: ['labels', 'valores', 'tipo', 'title'],

  mounted() {
    this.renderGrafico()
  },

  methods: {
    renderGrafico(){
      

      
      this.renderChart(
        {
          labels: this.labels,

          datasets: [
            {
              barPercentage: 0.5,
              label: this.title,
              borderWidth: 2,
              data: this.valores,
              "backgroundColor": (this.tipo == 'Receita') ? "rgba( 40, 160,  75, 0.50)" : 'rgba( 220, 50, 75, 0.50)',
              "borderColor":     (this.tipo == 'Receita') ? "rgba( 40, 160,  75, 0.75)" : 'rgba( 220, 50, 75, 0.75)',
            }
          ],
        },
        { 
          responsive: true, 
          maintainAspectRatio: false 
        }
      );
    },
  },
  
  watch: {
    'labels'(newValue, oldValue) {
      this.renderGrafico();
    },
    'valores'(newValue, oldValue) {
      this.renderGrafico();
    }
  },

};


// <template>
//     <HorizontalBar 
//       :labels="labels" 
//       :valores="valores" 
//       :title="title"
//       :styles="{height: height}"/> 
// </template>

// <script>
//   import HorizontalBar from './chat_HorizontalBar2'

//   export default {
//     components: { HorizontalBar },

//     props: ['labels', 'valores','title'],

//     data() {
//       return {
//         height: null
//       }
//     },

//     created () {
//       this.height = (this.labels.length < 5) ? '175px' : `${this.labels.length * 35}px`
//     },

</script>

