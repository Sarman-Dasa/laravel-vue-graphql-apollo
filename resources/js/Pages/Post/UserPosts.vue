<template>
    <AuthenticatedLayout>
        <button class="btn btn-primary mt-2" @click="isAddPost = !isAddPost">Add</button>
        <div class="container">
            <AddPost v-if="isAddPost" @save-post="addPost" />
            <PostCard v-for="post in result?.user?.postsList" :key="post.id" :post="post" :is-login-user-post="true" @delete-post="deleteUserPost"/>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import PostCard from '../../Components/PostCard.vue'
import { useMutation, useQuery } from '@vue/apollo-composable'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import gql from 'graphql-tag'
import { ref } from 'vue';
import AddPost from '@/Components/AddPost.vue';


const props = defineProps(['user']);

const post = ref([]);
const id = props.user.id;
const isAddPost = ref(false);

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
            user{
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
        post.value = result?.value?.post
    }, 200); 

const ADD_POST_MUTATION = gql`
    mutation($title: String!, $body: String!, $userId: ID!) {
        createPost(input:{title: $title, body: $body, user_id: $userId}) {
            id
            title
            body
            commentCount
        }
    }
`;


const DELETE_POST = gql`
    mutation($id: ID!) {
        deletePost(id:$id) {
            id
            title
            body
        }
    }
`;

const { mutate: addPostData } = useMutation(ADD_POST_MUTATION, {
    update: (cache, { data: { createPost } }) => {
        const existingData = cache.readQuery({
            query: GET_POST_DETAILS,
            variables: { id: props.user.id }
        });

        const newData = {
            ...existingData,
            user: {
                ...existingData.user,
                postsList: [
                    ...existingData.user.postsList,
                    createPost
                ]
            }
        };

        cache.writeQuery({
            query: GET_POST_DETAILS,
            variables: { id: props.user.id },
            data: newData
        });
    },
});


const { mutate: deletePost} = useMutation(DELETE_POST, {
    update: (cache, { data: { deletePost }}) => {
        cache.evict({id:cache.identify(deletePost)})
    }
});


function addPost(event) {
    addPostData({
        title: event.title,
        body: event.body,
        userId: id // Ensure 'id' is defined properly in your scope
    })
    .then((response) => {
        console.log('response: ', response);
        
    })
    .catch((error) => {
        console.error("Mutation error:", error);
    });
}

function deleteUserPost(id) {
    deletePost({
        id:id
    }).then((response) => {
        console.log('response: ', response);
        
    })
    .catch((error) => {
        console.error("Mutation error:", error);
    });
}
</script>