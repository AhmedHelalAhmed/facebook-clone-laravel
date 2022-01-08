<template>
    <div>
        <div class="w-100 h-64 overflow-hidden">
            <img
                class="object-cover w-full"
                src="https://images.unsplash.com/photo-1641566956210-3e4070983356?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1470&q=80"
                alt="user background image">
        </div>
    </div>
</template>

<script>
export default {
    name: "Show",
    data: () => {
        return {
            user: null,
            loading: true,
        }
    },
    mounted() {

        axios.get('/api/users/' + this.$route.params.userId)
            .then(response => {
                this.user = response.data;
            })
            .catch(error => {
                console.log('Unable to fetch the user from the server.');
            })
            .finally(() => {
                this.loading = false;

            });
        axios.get('/api/posts/' + this.$route.params.userId)
            .then(response => {
                this.posts = response.data.data;
                this.loading = false;
            }).catch(error => {
            console.log('Unable to fetch posts');
            this.loading = false;
        });
    }
}
</script>

<style scoped>

</style>
