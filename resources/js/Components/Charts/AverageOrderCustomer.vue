<template>
    <widget-chart
        :nodata="nodata"
        :loading="loading"
        name="Average Order Customer"
        desc="Average Order Customer over time"
        :value="value"
        :pr="pr"
        :chartOptions="chartOptions"
        :series="series"
    />
</template>


<script>

    import WidgetChart from "../WidgetChart";

    export default {
        components: {
            WidgetChart,
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
                pr:null,
                loading:false,
                value:0,
                value_:0,
                chartOptions: {
                    chart: {
                        type:'area',
                        id: 'vuechart-example',
                        stacked: false,
                        toolbar: {
                            show: false
                        },

                    },
                    colors: ['#c3c3c3','#008FFB'],
                    stroke: {
                        width: [2, 2],
                        // curve: 'straight',
                        dashArray: [4, 0]
                    },
                    dataLabels: {
                        enabled: false
                    },
                    xaxis: {
                        type: 'datetime',
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
                this.loading = true;
            // GET request using axios with error handling
                this.$store.dispatch('getData' ,{url:'/charts/averageOrderCustomer/' , id:this.selectedConnection , period:this.selectedPeriod })
                    .then(response => {

                        this.nodata = response[0].data.chart.nodata & response[1].data.chart.nodata;

                        this.value = response[0].data.result;
                        this.value_ = response[1].data.result;

                        this.chartOptions.xaxis.categories.splice(0,this.chartOptions.xaxis.categories.length);
                        this.series[0].data.splice(0,this.series[0].data.length);
                        this.series[1].data.splice(0,this.series[1].data.length);

                        for (let item of response[0].data.chart.x) this.chartOptions.xaxis.categories.push(item);
                        for (let item of response[1].data.chart.y) this.series[0].data.push(item);
                        for (let item of response[0].data.chart.y) this.series[1].data.push(item);


                        this.series[0].name=response[0].data.chart_name
                        this.series[1].name=response[1].data.chart_name


                        if (this.value_ != 0){
                            this.pr =Math.round(((this.value/this.value_)*100)-100);
                        } else {
                            this.pr = null;
                        }

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
