<div>
    <h1>User List</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>USER ID</th>
            </tr>
        </thead>
        <tbody id="users-table"></tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const token = localStorage.getItem('api_token');
    if (!token) {
        window.location.href = "{{ route('login') }}";
        return;
    }

    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

    axios.get('http://127.0.0.1:8000/api/v1/users')
        .then(res => {
          console.log('RES', res);
          
            const tbody = document.getElementById('users-table');
            tbody.innerHTML = '';
            res.data.forEach(user => {
                tbody.innerHTML += `
                    <tr>
                        <td>${user.name}</td>
                        <td>${user.user_id}</td>
                    </tr>
                `;
            });
        })
        .catch(() => {
            localStorage.clear();
            window.location.href = "{{ route('login') }}";
        });
});
</script>
