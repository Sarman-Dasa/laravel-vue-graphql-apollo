import './bootstrap'; // Laravel's bootstrap file for dependencies
import '../css/app.css'; // Your custom styles
import 'bootstrap'; // Optional: If you're using Bootstrap


import { createApp, h } from 'vue';
import { DefaultApolloClient } from '@vue/apollo-composable'; // For Apollo composables
import { createInertiaApp } from '@inertiajs/vue3'; // Inertia.js for Vue 3
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { ApolloClient, createHttpLink, InMemoryCache } from '@apollo/client/core'; // Apollo dependencies

const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
// Apollo Client setup
const httpLink = createHttpLink({
  uri: 'http://localhost:8000/graphql', // Your GraphQL API endpoint
  credentials: 'include',
  headers: {
    'X-CSRF-TOKEN': csrfToken,
  },
});

const cache = new InMemoryCache(); // Apollo in-memory cache

const apolloClient = new ApolloClient({
  link: httpLink,
  cache,
});

// Laravel Mix integration with Vue
createInertiaApp({
  resolve: name => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')), // Page component resolver
  setup({ el, App, props, plugin }) {
    const vueApp = createApp({
      render: () => h(App, props),
    });

    
    // Provide Apollo Client globally so all components can use it
    vueApp.provide(DefaultApolloClient, apolloClient);

    // Use Inertia.js and Ziggy plugins
    vueApp
      .use(plugin)
      .use(ZiggyVue)
      .provide(DefaultApolloClient, apolloClient)
      .mount(el); // Mount the app to the DOM
  },
});
