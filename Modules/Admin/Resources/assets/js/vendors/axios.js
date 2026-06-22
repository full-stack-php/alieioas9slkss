import axios from "axios";

window.axios = axios;

axios.defaults.baseURL = `${Korf.baseUrl}/admin`;
axios.defaults.headers.common["X-CSRF-TOKEN"] = Korf.csrfToken;
axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
