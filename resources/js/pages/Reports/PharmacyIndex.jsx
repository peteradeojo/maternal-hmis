import StatCard from "../../components/StatCard";

const PharmacyIndex = ({ data }) => {
    return (<>
        <div className="card grid grid-cols-3 gap-y-4 gap-x-8">
            <StatCard title={<p className='font-semibold text-4xl'>{data.drugsDispensedTodayCount}</p>} footer={"Dispensed Today"} />
            <StatCard footer={"Prescriptions Today"} title={<p className="text-4xl font-semibold">{data.totalPrescriptionsToday}</p>} />
            <StatCard footer={"Presciptions Closed Today"} title={<p className="text-4xl font-semibold">{data.totalClosedPrescriptionsToday}</p>}>
                <>{data.totalPrescriptionsToday > 0 ? (data.totalClosedPrescriptionsToday / data.totalPrescriptionsToday) * 100 : 0}%</>
            </StatCard>
        </div >

        <div className="card flex flex-col gap-y-8">
            <p className="card-title text-lg font-semibold">Pharmacy Reports</p>
            <table className="table">
                <thead>
                    <tr>
                        <td colSpan={2}>Dispensed Drugs by Frequency</td>
                    </tr>
                </thead>
                <tbody>
                    {data.drugsDispensedToday.length > 0 ? data.drugsDispensedToday.map((r) => <tr key={r.item_id}>
                        <td>{r.item.name}</td><td>{r.count}</td>
                    </tr>) : <tr>
                        <td colSpan={2}>No drugs dispensed</td>
                    </tr>}
                </tbody>
            </table>

            <table className="table">
                <thead>
                    <tr><td colSpan={2}>Dispensed Drugs by Quantity</td></tr>
                </thead>
                <tbody>
                    {data.topDrugsDispensed.length > 0 ? data.topDrugsDispensed.map((drug) => <tr key={drug.item.id}>
                        <td>{drug.item?.name}</td>
                        <td>{drug.quantity}</td>
                    </tr>) : <tr><td colSpan={2}>No drugs dispensed</td></tr>}
                </tbody>
            </table>
        </div>
    </>);
};

export default PharmacyIndex;
