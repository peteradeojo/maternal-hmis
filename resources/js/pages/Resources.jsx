import { Link } from "@inertiajs/react";
import { useRoles } from "../hooks/useRoles";

const ResourceLink = ({ title, link }) => {
    return (
        <Link
            href={link}
            className="border-2 border-black p-4 flex flex-col gap-y-4 hover:border-blue-400"
        >
            <span className="font-bold">{title}</span>
        </Link>
    );
};

const Resources = () => {
    const auth = useRoles();
    return (
        <>
            <div className="card grid grid-cols-4 gap-8">
                {auth.hasRole('insurance') && <ResourceLink
                    title={"NHIS Portals"}
                    link={"/resources/NHIS-Portals"}
                />}
            </div>
        </>
    );
};

export default Resources;
