<template>
    <div v-show="!nodata" class="module-box my-2 sm:my-4 mx-4 px-4 py-4 shadow rounded bg-white">
        <Loader :loading="loading"></Loader>
        <span class="w-name text-lg font-bold text-gray-800">{{name}}</span>
        <div class="w-value_holder flex items-baseline">
            <p class="w-value font-bold text-2xl">{{value}} {{currencyCode}}</p>
            <span class="w-pr ml-2" :class="pr >= 0 ? 'text-green-600':'text-red-600'">{{ pr ? pr + ' %' : '' }}</span>
            <span v-if="pr_ != undefined">/</span>
            <span v-if="pr_ != undefined" class="w-pr ml-2" :class="pr_ >= 0 ? 'text-green-600':'text-red-600'">{{ pr_ ? pr_ + ' %' : '' }}</span>
        </div>
        <p class="w-desc">{{desc}}</p>
        <div class="table-holder">
            <apexcharts :height="height" :options="chartOptions" :series="series"></apexcharts>
        </div>
    </div>
</template>


<script>


    import VueApexCharts from "vue3-apexcharts";
    import Loader from "./Loader";

    export default {
        components: {
            Loader,
            apexcharts: VueApexCharts,
        },
        props:['name','desc','nodata','loading','value','chartOptions','series','pr','pr_','currencyCode','height'],
        data: function() {
            return {
            }
        },
        methods: {

        },
        mounted() {
            console.log(this.chartOptions);
        }
    }

</script>
