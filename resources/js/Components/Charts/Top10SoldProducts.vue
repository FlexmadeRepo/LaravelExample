<template>
    <widget-table
        :conf="confTable"
        :value="value"
        :currency-code="currencyCode"
        :loading="loading"
        :nodata="nodata"
        name="Top 10 over most sold products">
    </widget-table>

</template>

<script>
    import Percent from "../Percent";
    import WidgetTable from "../WidgetTable";
    export default {
        name: 'Chart',
        components: {
            WidgetTable,
            Percent
        },
        props: ['selectedPeriod', 'selectedConnection'],
        watch: {
            selectedPeriod: function(newVal, oldVal) { // watch it
                console.log('Prop changed: ', newVal, ' | was: ', oldVal)
                this.getData()
            },
            selectedConnection: function(newVal, oldVal) { // watch it
                console.log('Prop changed: ', newVal, ' | was: ', oldVal)
                this.getData()
            },
        },
        data: function() {
            return {
                nodata:false,
                loading:false,
                soldProducts:{},
                value:{},
                confTable:[
                    {'header':'SKU','field':'sku'},
                    {'hide':true},
                    {'header':'Sold','field':'sold_products'},
                    {'header':'','field':'pr'},
                ],
            }
        },
        methods: {
            find (s,array) {
                for (let item of array) {
                    if (item.sku == s) return item;
                }
                return false;
            },
            getData() {
                this.loading = true;
            // GET request using axios with error handling
                this.$store.dispatch('getDataTable' ,{url:'/charts/top10SoldProducts/' , id:this.selectedConnection , period:this.selectedPeriod })
                    .then(response => {

                        this.nodata = !response.data.soldProducts || response.data.soldProducts.length == 0 ? true:false;

                        this.value = response.data.soldProducts
                        this.loading = false;
                    })
                    .catch(error => {
                        this.errorMessage = error.message;
                        console.error("There was an error!", error);
                    });
            }
        }
    }

</script>
