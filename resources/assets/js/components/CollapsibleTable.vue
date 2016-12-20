<template>
    <pre>COLLAPSIBLE TABLE</pre>
</template>

<script>
    export default {
        mounted() {
            alert(this.after)
        },
        props: ['loader']
    }
</script>
