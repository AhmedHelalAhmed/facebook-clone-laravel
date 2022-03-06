<template>
    <div class="flex flex-col items-center">
        <div class="relative mb-8">
            <div class="w-100 h-64 overflow-hidden z-10">
                <img
                    class="object-cover w-full"
                    src="https://images.unsplash.com/photo-1641566956210-3e4070983356?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1470&q=80"
                    alt="user background image">
            </div>
            <div class="absolute flex items-center bottom-1 left-0 -mb-8 ml-12 z-20">
                <div class="w-32">
                    <img src="https://cdn.pixabay.com/photo/2014/07/09/10/04/man-388104_960_720.jpg"
                         alt="user profile image"
                         class="w-32 h-32 object-cover border-4 border-gray-200 rounded-full shadow-lg">
                </div>
                <p v-if="userStatus==='loading'">Loading user...</p>
                <p v-else class="text-2xl  text-gray-100 ml-4">{{ user ? user.data.attributes.name : 'Not Found' }}</p>
            </div>

            <div class="absolute flex items-center bottom-1 right-0 mb-4 mr-12 z-20">
                <button class="py-1 px-3 rounded bg-gray-400"
                        style="background-color: rgb(156 163 175)">Add Friend
                </button>
            </div>
        </div>
        <p v-if="postsLoading">Loading posts...</p>
        <post v-else v-for="post in posts" :post="post" :key="post.data.post_id"></post>
        <p v-if="!postsLoading && (!posts || posts.length <1) ">
            No posts found. Get Started...
        </p>
    </div>
</template>

<script>

import Post from "../../components/Post";
import {mapGetters} from "vuex";

export default {
    name: "Show",
    components: {
        Post,
    },
    data: () => {
        return {
            posts: null,
            postsLoading: true,
        }
    },
    mounted() {

        this.$store.dispatch('fetchUser', this.$route.params.userId);

        axios.get('/api/users/' + this.$route.params.userId + '/posts')
            .then(response => {
                this.posts = response.data.data;
            })
            .catch(error => {
                console.log('Unable to fetch posts');
            })
            .finally(() => {
                this.postsLoading = false;
            });
    },
    computed: {
        ...mapGetters({user: 'user', userStatus: 'userStatus'})
    }
}
</script>

<style scoped>

</style>
