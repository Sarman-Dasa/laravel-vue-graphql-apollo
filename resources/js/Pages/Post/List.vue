<template>
    <div class="container">
        <PostCard v-for="post in  posts" :key="post.id" :post="post"/>
    </div>
</template>

<script setup>
import PostCard from '../../Components/PostCard.vue'
import { useQuery } from '@vue/apollo-composable'
import gql from 'graphql-tag'
import { ref } from 'vue';


const props = defineProps(['user']);
console.log('props: ', props.user.id);

const id = props.user.id;
const posts = ref([]);
const { result } = useQuery(gql`
  query {
    posts {
      data {  
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
      paginatorInfo { 
        currentPage
        total
        hasMorePages
      }
    }
  }
`);

setTimeout(() => {
        posts.value = result?.value?.posts.data
    }, 200);  

console.log("result",posts);


</script>