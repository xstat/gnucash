<template>

    <div>

    <gnc-collapsible title="Periods" class="report-wrapper" tree-node="true" expanded="true">

        <gnc-collapsible v-for="(period, index) in periods" :config="period" class="report-period" tree-node="true">

            <gnc-collapsible title="Budget" :data="index" tree-node="true" class="report-budget"></gnc-collapsible>

            <gnc-collapsible title="Revenues" :total="period.revenue" :data="index" tree-node="true" class="report-revenues" v-if="period.revenue" @load="load"></gnc-collapsible>

            <gnc-collapsible title="Credit" :total="period.credit" :data="index" tree-node="true" class="report-credit" v-if="period.credit" @load="load"></gnc-collapsible>

            <gnc-collapsible title="Expenses" :total="period.expense" :data="index" class="report-expenses" tree-node="true" v-if="period.expense" @load="load"></gnc-collapsible>

        </gnc-collapsible>

    </gnc-collapsible>

    </div>

</template>

<script>
import Collapsible from '../components/gnc/collapsible.vue'

export default {
    data() {
        return {
            periods: []
        }
    },
    methods: {
        load(item) {
            debugger;
            setTimeout(() => {

            item.vm.children = [
                {
                    title: 'Activos',
                    total: { sign: 'ARS', amount: '5,123.45' },
                    children: [
                        {
                            title: 'Vivienda',
                            total: { sign: 'ARS', amount: '5,123.45' },
                            children: [
                                {
                                    title: 'Alquiler',
                                    total: { sign: 'ARS', amount: '5,123.45' },
                                },
                                {
                                    title: 'Expensas',
                                    total: { sign: 'ARS', amount: '5,123.45' },
                                },
                            ]
                        }
                    ]
                }
            ]

            }, 1000)
        }
    },
    components: {
        'gnc-collapsible': Collapsible
    },
    mounted() {
        this.periods = [
            {
                title: 'Oct 2016',
                total: { sign: 'ARS', amount: '55,123.45' },
                stats: false,
                profit: { sign: 'ARS', amount: '123.35' },
                revenue: { sign: 'ARS', amount: '3,543.55' },
                expense: { sign: 'ARS', amount: '4,123.35' },
            },
            {
                title: 'Nov 2016',
                total: { sign: 'ARS', amount: '55,123.45' },
                stats: false,
                profit: { sign: 'ARS', amount: '123.35' },
                revenue: { sign: 'ARS', amount: '3,543.55' },
                expense: { sign: 'ARS', amount: '4,123.35' },
            },
            {
                title: 'Dic 2016',
                total: { sign: 'ARS', amount: '55,123.45' },
                stats: false,
                profit: { sign: 'ARS', amount: '123.35' },
                revenue: { sign: 'ARS', amount: '3,543.55' },
                expense: { sign: 'ARS', amount: '4,123.35' },
            },
        ]
    }
}
</script>

<style lang="stylus">

.gnc-cpe-wrapper
    &.report-wrapper
        background: black
        border-color: black
        color: white
        .gnc-cpe-header
            font-weight: bold

    &.report-period
        background: #fff
        color: #333333
        .gnc-cpe-header
            font-weight: normal

    &.report-budget
        background: #d9edf7
        color: #31708f

    &.report-revenues
        background: #dff0d8
        color: #3c763d

    &.report-expenses
        background: #f2dede
        color: #a94442

    &.report-credit
        background: #fcf8e3
        color: #8a6d3b

</style>
