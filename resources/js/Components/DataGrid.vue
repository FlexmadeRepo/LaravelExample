<template>
    <table class="w-full">
        <thead class="bg-indigo-100">
            <tr>
                <th v-for="(column, columnKey) in columns"
                    :key="columnKey"
                    class="border px-4 py-2"
                    v-html="column.title"
                    :style="column.style"
                />
            </tr>
        </thead>
        <tbody class="bg-white">
            <tr v-if="isFilters()">
                <td v-for="(column, columnKey) in columns"
                    :key="columnKey"
                    class="border px-4 py-2"
                    :style="column.style"
                >
                </td>
            </tr>
            <tr v-for="(row, rowKey) in rows" :key="rowKey">
                <template v-for="(column, columnKey) in columns" :key="`${rowKey}-${columnKey}`">
                    <template v-if="Object.prototype.hasOwnProperty.call(column, 'render')">
                        <td
                            class="border px-4 py-2"
                            :style="column.style"
                            v-html="column.render(row)"
                        />
                    </template>
                    <template v-else-if="Object.prototype.hasOwnProperty.call(column, 'type') && column.type === 'slot'">
                        <td class="border px-4 py-2" :style="column.style">
                            <slot :name="column.name" :row="row" />
                        </td>
                    </template>
                    <template v-else>
                        <td
                            class="border px-4 py-2"
                            :style="column.style"
                            v-html="row[column.name]"
                        />
                    </template>
                </template>
            </tr>
        </tbody>
    </table>
</template>

<script>
export default {
    name: 'DataGrid',
    props: {
        columns: {
            type: Array,
            default:() => ([])
        },
        rows: {
            type: Array,
            default:() => ([])
        }
    },
    methods: {
        isFilters() {
           return this.columns.some(function (i){
                return i.filter ? 1:0;
            })
        }
    }
}
</script>

<style scoped>
.disabled {
    color: #ccc;
}
</style>
