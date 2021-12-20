<template>
    <widget-chart
        :nodata="nodata"
        :loading="loading"
        name="New and Returning Customers"
        desc="New and Returning Customers over time"
        :value="newCustomersCount+ ' / '+returningCustomersCount"
        :pr="pr"
        :pr_="pr_"
        :chartOptions="chartOptions"
        :series="series"
    />

</template>

<script>
    import WidgetChart from "../WidgetChart";

    export default {
        name: 'Chart',
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
                pr_:null,
                loading:false,

                newCustomersCount:0,
                returningCustomersCount:0,
                newCustomersCount_:0,
                returningCustomersCount_:0,

                chartOptions: {
                    chart: {
                        type:'area',
                        id: 'vuechart-example',
                        stacked: false,
                        toolbar: {
                            show: false
                        },
                    },

                    colors: ['#c3c3c3','#008FFB','#5a5a5a','#0c9a66'],
                    stroke: {
                        width: [2, 2, 2, 2],
                        curve: 'straight',
                        dashArray: [4, 0, 4,0]
                    },
                    dataLabels: {
                        enabled: false
                    },
                    xaxis: {
                        type: 'datetime',
                        categories: [],
                    },


                },
                series: [
                    {name: '',data: []},
                    {name: '',data: []},
                    {name: '',data: []},
                    {name: '',data: []},
                ],
            }
        },
        methods: {
            getData() {
                this.loading = true;
            // GET request using axios with error handling
             this.$store.dispatch('getData' ,{url:'/charts/newCustomers/' , id:this.selectedConnection , period:this.selectedPeriod })
                    .then(response => {

                        this.nodata = response[0].data.chart.nodata & response[1].data.chart.nodata;

                        this.newCustomersCount = response[0].data.newCustomersCount
                        this.returningCustomersCount = response[0].data.returningCustomersCount
                        this.newCustomersCount_ = response[1].data.newCustomersCount
                        this.returningCustomersCount_ = response[1].data.returningCustomersCount


                        this.chartOptions.xaxis.categories.splice(0,this.chartOptions.xaxis.categories.length);
                        this.series[0].data.splice(0,this.series[0].data.length);
                        this.series[1].data.splice(0,this.series[1].data.length);
                        this.series[2].data.splice(0,this.series[2].data.length);
                        this.series[3].data.splice(0,this.series[3].data.length);

                        for (let item of response[0].data.chart.x) this.chartOptions.xaxis.categories.push(item);
                        for (let item of response[1].data.chart.y) this.series[0].data.push(item);
                        for (let item of response[1].data.chart1.y) this.series[2].data.push(item);
                        for (let item of response[0].data.chart.y) this.series[1].data.push(item);
                        for (let item of response[0].data.chart1.y) this.series[3].data.push(item);

                        this.series[0].name='New '+response[0].data.chart_name

                        this.series[1].name='New '+response[1].data.chart_name

                        this.series[2].name='Returning ' + response[0].data.chart_name
                        this.series[3].name='Returning ' + response[1].data.chart_name

                        if (this.newCustomersCount_ != 0){
                            this.pr =Math.round(((this.newCustomersCount/this.newCustomersCount_)*100)-100);
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
