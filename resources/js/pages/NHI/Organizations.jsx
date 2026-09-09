import { Link, useForm } from '@inertiajs/react';
import OrganizationForm from './OrganizationForm';

export default function Organizations({ orgs }) {
    const { data, setData, post, errors, reset } = useForm({
        name: '',
        portal_url: '',
        is_public: false,
        contact_details: { email: null, phone: null },
    });

    const createNew = () => {
        post("/nhis/organizations", {
            headers: {
                'Accept': 'text/html',
            },
        });
        reset();
    }

    return <div className="grid gap-y-4">
        <div className="card">
            <h2>New HMO</h2>
            <OrganizationForm setData={setData} data={data} onSubmit={(e) => { e.preventDefault(); createNew(); }} />

        </div>
        <div className='card'>
            <table className="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Portal</th>
                    </tr>
                </thead>
                <tbody>
                    {orgs.map((org) => <>
                        <tr key={org.id}>
                            <td><Link className='link' href={`/nhis/organizations/${org.id}`}>{org.name}</Link></td>
                            <td>{org.is_public ? 'Public' : 'Private'}</td>
                            <td>{org.portal_url}</td>
                        </tr>
                    </>)}
                </tbody>
            </table>
        </div>

    </div>;
};
