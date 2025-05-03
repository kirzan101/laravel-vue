import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import Layout from "@/App.vue";
import vuetify from "./vuetify";

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue", { eager: true });
        const page = pages[`./Pages/${name}.vue`];

        // only pages that has no layout defined in the component will render the layout
        if (page.default.layout === undefined) {
            page.default.layout = Layout;
        }

        return page;
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(vuetify)
            .mount(el);
    },
});
