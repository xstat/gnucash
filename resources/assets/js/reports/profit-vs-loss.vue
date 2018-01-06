<template>

    <div>

    <gnc-collapsible title="Periods" class="report-wrapper" tree-node="true" expanded="true">

        <gnc-collapsible v-for="(period, index) in periods" :config="period" class="report-period" tree-node="true" @load="load(period)">

            <!--
            <gnc-collapsible title="Budget" :data="index" tree-node="true" class="report-section-budget"></gnc-collapsible>
            -->

            <gnc-collapsible v-for="(txs, curr) in period.transactions" :title="curr" class="report-commodity" expanded="true">

                <table border="1" width="100%" frame="void" >
                    <tr>
                        <td width="160" align="center">Date</td>
                        <td>Description</td>
                        <td width="120" align="right">Debit</td>
                        <td width="120" align="right">Credit</td>
                        <td width="120" align="right">Balance</td>
                    </tr>
                    <tr v-for="tx in txs">
                        <td align="center">{{ tx.date }}</td>
                        <td class="gnc-report-description">{{ tx.description }}</td>
                        <td class="gnc-report-amount" align="right">
                            {{ tx.debit ? tx.debit.formatted : '-' }}
                        </td>
                        <td class="gnc-report-amount" align="right">
                            {{ tx.credit ? tx.credit.formatted : '-' }}
                        </td>
                        <td class="gnc-report-amount" align="right">
                            {{ tx.balance.formatted }}
                        </td>
                    </tr>
                </table>

            </gnc-collapsible>

        </gnc-collapsible>

        <gnc-collapsible title="TOTAL" :config="{ total }"></gnc-collapsible>

    </gnc-collapsible>

    </div>

</template>

<script>
import Collapsible from '../components/gnc/collapsible.vue'

export default {
    data() {
        return {
            periods: [],
            total: []
        }
    },
    methods: {
        load(period) {
            this.$http.get('profit-vs-loss/detail', {
                params: { code: period.code }
            }).then((response) => {
                Vue.set(period, 'transactions', response.body.transactions);
            }, (response) => {
            });
        },
        fetchPeriods() {
            this.$http.get('profit-vs-loss/periods').then((response) => {
                this.periods = response.body.periods
                this.total = response.body.total
                console.log(response.body);
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

    &.report-commodity
        background: black
        color: white
        .gnc-cpe-content
            background: white;
            color: black;

.gnc-report-amount
    font-family: "Ubuntu Mono"
    font-size: 110%;

.gnc-cpe-content
    table
        th
            background: black;
            color: white;
            font-size: 110%;
            text-align: center;
            padding: 10px;
        td
            padding: 5px 10px

</style>
