import { Link } from "@inertiajs/react";

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
    return (
        <>
            <div className="card grid grid-cols-4 gap-8">
                <ResourceLink
                    title={"NHIS Portals"}
                    link={"/resources/NHIS-Portals"}
                />
            </div>
        </>
    );
};

export default Resources;
