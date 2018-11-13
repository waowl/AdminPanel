import moment from 'moment'
import Vue from 'vue'

export default {
    filters: Vue.filter('prettyDate', (date) => {
        return moment(date).format('YYYY DD MMMM')
    })
}
