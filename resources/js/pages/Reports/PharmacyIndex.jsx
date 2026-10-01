import StatCard from "../../components/StatCard";

const PharmacyIndex = ({ data }) => {
    return (<>
        <div className="card flex flex-col gap-y-8">
            <p className="card-title text-lg font-semibold">Pharmacy Reports</p>
            <table className="table">
                <thead>
                    <tr>
                        <td colSpan={2}>Dispensed Drugs by Frequency</td>
                    </tr>
                </thead>
                <tbody>
                    {data.drugsDispensedToday.map((r) => <tr key={r.item_id}><td>{r.item.name}</td><td>{r.count}</td></tr>)}
                </tbody>
            </table>

            <table className="table">
                <thead>
                    <tr><td colSpan={2}>Dispensed Drugs by Quantity</td></tr>
                </thead>
                <tbody>
                    {data.topDrugsDispensed.map((drug) => <tr key={drug.item.id}>
                        <td>{drug.item.name}</td>
                        <td>{drug.quantity}</td>
                    </tr>)}
                </tbody>
            </table>
        </div>


        <div className="card grid gap-x-8">
            <StatCard title={"Dispensed by Frequency"}>
                0
            </StatCard>
            <StatCard title={"Dispensed by Frequency"}>
                0
            </StatCard>
            <StatCard title={"Dispensed by Frequency"}>
                0
            </StatCard>
        </div>
    </>);
};

export default PharmacyIndex;
