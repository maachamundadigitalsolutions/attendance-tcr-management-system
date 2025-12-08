// axios-setup.js

// Use the injected API_URL from Blade
axios.defaults.baseURL = window.API_URL;
axios.defaults.headers.common['Accept'] = 'application/json';

// Request interceptor: attach token
axios.interceptors.request.use(config => {
  const token = localStorage.getItem('api_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
}, error => Promise.reject(error));

// Response interceptor: handle 401 globally
axios.interceptors.response.use(
  response => response,
  error => {
    if (error.response && error.response.status === 401) {
      localStorage.removeItem('api_token');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

// Export common instance
window.api = axios;



