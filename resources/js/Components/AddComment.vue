<template>
    <form
        @submit.prevent="addComment"
        class="bg-light p-2 mt-3 container"
    >
        <div>
            <label v-show="props.parentId" for="content" class="form-label text-dark"
                >Add a comment:</label
            >
            <textarea
                type="text"
                class="form-control"
                id="content"
                placeholder="Enter comment"
                v-model="comment"
                name="content"
                rows="5"
                required
            />
        </div>
        <button class="btn btn-primary my-3" type="submit">Send</button>
    </form>
</template>

<script setup>
import { ref, watch } from "vue";
const emit = defineEmits(["addComment"]);
const props = defineProps({
    isClearForm: {
        type: Boolean,
        default: false,
    },
    parentId: {
        type: Number,
        default: null,
    },
});
const comment = ref(null);

// Watch for changes to isClearForm
watch(
    () => props.isClearForm,
    (newValue, oldValue) => {
        console.log("newValue: ", newValue);
        comment.value = null;
    }
);

const addComment = () => {
    let obj = {
      comment: comment.value,
      parentId: props.parentId
    }
    emit("addComment", obj);
};
</script>

<style scoped>
textarea {
    resize: none;
    border: 0px !important;
}

form {
    border-radius: 10px;
}
</style>
