import { Link, useForm } from '@inertiajs/react';

export default function Organizations({ orgs }) {
    const { data, setData, post, errors, reset } = useForm({
        name: '',
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

            <form onSubmit={(e) => { e.preventDefault(); createNew(); }}>
                <div className="form-group">
                    <label>Name</label>
                    <input type="text" value={data.name} onChange={(e) => setData('name', e.target.value)} className="form-control" required />
                </div>
                <div className="form-group">
                    <label>Is Public? <input type="checkbox" checked={data.is_public} onChange={(e) => setData('is_public', e.target.checked)} /></label>
                </div>
                <fieldset><legend>Contact details (<small>Fill at least one)</small></legend>
                    <div className="form-group">
                        <label>E-Mail</label>
                        <input type="email" value={data.contact_details.email} onChange={(e) => setData('contact_details.email', e.target.value.length > 0 ? e.target.value : null)} className="form-control" />
                    </div>
                    <div className="form-group">
                        <label>Phone Number</label>
                        <input type="tel" value={data.contact_details.phone} onChange={(e) => setData('contact_details.phone', e.target.value.length > 0 ? e.target.value : null)} className="form-control" />
                    </div>
                </fieldset>
                <div className="form-group">
                    <button className="btn bg-primary text-white">Submit</button>
                </div>
            </form>
        </div>
        <div className='card'>
            <table className="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Portal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    {orgs.map((org) => <>
                        <tr key={org.id}>
                            <td>{org.name}</td>
                            <td>{org.is_public ? 'Public' : 'Private'}</td>
                            <td>{org.portal_url}</td>
                            <td><Link href={`/nhis/organizations/${org.id}`}>View</Link></td>
                        </tr>
                    </>)}
                </tbody>
            </table>
        </div>

    </div>;
};
