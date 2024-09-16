<template>
    <div class="p-2 mt-2">
        <div
            class="d-flex justify-content-between align-content-center text-secondary small"
        >
            <p>{{ comment.user.name }}</p>
            <p>{{ comment.created_at }}</p>
        </div>
        <p class="fst-italic">{{ comment.comment }}</p>
        <div class="d-flex justify-content-end">
          <p
            v-if="!comment?.hasLiked"
            class="mt-1"
            @click="emits('commentLikeDisLike', comment.id)"
        >
            <NotLike />
        </p>
        <p v-else class="mt-1" @click="emits('commentLikeDisLike', comment.id)">
            <Like />
        </p>
        <span class="ml-2 mt-1"> {{ comment.likeCount }}</span>
        </div>
    </div>
</template>

<script setup>
import NotLike from "@/Icon/NotLike.vue";
import Like from "@/Icon/Like.vue";
const props = defineProps(["comment"]);
const emits = defineEmits(["commentLikeDisLike"]);
</script>

<style scoped>
ul {
    list-style-type: none;
    padding: 0;
}
li {
    display: inline-block;
    margin: 0 10px;
}
a {
    color: #42b983;
}
</style>
