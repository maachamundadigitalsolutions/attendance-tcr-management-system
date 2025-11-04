<div class="container mt-4">
    <h1>Welcome, <span id="username"></span></h1>
    <button onclick="logout()" class="btn btn-danger mt-3">Logout</button>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const token = localStorage.getItem('api_token');
    console.log('token', token);

    if (!token) {
        window.location.href = "/login";
        return;
    }

    axios.get(window.API_URL + "/v1/user", {
        headers: {
            "Authorization": "Bearer " + token,
            "Accept": "application/json"
        }
    })
    .then(res => {
        console.log('res', res);
        
        document.getElementById('username').innerText = res.data.user.name;
    })
    .catch(err => {
        console.error("User fetch failed:", err.response?.status, err.response?.data);
        if (err.response?.status === 401) {
            localStorage.removeItem('api_token');
            localStorage.removeItem('user');
            window.location.href = "/login";
        }
    });
});

function logout() {
    const token = localStorage.getItem('api_token');
    axios.post(window.API_URL + "/v1/logout", {}, {
        headers: {
            "Authorization": "Bearer " + token,
            "Accept": "application/json"
        }
    }).then(() => {
        localStorage.removeItem('api_token');
        localStorage.removeItem('user');
        window.location.href = "/login";
    }).catch(err => {
        console.error("Logout failed:", err.response?.status, err.response?.data);
    });
}
</script>
