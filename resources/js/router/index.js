import { createRouter, createWebHistory } from "vue-router";
// import CreatePost from "../views/CreatePost.vue";
// import PostDetails from "../views/PostDetails.vue";
// import PostsList from "../views/PostsList.vue";
// import NotFound from "../views/NotFound.vue";
import CreatePost from "../Components/PostCard.vue"

const routes = [
//   {
//     path: "/",
//     name: "home",
//     component: PostsList,
//   },
  {
    path: "/login",
    name: "CreatePost",
    component: CreatePost,
  },
//   {
//     path: "/post/:id",
//     name: "PostDetails",
//     props: true,
//     component: PostDetails,
//   },
//   {
//     path: "/:catchAll(.*)",
//     name: "NotFound",
//     component: NotFound,
//   },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
