<script>
import { Pie } from 'vue-chartjs'

export default {
  extends: Pie,
  props: ['chartdata', 'options'],
  mounted () {
    //this.renderChart(this.chartdata, this.options)
    this.renderChart(this.chartdata, this.options)
  }
}
</script>

<style>
</style>