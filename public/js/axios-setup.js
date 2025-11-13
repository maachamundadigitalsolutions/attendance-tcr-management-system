// axios-setup.js

// Base URL for your API
axios.defaults.baseURL = 'http://127.0.0.1:8001/api/v1';
axios.defaults.headers.common['Accept'] = 'application/json';

// Interceptor: attach token automatically
axios.interceptors.request.use(config => {
  const token = localStorage.getItem('api_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
}, error => {
  return Promise.reject(error);
});

// Optional: handle 401 Unauthorized globally
axios.interceptors.response.use(response => response, error => {
  if (error.response && error.response.status === 401) {
    localStorage.clear();
    window.location.href = '/login';
  }
  return Promise.reject(error);
});


