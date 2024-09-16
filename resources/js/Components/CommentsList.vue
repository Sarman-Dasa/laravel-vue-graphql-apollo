<template>
<div class="container bg-light py-3 mt-2 overflow-y-auto" style="max-height: calc(100vh - 100px);">
  <h6 class="fst-italic">Comments:</h6>
  <CommentCard
    v-for="comment in comments"
    :key="comment.id"
    :comment="comment"
    class="card p-2"
    @commentLikeDisLike="(val) => emits('likeDisLike', val)"
  />
</div>

</template>

<script setup>
import CommentCard from "@/Components/CommentCard.vue"
  const props = defineProps(['comments'])
  const emits = defineEmits(['likeDisLike'])

</script>