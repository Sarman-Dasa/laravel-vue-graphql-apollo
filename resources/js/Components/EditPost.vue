<template>
    <form
        @submit.prevent="updatePost"
        class="p-2 border border-light rounded-2 bg-transparent"
    >
        <button type="button" @click="emit('close')" class="float-end cursor-pointer">
            <CloseIcon />
        </button>
        <button type="submit" class="float-end mr-2 cursor-pointer">
            <CheckIcon />
        </button>
        <div class="mb-3 mt-3">
            <label></label>
            <label for="title" class="form-label">Title</label>
            <input
                type="text"
                class="form-control border border-light rounded-2"
                id="title"
                placeholder="Title"
                v-model="formData.title"
                required
            />
        </div>
        <div class="mb-3 mt-3">
            <label for="description">Description</label>
            <textarea
                class="form-control"
                rows="5"
                id="description"
                name="description"
                placeholder="Description"
                v-model="formData.body"
                minlength="5"
                required
            ></textarea>
        </div>
    </form>
</template>

<script setup>
import CheckIcon from "@/Icon/CheckIcon.vue";
import CloseIcon from "@/Icon/CloseIcon.vue";
import { reactive } from "vue";
const emit = defineEmits(["updatePost","close"]);
const props = defineProps(["post"]);

const formData = reactive({
    title: props.post.title,
    body: props.post.body,
});

function updatePost() {
    emit("updatePost", formData);
}
</script>
