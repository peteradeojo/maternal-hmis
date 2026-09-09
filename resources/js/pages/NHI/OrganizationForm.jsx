const OrganizationForm = ({ formData, onSubmit, data, setData }) => {
	// const { data, setData, post, errors, reset } = useForm({
	// 	name: '',
	// 	portal_url: '',
	// 	is_public: false,
	// 	contact_details: { email: '', phone: '' },
	// 	...formData,
	// });

	return <>
		<form onSubmit={onSubmit}>
			<div className="form-group">
				<label>Name</label>
				<input type="text" value={data.name} onChange={(e) => setData('name', e.target.value)} className="form-control" required />
			</div>
			<div className="form-group">
				<label>Portal URL</label>
				<input type="text" value={data.portal_url || ''} onChange={(e) => setData('portal_url', e.target.value)} className="form-control" />
			</div>
			<div className="form-group">
				<label>Is Public? <input type="checkbox" checked={data.is_public} onChange={(e) => setData('is_public', e.target.checked)} /></label>
			</div>
			<fieldset><legend>Contact details (<small>Fill at least one)</small></legend>
				<div className="form-group">
					<label>E-Mail</label>
					<input type="email" value={data.contact_details.email || ''} onChange={(e) => setData('contact_details.email', e.target.value)} className="form-control" />
				</div>
				<div className="form-group">
					<label>Phone Number</label>
					<input type="tel" value={data.contact_details.phone || ''} onChange={(e) => setData('contact_details.phone', e.target.value)} className="form-control" />
				</div>
			</fieldset>
			<div className="form-group">
				<button className="btn bg-primary text-white">Submit</button>
			</div>
		</form>
	</>;
};

export default OrganizationForm;
