<template>

    <div :class="wrapperClass">
        <div class="gnc-cpe-main">
            <div class="gnc-cpe-header gnc-cpe-title" @click="toggle">
                {{ vm.title }}
            </div>
            <div class="gnc-cpe-content" v-show="state.expanded">
                <!-- Render items -->
                <div class="gnc-cpe-content-wrapper" v-if="vm.children.length">
                    <gnc-collapsible v-for="child in vm.children" :config="child" tree-node="true">
                    </gnc-collapsible>
                </div>
                <!-- Render slot -->
                <div class="gnc-cpe-content-wrapper" v-else-if="slotLength()">
                    <slot></slot>
                </div>
            </div>
        </div>
        <div class="gnc-cpe-header gnc-cpe-total" v-if="vm.total" @click="toggle">
            <div class="gnc-cpe-total-wrapper">
                <div class="gnc-cpe-currency">{{ vm.total.sign }}</div>
                <div class="gnc-cpe-amount">{{ vm.total.amount }}</div>
            </div>
        </div>
    </div>
    
</template>

<script>
    export default {
        name: 'gnc-collapsible',
        data() {
            return {
                vm: {
                    title: null,
                    total: null,
                    children: []
                },
                state: {
                    loaded: false,
                    expanded: false                    
                }
            }
        },
        props: {
            'data': { default: null },
            'title': { default: null },
            'total': { default: null },
            'config': { default: () => { return {} } },
            'expanded': { default: false },
            'tree-node': { default: false },
        },
        computed: {
            wrapperClass() {
                return [
                    'gnc-cpe-wrapper',
                    {
                        'gnc-cpe-has-total': this.vm.total,
                        'gnc-cpe-tree-node': this.treeNode
                    }
                ]
            }
        },
        created() {
            this.vm.title = this.title || this.config.title
            this.vm.total = this.total || this.config.total
            this.vm.children = this.config.children || []

            this.state.expanded = this.expanded
        },
        methods: {
            toggle() {
                this.state.expanded = !this.state.expanded

                if ( ! this.state.loaded) {
                    this.state.loaded = true
                    this.$emit('load', this)
                }
            },
            slotLength(slotName) {
                var slot = this.$slots[slotName || 'default']
                return slot && (slot.length > 0)
            }
        },
    }    
</script>

<style lang="stylus">

$default-padding = 10px
$default-border-radius = 4px

has-total = '^[0].gnc-cpe-has-total ^[1..-1]'
is-tree-node = '^[0].gnc-cpe-tree-node ^[1..-1]'
is-tree-node-and-has-total = '^[0].gnc-cpe-tree-node.gnc-cpe-has-total ^[1..-1]'

.gnc-cpe-wrapper
    border-style: solid
    border-width: 1px
    border-radius: $default-border-radius
    display: flex
    justify-content: space-between
    overflow: hidden
    width: 100%

    .gnc-cpe-content
        border-width: 0
        overflow: hidden

    > .gnc-cpe-main
        display: flex
        flex-direction: column
        flex-grow: 1
        z-index: 1

        > .gnc-cpe-title
            cursor: pointer
            padding: $default-padding

        > .gnc-cpe-content .gnc-cpe-content-wrapper
            border-width: 1px 0 0 0
            border-style: solid
            padding: $default-padding

            {has-total}
                border-top-width: 1px
                border-right-width: 1px
                border-top-right-radius: $default-border-radius

            {is-tree-node}
                border-bottom-left-radius: $default-border-radius
                border-bottom-right-radius: $default-border-radius
                padding: 0

            /*{is-tree-node-and-has-total}*/

            > .gnc-cpe-wrapper
                border-width: 0
                border-radius: 0
                &:not(:first-child)
                    border-top-width: 1px
                &:not(:last-child)
                    .gnc-cpe-content
                        border-bottom-left-radius: 0
                        border-bottom-right-radius: 0

    > .gnc-cpe-total
        align-items: center
        cursor: pointer
        display: flex
        font-family: monospace
        font-size: 90%
        padding: $default-padding
        width: 140px
        z-index: 0

        > .gnc-cpe-total-wrapper
            display: flex
            justify-content: space-between
            padding: 0 8px
            width: 100%

</style>
