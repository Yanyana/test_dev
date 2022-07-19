import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import "bootstrap";
import "bootstrap/dist/css/bootstrap.min.css";
import { FontAwesomeIcon } from './plugins/font-awesome'
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

import './assets/main.css'


const app = createApp(App)
app.use(VueSweetalert2);
app.use(createPinia());

app
.component("font-awesome-icon", FontAwesomeIcon)
.mount('#app')
