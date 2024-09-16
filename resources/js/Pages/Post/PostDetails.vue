<template>
    <AuthenticatedLayout>
        <div class="home p-2">
            <NotFound v-if="result == null" />
            <PostLayout v-if="result?.post" :post="result.post" />
            <CommentsList
                v-if="result?.post?.commentCount"
                :comments="result?.post?.commentsList"
                @likeDisLike="handleCommentLikeDisLike"
            />

            <AddComment @add-comment="AddNewPostComment" :isClearForm="isClearForm"/>
        </div>
    </AuthenticatedLayout>


</template>

<script setup>
import { onMounted, ref } from "vue";
import PostLayout from "@/Components/PostLayout.vue";
import CommentsList from "@/Components/CommentsList.vue";
import { useMutation, useQuery } from "@vue/apollo-composable";
import gql from "graphql-tag";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { usePage } from "@inertiajs/vue3";
import AddComment from "@/Components/AddComment.vue";
import { comment } from "postcss";

const props = defineProps(["id"]);
const post = ref([]);
const userId = usePage().props.auth.user.id;
const isClearForm = ref(false);

const GET_POST_DETAILS = gql`
  query post($id: ID!, $userId: ID!) {
    post(id: $id) {
      id
      title
      body
      created_at
      commentCount
      commentsList {
        id
        comment
        likeCount
        created_at
        hasLiked(user_id: $userId)
        user {
          id
          name
        }
      }
    }
  }
`;

const { result, loading, error } = useQuery(GET_POST_DETAILS, {
    id: props.id,
    userId
});
    setTimeout(() => {
        post.value = result?.value?.post
    }, 200);    

// Define your mutation
const LIKE_COMMENT_MUTATION = gql`
    mutation ($commentId: ID!, $userId: ID!) {
        likeComment(comment_id: $commentId, user_id: $userId) {
            id
            comment
            likeCount
            hasLiked(user_id:$userId)
        }
    }
`;


const ADD_POST_COMMENT = gql`
    mutation($userId:ID!, $postId:ID!, $comment:String!) {
        createPostComment(input:{user_id:$userId post_id:$postId comment:$comment}) {
            id
            comment
            likeCount
            hasLiked(user_id: $userId)
            user {
                id
            }
        }
    }
`

const { mutate: likeComment } = useMutation(LIKE_COMMENT_MUTATION);

const { mutate: addComment } = useMutation(ADD_POST_COMMENT,{
    update: (cache, { data: { createPostComment } }) => {
        const existingData = cache.readQuery({
            query:GET_POST_DETAILS,
            variables: { id: props.id, userId}
        });

        const newData = {
            ...existingData,
            post: {
                ...existingData.post,
                commentsList: [
                    ...existingData.post.commentsList,
                    createPostComment
                ]
            }
        };
        cache.writeQuery({
            query: GET_POST_DETAILS,
            variables: { id: props.id, userId},
            data: newData
        });
    },
});
// Function to handle Like/Dislike
const handleCommentLikeDisLike = (id) => {
    likeComment({ commentId: id, userId: userId })
        .then((response) => {
            console.log("Mutation result:", response);
        })
        .catch((error) => {
            console.error("Mutation error:", error);
        });
};


const AddNewPostComment = (event) => {
    addComment({
        userId:userId,
        postId:props.id,
        comment:event
    }).then((response) => {
        console.log('response: ', response);
        isClearForm.value = !isClearForm.value;
    })
    .catch((error) => {
        console.error("Mutation error:", error);
    });
}
</script>

