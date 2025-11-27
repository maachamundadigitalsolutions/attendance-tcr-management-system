document.addEventListener('alpine:init', () => {
    Alpine.store('auth', {
        user: null,
        roles: [],
        perms: [],
        set(data) {
            this.user = data.user;
            this.roles = data.roles || [];
            this.perms = data.permissions || [];
        },
        can(permission) {
            return this.perms.includes(permission);
        },
        hasRole(role) {
            return this.roles.includes(role);
        }
    });
});
