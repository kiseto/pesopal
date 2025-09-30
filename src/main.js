import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import VueApexCharts from 'vue3-apexcharts'
import VueGoodTablePlugin from 'vue-good-table-next'
import 'vue-good-table-next/dist/vue-good-table-next.css'


const app = createApp(App)

//Vue Router
app.use(router)

//Vue Good Table
app.use(VueGoodTablePlugin)

//Vue Apex Charts
app.use(VueApexCharts)
app.component('ApexChart', VueApexCharts)

//Mount App
app.mount('#app')
