<template>
    <Head title="Posts" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Posts
            </h2>
        </template>
        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <PostCard
                        v-for="post in posts"
                        :key="post.id"
                        :post="post"
                    />
                </div>
            </div>
        </div>
        <!-- </div> -->
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PostCard from "../../Components/PostCard.vue";
import { useQuery } from "@vue/apollo-composable";
import gql from "graphql-tag";
import { ref } from "vue";
import { Head } from "@inertiajs/vue3";

const props = defineProps(["user"]);
console.log("props: ", props.user.id);

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
    posts.value = result?.value?.posts.data;
}, 200);

console.log("result", posts);
</script>
