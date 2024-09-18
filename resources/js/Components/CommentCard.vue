<template>
    <div class="pt-2 mt-2">
        <div
            class="d-flex justify-content-between align-items-center text-secondary small"
        >
          <div class="d-flex">
            <img src="../download.png"  class="rounded border-1" height="18px" width="18px" />
            <span class="ml-2">{{ comment.user.name }}</span>
          </div>
            <p>{{ comment.created_at }}</p>
        </div>
        <p class="fst-italic">{{ comment.comment }}</p>
        <div class="d-flex justify-content-between mt-1">
            <span class="cursor-pointer text-primary" @click="toggleReplyForm"
                >reply</span
            >
            <AddComment
                v-if="activeCommentId === comment.id"
                @addComment="(val) => {emits('addCommentDetail',val)}"
                :isClearForm="isClearForm"
                :parentId="comment.id"
            />
            <div class="d-flex">
              <p v-if="!comment?.hasLiked" class="mt-1" @click="emitLikeDislike">
                <NotLike />
            </p>
            <p v-else class="mt-1" @click="emitLikeDislike">
                <Like />
            </p>
            <span class="ml-2 mt-1">{{ comment.likeCount }}</span>
            </div>
        </div>
        <div v-if="comment.children.length" class="ml-4 mt-2 hello">
            <CommentCard
                v-for="child in comment.children"
                :key="child.id"
                :comment="child"
                :activeCommentId="activeCommentId"
                @addCommentDetail="(val) => {emits('addCommentDetail',val)}" 
                @setActiveComment="(val) => { emits('setActiveComment',val) }"
                @commentLikeDisLike="handleLikeDislike"
            />
        </div>
    </div>
</template>

<script setup>
import NotLike from "@/Icon/NotLike.vue";
import Like from "@/Icon/Like.vue";
import AddComment from "./AddComment.vue";

const props = defineProps(["comment", "activeCommentId"]);
const emits = defineEmits(["commentLikeDisLike", "setActiveComment","addCommentDetail"]);

const emitLikeDislike = () => {
    emits("commentLikeDisLike", props.comment.id);
};

const handleLikeDislike = (val) => {
    emits("commentLikeDisLike", val);
};

const toggleReplyForm = () => {
 
    if (props.activeCommentId === props.comment.id) {
        emits("setActiveComment", null); // Close the reply form if already open
        console.log("call if");
    } else {
        emits("setActiveComment", props.comment.id); // Open the reply form for this comment
        console.log("call else");
    }
};
</script>

<style scoped>
/* Add styles for nested comments */
</style>
