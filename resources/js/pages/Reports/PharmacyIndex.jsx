import { Chart as ChartJS, ArcElement, Tooltip, Legend, Colors } from "chart.js";
import { Line, Bar } from "react-chartjs-2";
import { getRandomColor } from "../utils";
import { useState } from "react";

ChartJS.register(ArcElement, Tooltip, Legend, Colors);

const PharmacyIndex = ({ data }) => {
    const [topDrugs, setTopDrugs] = useState(data.topDrugs);
    const [dispenseTrend, setDTrend] = useState(data.closedPrescriptions);
    const [openPrescriptions, setOpTrend] = useState(data.openPrescriptions);

    return (<div className='grid gap-y-4'>
        <div className="card grid grid-cols-2 gap-y-4 gap-x-8">
            <div>
                <p className="text-xl">Top Drugs</p>
                <Bar
                    datasetIdKey="item_id"
                    data={{
                        labels: topDrugs.map(d => d.name),
                        datasets: [{
                            label: 'Top Drugs',
                            data: topDrugs.map(d => d.qty),
                            backgroundColor: topDrugs.map(d => getRandomColor(1)),
                        }],
                    }}
                    options={{
                        indexAxis: 'y',
                    }}
                />
            </div>

            <div>
                <p className='text-xl font-semibold'>Prescriptions Closed</p>

                <Line datasetIdKey="dayt"
                    data={{
                        labels: dispenseTrend.map(d => d.dayt),
                        datasets: [{
                            label: 'Drug Dispense Trend',
                            data: dispenseTrend.map(d => d.qty),
                            backgroundColor: 'blue',
                            borderColor: 'pink'
                        },
                        {
                            label: 'Open Prescriptions',
                            data: openPrescriptions.map(d => d.qty),
                            backgroundColor: 'red',
                            borderColor: 'orange',
                        }],
                    }}
                    options={{
                        scales: {
                            x: {
                                beginAtZero: true
                            },
                            y: {
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }}
                />
            </div>
        </div>

        <div className="card">
            <p className='text-xl font-semibold'>Top Performing Stock Levels</p>

            <table className='table'>
                <tbody>
                    {data.topStockLevels.map((row) => <tr key={row.id}>
                        <td>{row.name}</td>
                        <td>{Number(row.qty_on_hand)}</td>
                    </tr>)}
                </tbody>
            </table>

        </div>

        {/* {JSON.stringify(data)} */}
    </div>);
};

export default PharmacyIndex;
