import { usePage } from "@inertiajs/react"

export const useRoles = () => {
    const { props } = usePage();

    return {
        ...props.auth,
        can(action) {
            return this.permissions.some((p) => p == action);
        },
        hasRole(role) {
            return this.roles.some((p) => p == role);
        }
    };
}
