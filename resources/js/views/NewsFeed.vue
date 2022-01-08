<template>
    <div class="flex flex-col items-center py-4">
        <new-post></new-post>
        <p v-if="loading">Loading posts...</p>
        <post v-else v-for="post in posts" :post="post" :key="post.data.post_id"></post>
    </div>
</template>

<script>
import NewPost from "../components/NewPost";
import Post from "../components/Post";

export default {
    name: "NewsFeed",
    components: {
        NewPost,
        Post,
    },
    data: () => {
        return {
            posts: null,
            loading: true,
        }
    },
    mounted() {
        axios.get('/api/posts')
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
