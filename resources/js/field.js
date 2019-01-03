Nova.booting((Vue, router) => {
    Vue.component('index-nova-creatable-belongs-to', require('./components/IndexField'));
    Vue.component('detail-nova-creatable-belongs-to', require('./components/DetailField'));
    Vue.component('form-nova-creatable-belongs-to', require('./components/FormField'));
})
