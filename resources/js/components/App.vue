<template>
    <div class="container">
        Hello world
        <router-view></router-view>
    </div>
</template>

<script>
export default {
    name: 'App',
    mounted() {
        console.log('App mounted.')
    }
}
</script>
