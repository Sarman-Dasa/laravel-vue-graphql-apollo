<template>
  <form
    @submit.prevent="savePost"
    class="p-5 my-5 border bg-light"
  >
    <div class="mb-3 mt-3">
      <label for="title" class="form-label">Title</label>
      <input
        type="text"
        class="form-control"
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
        minlength="50"
        required
      ></textarea>
    </div>
    <div class="container">
      <button type="submit" class="btn btn-success m-2">Create</button>
      <button type="button" class="btn btn-danger m-2" @click="cancel">
        Cancel
      </button>
    </div>
  </form>
</template>

<script setup>
import { reactive } from "vue";
const emit = defineEmits(['savePost'])

const formData = reactive({
  title:null,
  body:null
})

function savePost() {
    emit('savePost',formData);
}
</script>