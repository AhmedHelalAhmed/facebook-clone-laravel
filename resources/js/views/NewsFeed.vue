<template>
    <div class="flex flex-col items-center py-4">
        <new-post></new-post>
    </div>
</template>

<script>
import NewPost from "../components/NewPost";

export default {
    name: "NewsFeed",
    components: {
        NewPost,
    }
}
</script>

<style scoped>

</style>
