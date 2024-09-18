<template>
    <div
        class="container bg-light py-3 mt-2 overflow-y-auto"
        style="max-height: calc(100vh - 100px)"
    >
        <h6 class="fst-italic">Comments:</h6>
        <CommentCard
            v-for="comment in comments"
            :key="comment.id"
            :comment="comment"
            class="card p-2"
            :activeCommentId="activeCommentId"
            @setActiveComment="setActiveComment"
            @addCommentDetail="(val) => {emits('addComment',val)}"
            @commentLikeDisLike="(val) => emits('likeDisLike', val)"
        />
    </div>
</template>

<script setup>
import CommentCard from "@/Components/CommentCard.vue";
import { ref, watch } from "vue";
const props = defineProps(["comments","isCommentAdded"]);
const emits = defineEmits(["likeDisLike","addComment"]);
const activeCommentId = ref(null);

watch(() => props.isCommentAdded,() => {
    activeCommentId.value = null;
})

const setActiveComment = (commentId) => {
    activeCommentId.value = commentId;
};


</script>
