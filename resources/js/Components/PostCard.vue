<template>
    <div class="card my-3">
        <div class="justify-between card-header text-primary d-flex">
            <h5 class="">{{ post.title }}</h5>
            <span v-if="props.isLoginUserPost" class="cursor-pointer" @click="deletePost(post.id)"><DeleteIcon /></span>
            <span v-else>{{ post.user.name }}</span>
            
        </div>
        <p class="card-body text-start">
            {{ preview }} <span class="text-end text-secondary"></span>
        </p>
        <div class="card-footer d-flex justify-content-between">
            <div>{{ formatedDate }}</div>
            <div>Comments {{ post.commentCount }}</div>
            
            <a :href="route('post.view', post.id)">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="24"
                    height="24"
                    viewBox="0 0 20 24"
                    fill="#7293e0"
                    stroke="#7293e0"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="feather feather-arrow-up-right"
                >
                    <line x1="7" y1="17" x2="17" y2="7"></line>
                    <polyline points="7 7 17 7 17 17"></polyline>
                </svg>
            </a>
        </div>
    </div>

    <div>
</div>
</template>

<script setup>
import DeleteIcon from "@/Icon/DeleteIcon.vue";
import { computed } from "vue";
import Swal from "sweetalert2"

// Define props using defineProps
const props = defineProps({
    post: {
        type: Object,
        required: true
    },
    isLoginUserPost: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits("deletePost");

// Computed properties
const preview = computed(() => {
    let text = props.post.body;
    return text.length > 100 ? `${text.slice(0, 100)}...` : text;
});

const formatedDate = computed(() => {
    return new Date(props.post.created_at).toLocaleDateString({
        day: "numeric",
        month: "short",
        year: "numeric",
        hour: "numeric",
        minute: "2-digit",
    });
});

async function deletePost(id) {
  Swal.fire({
    title: "Are You sure To Delete Post.",
    text: "Record can't retrive !",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Yes",
    confirmButtonColor: "primary",
  }).then(response => {
    if (response.isConfirmed) {
        emit('deletePost',id)
    }
  })
}
</script>

<style scoped>
.no-text-decor {
    text-decoration: none;
    color: initial;
}

.card:hover {
    background-color: #ffffff;
    border: 2px solid #0d6efd;
    cursor: default;
}
</style>
