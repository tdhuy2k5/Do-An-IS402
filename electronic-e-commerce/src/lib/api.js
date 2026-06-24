import axios from "axios";

const API_BASE_URL = import.meta.env.VITE_BACKEND_API_URL;


const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    "Content-Type": "application/json",
    "Accept": "application/json",
  },
});

export const tryRefreshToken = async () => {
  const response = await axios.get(`${API_BASE_URL}/refresh`, {
    headers: {
      Authorization: `Bearer ${localStorage.getItem("access_token")}`,
      "refresh-token": localStorage.getItem("refresh_token"),
    }
  });
  if (response.status === 200 && response.data.access_token) {
    localStorage.setItem("access_token", response.data.access_token);
  }
  if (response.status === 200 && response.data.refresh_token) {
    localStorage.setItem("refresh_token", response.data.refresh_token);
  }
  if (response.status === 200 && response.data.user) {
    localStorage.setItem("user", JSON.stringify(response.data.user));
  }
}


api.interceptors.request.use(
  async (config) => {

    const user = JSON.parse(localStorage.getItem("user"));
    var accessToken = localStorage.getItem("access_token");

    if (user && user.exp_unix && Math.floor(Date.now() / 1000) >= user.exp_unix) {
       try {
          await tryRefreshToken();
       } catch (error) {
          if (error.response && error.response.status === 401) {
            console.error("Token refresh failed", error);
            localStorage.removeItem("access_token");
            localStorage.removeItem("refresh_token");
            localStorage.removeItem("user");
          }
          return Promise.reject(error);
       }
    }
    accessToken = localStorage.getItem("access_token");
    if (accessToken) {
      config.headers.Authorization = `Bearer ${accessToken}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);


api.interceptors.response.use(
  (response) => {
    const user = response.data.user;


    if (user) {
      localStorage.setItem("user", JSON.stringify(user));
    }


    if (response.status === 200 && response.data.access_token) {
      localStorage.setItem("access_token", response.data.access_token);
    }
    if (response.status === 200 && response.data.refresh_token) {
      localStorage.setItem("refresh_token", response.data.refresh_token);
    }

    return response;
  },
  (error) => {
    const status = error?.response?.status;
    const authUrls = ["/login", "/signup", "/auth/callback"];
    if (status === 401) {
      localStorage.removeItem("access_token");
      localStorage.removeItem("refresh_token");
      localStorage.removeItem("user");

      if (!authUrls.includes(window.location.pathname)) {
        window.location.replace("/login");
        localStorage.setItem("returnUrl", window.location.pathname);
      }
    }
    else if (status === 409) {
      window.location.replace("/");
    }
    return Promise.reject(error);
  }
);



export const logout = () => {
  localStorage.removeItem("access_token");
  localStorage.removeItem("refresh_token");
  localStorage.removeItem("user");
  window.location.href = "/";
};


export const ProductService = {

  getRecommended: (limit = 10) =>
    api.get(`/products/recommended`, { params: { limit } }),


  getDetails: (id) =>
    api.get(`/product/${id}`),


  searchMobile: (slug, filters = {}) =>
    api.get(`/mobile/${slug}`, { params: filters }),


  searchTV: (slug, filters = {}) =>
    api.get(`/tv-av/${slug}`, { params: filters }),


  searchAll: (params) =>
    api.get(`/products/search`, { params })
};

export default api;
