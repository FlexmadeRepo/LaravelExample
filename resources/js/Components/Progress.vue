<template>

    <div class="py-6 sm:py-8 lg:py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="sm:py-4 sm:px-4">
                    <div v-if="batch" class="mb-5">
                        <div class="mb-3">
                            <div class="shadow w-full bg-gray-100 rounded">
                                <div class="bg-green-400 text-xs py-1 font-medium text-center rounded" :style="'width:'+ getPr() + '%;'">
                                    <span class="px-1">{{getPr()}}%</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-base">
                            <p><strong>Progress:</strong> {{getPr()}}% </p>
                            <p><strong>Total jobs:</strong> {{batch.total_jobs}}</p>
                            <p><strong>Pending jobs:</strong> {{batch.pending_jobs}}</p>
                            <p><strong>Failed jobs:</strong> {{batch.failed_jobs}}</p>
                        </div>
                    </div>
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                            <tr class="bg-gray-100">
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="row in log" :key="row.id">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ formatTime(row.created_at) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">{{ row.status }}</span></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ row.message }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>


    import moment from "moment";

    export default {
        name: 'Progress',
        components: {

        },
        props:['connection_id'],
        data: function() {
            return {
                batch: {},
                log:[],
                timer:0,
            }
        },
        methods: {
              update : function () {
                  self = this;
                  axios.get('/connections/progress/'+this.connection_id).then(function (resp) {
                      self.batch = resp.data.batch;
                      self.log = resp.data.log;
                      if (self.batch && self.batch.pending_jobs == 0) self.timer=0;
                  })
              },
            getPr : function () {
                  let pr = (this.batch.total_jobs && this.batch.total_jobs!=0) ? (this.batch.total_jobs-this.batch.pending_jobs)/this.batch.total_jobs*100 : 0
               return Math.round(pr);
            },
            formatTime: function (t) {
               return moment(t).format('DD-MM-YYYY hh:mm:ss');
            }
        },
        watch: {
            timer: function (value, oldValue) {
                if (value <= 0) return
                if (++value == oldValue || oldValue <= 0) setTimeout(() => {
                    --this.timer
                        this.update();
                }, 5000)
            }
        },

        mounted() {
            this.update();
            this.timer = 1000;
        }
    }

</script>
