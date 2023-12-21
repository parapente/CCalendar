import axios from "axios";
import get from "lodash/get";

axios.interceptors.response.use(
    (response) => response,
    async (err) => {
        const status = get(err, "response.status");

        if (status === 419) {
            // Refresh our session token
            await axios.get("/sanctum/csrf-cookie");

            // Return a new request using the original request's configuration
            return axios(err.response.config);
        } else if (status === 302 && err.response.headers.location) {
            console.log(err.response.headers.location);
            return window.location.replace(err.response.headers.location);
        }

        return Promise.reject(err);
    }
);
