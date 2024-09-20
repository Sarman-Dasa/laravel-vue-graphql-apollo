<template>
     <Head title="My post" />
    <AuthenticatedLayout>
        <template #header>
            <div class="justify-between d-flex">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                My post
            </h2>
            <button class="btn btn-primary ml-5" @click="toggleAddPostModal">
            Add
        </button>
            </div>
        </template>
        <div class="container">
            <AddPost v-if="isAddPost" @save-post="addPost" />
            <PostCard
                v-for="post in result?.user?.postsList"
                :key="post.id"
                :post="post"
                :is-login-user-post="true"
                :editPostId="editPostId"
                @deletePost="deleteUserPost"
                @setEditPostId="setEditPostId"
                @updatePost="updatePostData"
                @close="() => { editPostId = null}"
            />
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import PostCard from "../../Components/PostCard.vue";
import { useMutation, useQuery } from "@vue/apollo-composable";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import gql from "graphql-tag";
import { nextTick, ref } from "vue";
import AddPost from "@/Components/AddPost.vue";

const props = defineProps(["user"]);

const post = ref([]);
const id = props.user.id;
const isAddPost = ref(false);
const editPostId = ref(null);

const GET_POST_DETAILS = gql`
    query user($id: ID!) {
        user(id: $id) {
            id
            name
            email
            postsList {
                id
                title
                body
                created_at
                updated_at
                commentCount
                user {
                    id
                    name
                }
            }
        }
    }
`;

const { result, loading, error } = useQuery(GET_POST_DETAILS, {
    id: props.user.id,
});

setTimeout(() => {
    post.value = result?.value?.post;
}, 200);

const ADD_POST_MUTATION = gql`
    mutation ($title: String!, $body: String!, $userId: ID!) {
        createPost(input: { title: $title, body: $body, user_id: $userId }) {
            id
            title
            body
            commentCount
        }
    }
`;

const DELETE_POST = gql`
    mutation ($id: ID!) {
        deletePost(id: $id) {
            id
            title
            body
        }
    }
`;


const UPDATE_POST = gql`
    mutation ($postId:ID!, $title:String!, $body:String!) {
        updatePost(id:$postId,input:{title: $title, body: $body}) {
            id
            title
            body
            commentCount
        }
    }
`

const { mutate: addPostData } = useMutation(ADD_POST_MUTATION, {
    update: (cache, { data: { createPost } }) => {
        const existingData = cache.readQuery({
            query: GET_POST_DETAILS,
            variables: { id: props.user.id },
        });

        const newData = {
            ...existingData,
            user: {
                ...existingData.user,
                postsList: [...existingData.user.postsList, createPost],
            },
        };

        cache.writeQuery({
            query: GET_POST_DETAILS,
            variables: { id: props.user.id },
            data: newData,
        });
    },
});

const { mutate:updatePost } = useMutation(UPDATE_POST,{
    // refetchQueries: [{ query: GET_POST_DETAILS,variables: { id: props.user.id } }],
    // awaitRefetchQueries: true

    update: (cache, { data: { updatePost } }) => {
        // Update the cache directly with the new post data
        const data = cache.readQuery({
            query: GET_POST_DETAILS,
            variables: { id: props.user.id }
         }); // Replace with your query

         console.log("data",data);
        // Find the updated post and modify it in the cache
        const updatedPosts = data.user.postsList.map((post) => 
            post.id === updatePost.id ? { ...post, ...updatePost } : post
        );

        // Write back the updated list to the cache
        cache.writeQuery({
            query: GET_POST_DETAILS,
            variables: { id: props.user.id },
            data: { postsList: updatedPosts },
        });
    }
});

const { mutate: deletePost } = useMutation(DELETE_POST, {
    update: (cache, { data: { deletePost } }) => {
        cache.evict({ id: cache.identify(deletePost) });
    },
});

function addPost(event) {
    addPostData({
        title: event.title,
        body: event.body,
        userId: id, // Ensure 'id' is defined properly in your scope
    })
        .then((response) => {
            console.log("response: ", response);
            isAddPost.value = false;
        })
        .catch((error) => {
            console.error("Mutation error:", error);
        });
}

function deleteUserPost(id) {
    deletePost({
        id: id,
    })
        .then((response) => {
            console.log("response: ", response);
        })
        .catch((error) => {
            console.error("Mutation error:", error);
        });
}

function updatePostData(data) {
    updatePost({
        title: data.title,
        body: data.body,
        postId: editPostId.value
    })
        .then((response) => {
            console.log("response: ", response);
            editPostId.value = null;
        })
        .catch((error) => {
            console.error("Mutation error:", error);
        });

}

async function toggleAddPostModal() {
    editPostId.value = null
    isAddPost.value = !isAddPost.value

}

function setEditPostId(id) {
    console.log('id: ', id);
    isAddPost.value = false;
    editPostId.value = id;

}
</script>
