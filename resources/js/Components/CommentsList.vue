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
            :editCommentId="editCommentId"
            :loginUserId="userId"
            @setActiveComment="setActiveComment"
            @setEditCommentId="setEditCommentId"
            @addCommentDetail="emits('addComment', $event)"
            @commentLikeDisLike="emits('likeDisLike', $event)"
            @deletComment="emits('deleteComment',$event)"
            @updateComment="updateComment"
            @close="() => editCommentId = null"
        />
    </div>
</template>

<script setup>
import CommentCard from "@/Components/CommentCard.vue";
import { usePage } from "@inertiajs/vue3";
import { ref, watch } from "vue";

const props = defineProps(["comments", "isCommentAdded"]);
const emits = defineEmits(["likeDisLike", "addComment", "deleteComment","updateComment"]);

const activeCommentId = ref(null);
const userId = usePage().props.auth.user.id;
const editCommentId = ref(null);
watch(
    () => props.isCommentAdded,
    () => {
        activeCommentId.value = null;
        editCommentId.value = null;
    }
);

const setActiveComment = (commentId) => {
    activeCommentId.value = commentId;
};

const setEditCommentId = (id) => {
    editCommentId.value = id;
};

const updateComment = (comment) => {
    let obj = {
      comment:comment,
      commentId:editCommentId.value
    }
    emits('updateComment',obj);
};
</script>
