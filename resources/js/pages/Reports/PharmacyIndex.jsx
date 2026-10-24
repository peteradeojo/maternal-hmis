import { Chart as ChartJS, ArcElement, Tooltip, Legend } from "chart.js";
import { Line, Bar } from "react-chartjs-2";
import { getRandomColor } from "../utils";
import { useState } from "react";

ChartJS.register(ArcElement, Tooltip, Legend);

const PharmacyIndex = ({ data }) => {
    const [topDrugs, setTopDrugs] = useState(data.topDrugs);
    const [dispenseTrend, setDTrend] = useState(data.dailyDispensings);

    return (<>
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
                <p className='text-xl font-semibold'>Trend</p>

                <Line datasetIdKey="dayt"
                    data={{
                        labels: dispenseTrend.map(d => d.dayt),
                        datasets: [{
                            label: 'Drug Dispense Trend',
                            data: dispenseTrend.map(d => d.qty),
                        }],
                    }} />
            </div>
        </div>

        {/* {JSON.stringify(data)} */}
    </>);
};

export default PharmacyIndex;
