<template>
    <div class="comment-card-container">
        <div class="comment__card">
            <div class="d-flex justify-content-between align-items-center text-secondary small">
                <!-- Left side: Image and Name -->
                <div class="d-flex align-items-center">
                    <img
                        src="../download.png"
                        class="rounded border-1"
                        height="18px"
                        width="18px"
                        alt="User Image"
                    />
                    <span class="ml-2">{{ comment.user.name }}</span>
                </div>

                <!-- Right side: Created Date and Delete Icon -->
                <div class="d-flex align-items-center">
                    <p class="mb-0 mr-2">{{ comment.created_at }}</p>
                    <span v-if="loginUserId == comment.user.id" class="cursor-pointer" @click="deleteComment(comment.id)"><DeleteIcon /></span>
                </div>
            </div>

            <p class="fst-italic">{{ comment.comment }}</p>
            <div class="d-flex justify-content-between mt-1">
                <span
                    class="cursor-pointer text-primary"
                    @click="toggleReplyForm"
                    >reply
                </span>

                <div class="d-flex">
                    <div class="d-flex mr-2" @click="toggleChildComment">
                        <span
                            v-if="comment.childCount > 0"
                            class="cursor-pointer text-primary mt-2"
                            >view {{ comment.childCount }}
                            {{
                                comment.childCount == 1 ? "reply" : "replies"
                            }}</span
                        >
                        <span v-if="comment.childCount > 0" class="mt-2 pt-1">
                            <span v-if="viewChildComment">
                                <DownArrowIcon />
                                <!-- Replace with your down arrow icon -->
                            </span>
                            <span v-else>
                                <UpArrowIcon />
                                <!-- Replace with your up arrow icon -->
                            </span>
                        </span>
                    </div>
                    <p
                        v-if="!comment.hasLiked"
                        class="mt-1"
                        @click="emitLikeDislike"
                    >
                        <NotLike />
                    </p>
                    <p v-else class="mt-1" @click="emitLikeDislike">
                        <Like />
                    </p>
                    <span class="ml-2 mt-1">{{ comment.likeCount }}</span>
                </div>
            </div>
            <AddComment
                v-if="activeCommentId === comment.id"
                @addComment="
                    (val) => {
                        emits('addCommentDetail', val);
                    }
                "
                :isClearForm="isClearForm"
                :parentId="comment.id"
            />
        </div>
    </div>
    <div
        v-if="comment.children.length && viewChildComment"
        class="ml-4 mt-2"
        ref="childCommentsContainer"
    >
        <CommentCard
            v-for="child in comment.children"
            :key="child.id"
            :comment="child"
            :activeCommentId="activeCommentId"
            :loginUserId="props.loginUserId"
            @addCommentDetail="
                (val) => {
                    emits('addCommentDetail', val);
                }
            "
            @setActiveComment="
                (val) => {
                    emits('setActiveComment', val);
                }
            "
            @commentLikeDisLike="handleLikeDislike"
            @deletComment="deleteComment"
        />
    </div>
</template>

<script setup>
import NotLike from "@/Icon/NotLike.vue";
import Like from "@/Icon/Like.vue";
import DownArrowIcon from "@/Icon/DownArrowIcon.vue";
import UpArrowIcon from "@/Icon/UpArrowIcon.vue";
import AddComment from "./AddComment.vue";
import { nextTick, ref } from "vue";
import DeleteIcon from "@/Icon/DeleteIcon.vue";

const props = defineProps(["comment", "activeCommentId","loginUserId"]);
const emits = defineEmits([
    "commentLikeDisLike",
    "setActiveComment",
    "addCommentDetail",
    "deletComment"
]);
const viewChildComment = ref(false);
const childCommentsContainer = ref(null);

const emitLikeDislike = () => {
    emits("commentLikeDisLike", props.comment.id);
};

const handleLikeDislike = (val) => {
    emits("commentLikeDisLike", val);
};

const toggleReplyForm = () => {
    if (props.activeCommentId === props.comment.id) {
        emits("setActiveComment", null);
    } else {
        emits("setActiveComment", props.comment.id);
    }
};

const toggleChildComment = () => {
    viewChildComment.value = !viewChildComment.value;
    nextTick(() => {
        childCommentsContainer.value.scrollIntoView({
            behavior: "smooth",
            block: "start",
        });
    });
};

const deleteComment = (id) => {
    console.log('id: ', id);
    emits("deletComment",id);
}
</script>

<style scoped>
.comment-card-container {
    margin-top: 1rem;
}

.comment__card {
    padding: 1rem;
    border: 1px solid #ddd;
    border-radius: 0.5rem;
    background-color: #f9f9f9;
}

.comment__title {
    font-size: 1.25rem;
    font-weight: bold;
}

.show-replies {
    cursor: pointer;
    color: #007bff;
}

.comment__card-footer {
    margin-top: 0.5rem;
    display: flex;
    justify-content: space-between;
}

.comment__container {
    margin-left: 1rem;
}
</style>
