<template>
    <div class="flex flex-col items-center py-4">
        <new-post></new-post>

        <post v-for="post in posts" :post="post" :key="post.data.post_id"></post>
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
        }
    },
    mounted() {
        axios.get('/api/posts')
            .then(response => {

                this.posts = response.data.data;

            }).catch(error => {
            console.log('Unable to fetch posts')
        });
    }
}
</script>

<style scoped>

</style>
