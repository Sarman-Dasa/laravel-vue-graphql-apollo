<template>
    <AuthenticatedLayout>
        <div class="home p-2">
            <NotFound v-if="result == null" />
            <PostLayout v-if="result?.post" :post="result.post" />
            <CommentsList
                v-if="parentChildCommentData?.length"
                :comments="parentChildCommentData"
                :isCommentAdded="isClearForm"
                @likeDisLike="handleCommentLikeDisLike"
                @addComment="addNewPostComment"
                @deleteComment="commentDelete"
                @updateComment="updateCommentData"
            />

            <AddComment
                @add-comment="addNewPostComment"
                :isClearForm="isClearForm"
            />
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed, onMounted, ref } from "vue";
import PostLayout from "@/Components/PostLayout.vue";
import CommentsList from "@/Components/CommentsList.vue";
import { useMutation, useQuery } from "@vue/apollo-composable";
import gql from "graphql-tag";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { usePage } from "@inertiajs/vue3";
import AddComment from "@/Components/AddComment.vue";

const props = defineProps(["id"]);
const post = ref([]);
const commentsList = ref([]);
const userId = usePage().props.auth.user.id;
const isClearForm = ref(false);

const parentChildCommentData = computed(() => {
    const list = result.value?.post?.commentsList;
    if (!list) {
        return;
    }
    const parentChildPair = [];
    const idToObjectMap = {};

    // Create a shallow copy of the list to avoid immutability issues
    const mutableList = list.map((item) => ({ ...item, children: [],childCount: 0 }));

    mutableList.forEach((item) => {
        idToObjectMap[item.id] = item;
    });

    mutableList.forEach((item) => {
        if (item.parent_id !== null) {
            const parent = idToObjectMap[item.parent_id];
            if (parent) {
                parent.children.push(item);
                parent.childCount += 1; 
            }
        } else {
            parentChildPair.push(item);
        }
    });

    return parentChildPair;
});

const GET_POST_DETAILS = gql`
    query post($id: ID!) {
        post(id: $id) {
            id
            title
            body
            created_at
            commentCount
            commentsList {
                ...CommentFields
            }
        }
    }
    fragment CommentFields on Comment {
        id
        comment
        likeCount
        created_at
        hasLiked
        parent_id
        user {
            id
            name
        }
    }
`;

const { result, loading, error } = useQuery(GET_POST_DETAILS, {
    id: props.id,
});
setTimeout(() => {
    post.value = result?.value?.post;
    commentsList.value = result?.value?.post?.commentsList;
}, 200);

// Define your mutation
const LIKE_COMMENT_MUTATION = gql`
    mutation ($commentId: ID!) {
        likeComment(comment_id: $commentId) {
            id
            comment
            likeCount
            hasLiked
        }
    }
`;

const ADD_POST_COMMENT = gql`
    mutation ($userId: ID!, $postId: ID!, $comment: String!, $parentId: ID) {
        createPostComment(
            input: {
                user_id: $userId
                post_id: $postId
                comment: $comment
                parent_id: $parentId
            }
        ) {
            id
            comment
            likeCount
            hasLiked
            user {
                id
            }
        }
    }
`;


const DELETE_POST_COMMENT = gql`
    mutation($id:ID!) {
        deleteComment(id:$id) {
            id
            comment
        }
    }       
`

const UPDATE_POST_COMMENT = gql`
    mutation($id:ID!,$comment:String!) {
        updateComment(id:$id comment:$comment) {
            id
            comment
            likeCount
            hasLiked
            user {
                id
            }
        }
    }
`
// const { mutate: likeComment } = useMutation(LIKE_COMMENT_MUTATION);

const { mutate: likeComment } = useMutation(LIKE_COMMENT_MUTATION, {
    update: (cache, { data: { likeComment } }) => {
        // Read the existing data from the cache
        const existingData = cache.readQuery({
            query: GET_POST_DETAILS,
            variables: { id: props.id },
        });

        if (!existingData || !existingData.post) return;

        // Update the commentsList with the new likeComment data
        const updatedCommentsList = existingData.post.commentsList.map(
            (comment) => {
                if (comment.id === likeComment.id) {
                    // Update the specific comment with new like data
                    return {
                        ...comment,
                        likeCount: likeComment.likeCount,
                        hasLiked: likeComment.hasLiked,
                    };
                }
                return comment;
            }
        );

        console.log("updatedCommentsList", updatedCommentsList);
        commentsList.value = updatedCommentsList;
        // Write the updated data back to the cache
        cache.writeQuery({
            query: GET_POST_DETAILS,
            variables: { id: props.id },
            data: {
                post: {
                    ...existingData.post,
                    commentsList: updatedCommentsList,
                },
            },
        });
    },
});

const { mutate: addComment } = useMutation(ADD_POST_COMMENT, {
    update: (cache, { data: { createPostComment } }) => {
        const existingData = cache.readQuery({
            query: GET_POST_DETAILS,
            variables: { id: props.id },
        });

        const newData = {
            ...existingData,
            post: {
                ...existingData.post,
                commentsList: [
                    ...existingData.post.commentsList,
                    createPostComment,
                ],
            },
        };

        cache.writeQuery({
            query: GET_POST_DETAILS,
            variables: { id: props.id, userId },
            data: newData,
        });
    },
});

const { mutate :updateComment} = useMutation(UPDATE_POST_COMMENT,{
    // update: (cache, { data: {  updateComment }}) => {
    //     const existingData = cache.readQuery({
    //         query:GET_POST_DETAILS,
    //         variables: { id:props.id }
    //     });
    // }
    refetchQueries: [{ query: GET_POST_DETAILS,variables: { id: props.id  } }],
    awaitRefetchQueries: true
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

const addNewPostComment = (event) => {
    console.log("event: ", event);
    addComment({
        userId: userId,
        postId: props.id,
        comment: event.comment,
        parentId: event.parentId,
    })
        .then((response) => {
            console.log("response: ", response);
            isClearForm.value = !isClearForm.value;
        })
        .catch((error) => {
            console.error("Mutation error:", error);
        });
};

const {mutate: deleteComment } = useMutation(DELETE_POST_COMMENT, {
    update: (cache, { data: { deleteComment }}) => {
        cache.evict({id:cache.identify(deleteComment)})
    }
});

const commentDelete = (id) => {
    deleteComment({
        id:id
    }).then((response) => {
        console.log('response: ', response);
        
    })
    .catch((error) => {
        console.error("Mutation error:", error);
    });

}

const updateCommentData = (data) => {
    updateComment({
        id:data.commentId,
        comment:data.comment
    })
    .then((response) => {
        console.log("response: ", response);
        isClearForm.value = !isClearForm.value;
    })
    .catch((error) => {
        console.error("Mutation error:", error);
    });

}
</script>
