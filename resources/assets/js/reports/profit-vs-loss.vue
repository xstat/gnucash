<template>

    <div>

    <gnc-collapsible title="Periods" class="report-wrapper" tree-node="true" expanded="true">

        <gnc-collapsible v-for="(period, index) in periods" :config="period.item" class="report-period" tree-node="true">

            <gnc-collapsible title="Budget" :data="index" tree-node="true" class="report-section-budget"></gnc-collapsible>

            <gnc-collapsible tree-node="true" v-for="(section, id) in period.sections" :class="'report-section-' + id" :config="section"></gnc-collapsible>

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
        },
        fetchPeriods() {
            this.$http.get('profit-vs-loss/periods').then((response) => {
                this.periods = response.body.periods
            }, (response) => {
                alert('Error')
            });
        }
    },
    mounted() {
        this.fetchPeriods();
    },
    components: {
        'gnc-collapsible': Collapsible
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

    &.report-section-budget
        background: #d9edf7
        color: #31708f

    &.report-section-income
        background: #dff0d8
        color: #3c763d

    &.report-section-expense
        background: #f2dede
        color: #a94442

    &.report-section-liability
        background: #fcf8e3
        color: #8a6d3b

</style>
