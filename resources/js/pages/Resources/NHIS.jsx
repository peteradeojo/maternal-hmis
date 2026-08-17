import { Form, Link, useForm, router } from "@inertiajs/react";
import { useEffect, useState } from "react";
// import portals from "./nhis-portals.json";

const NHIS = () => {
    const { data, setData, post } = useForm({
        portals: null,
    });

    const [portals, setPortals] = useState([]);

    const onSubmit = (e) => {
        e.preventDefault();
        post("/resources/NHIS-Portals");
    }

    useEffect(() => {
        fetch("/storage/nhis-portals.json").then((data) => {
            data.json().then(portals => setPortals(portals));
        });
    }, []);

    return (
        <div className="card">
            <div>
                <form onSubmit={onSubmit}>
                    <input type="file" onChange={(e) => setData('portals', e.target.files[0])} name="portals" />
                    <button className="btn bg-theme">Submit</button>
                </form>
            </div>

            <p className="text-lg font-semibold">NHIS Portals</p>

            <table className="table">
                <thead>
                    <tr>
                        <th></th>
                        <th>HMO</th>
                        <th>Type</th>
                        <th>Website</th>
                        <th>E-mail Address</th>
                        <th>Tel.</th>
                    </tr>
                </thead>
                <tbody>
                    {portals.map((portal, i) => (
                        <tr key={portal.name}>
                            <td>{i + 1}</td>
                            <td>{portal.name}</td>
                            <td>{portal.class}</td>
                            <td>
                                {portal.website && (
                                    <>
                                        <Link
                                            className="link"
                                            href={portal.website}
                                            target="_blank"
                                        >
                                            {portal.website}
                                            <i className="fa fa-link"></i>
                                        </Link>
                                    </>
                                )}
                            </td>
                            <td>{portal.email}</td>
                            <td>{portal.phone}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
};

export default NHIS;
