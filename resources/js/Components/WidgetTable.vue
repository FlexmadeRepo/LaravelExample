<template>
    <div v-show="!nodata" class="module-box my-2 sm:my-4 mx-4 px-4 py-4 shadow rounded bg-white">
        <Loader :loading="loading"></Loader>
        <span class="w-name text-lg font-bold text-gray-800">{{name}}</span>
        <div class="table-holder">
            <slot>
                <table class="table min-w-full divide-y divide-gray-200 my-2 truncate-table">
                    <thead>
                    <tr>
                        <th scope="col" class="px-1 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">{{conf[0].header}}</th>
                        <th v-if="!conf[1].hide" scope="col"  class="px-1 py-2 text-center text-sm font-medium text-gray-500 uppercase tracking-wider">{{conf[1].header}}</th>
                        <th scope="col"  class="px-1 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">{{conf[2].header}}</th>
                        <th scope="col"  class="px-1 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">{{conf[3].header}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="row in value" :key="row.id">
                        <td class="px-1 py-2 text-xs text-gray-900">
                            <span class="block truncate" :title="row.coupon_code">{{row[(conf[0].field)]}}</span>
                        </td>
                        <td v-if="!conf[1].hide" class="px-1 py-2 text-xs text-center text-gray-900">{{row[(conf[1].field)]}}</td>
                        <td class="px-1 py-2 text-xs text-gray-900 whitespace-nowrap">{{row[(conf[2].field)]}} {{currencyCode}}</td>
                        <td class="px-1 py-2 text-xs text-center">
                            <div class="flex justify-center">
                                <percent :value="round(row[(conf[3].field)])"/>
                                <span v-show="row[(conf[3].field_ex)] != null">&ensp;/&ensp;</span>
                                <percent :value="round(row[(conf[3].field_ex)])"/>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </slot>
        </div>
    </div>
</template>


<script>

    import Loader from "./Loader";
    import Percent from "./Percent";

    export default {
        components: {
            Percent,
            Loader,
        },
        props:['name','nodata','loading','conf','value','currencyCode'],
        data: function() {
            return {
            }
        },
        methods: {
            round(i) {
                if (i) return parseFloat(i).toFixed(0);
            },
        },
        mounted() {
            console.log(this.chartOptions);
        }
    }

</script>
