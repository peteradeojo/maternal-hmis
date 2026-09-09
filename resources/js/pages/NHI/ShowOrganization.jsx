import { Link, useForm } from "@inertiajs/react";
import OrganizationForm from "./OrganizationForm";

export default function ShowOrganization({ org }) {
	const form = useForm({ ...org });

	const update = (e) => {
		e.preventDefault();
		form.patch(`/nhis/organizations/${org.id}`);
	}

	return <>
		<div className="card">
			<Link href="/nhis/organizations">Back</Link>
			<h2>{org.name}</h2>

			<OrganizationForm onSubmit={update} data={form.data} setData={form.setData} />
		</div>
	</>;
}
