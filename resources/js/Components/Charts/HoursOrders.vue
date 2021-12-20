<template>
    <widget-chart
        :nodata="nodata"
        :loading="loading"
        name="Hours Orders"
        desc=""
        value=""
        currencyCode=""
        pr=""
        :chartOptions="chartOptions"
        :series="series"
        height="600"
    />
</template>

<script>

    import Percent from "../Percent";
    import WidgetChart from "../WidgetChart";

    export default {
        components: {WidgetChart,  Percent},
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
                value:0,
                currencyCode:null,
                chartOptions: {
                    chart: {
                        toolbar: {
                            show: false
                        },
                        type:'bar',
                    },
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            barHeight: '75%',
                            dataLabels: {
                                position: 'top',
                            }
                        }
                    },
                    colors: ['#c3c3c3','#008FFB'],
                    dataLabels: {
                        enabled: false
                    },
                    xaxis: {
                        type: 'category',
                        categories: [],
                    },

                },
                series: [{
                    name: '',
                    data: []
                },
                    {
                        name: '',
                        data: [],
                    }
                ]
            }
        },
        methods: {
            getData() {
                this.loading=true;
            // GET request using axios with error handling
                this.$store.dispatch('getDataTable' ,{url:'/charts/hoursOrders/' , id:this.selectedConnection , period:this.selectedPeriod })
                    .then(response => {

                        this.nodata = !response.data.result || response.data.result.length == 0 ? true:false;

                        this.chartOptions.xaxis.categories.splice(0,this.chartOptions.xaxis.categories.length);
                        this.series[0].data.splice(0,this.series[0].data.length);
                        this.series[1].data.splice(0,this.series[1].data.length);

                        for (let item of response.data.result){
                            this.chartOptions.xaxis.categories.push(item.hour);
                            this.series[1].data.push(item.total ?item.total:0);
                            this.series[0].data.push(item['total_']?item['total_']:0);
                        }

                        this.series[0].name=response.data.result.chart_name0
                        this.series[1].name=response.data.result.chart_name1

                        this.currencyCode = response.data.currencyCode;
                        this.loading=false;
                    })
                    .catch(error => {
                        this.errorMessage = error.message;
                        console.error("There was an error!", error);
                        this.loading=false;
                    });
            },
            round(i) {
                if (i) return parseFloat(i).toFixed(2);
            },
        }
    }

</script>
