<div class="pb-8">
    <x-patient-profile :patient="$scan->patient" />

    <div class="py-2">
        <table class="table bordered">
            <tbody>
                <tr>
                    <td class="font-semibold">Date</td>
                    <td>{{ date('Y-m-d h:i A', strtotime($scan->results->date)) }}</td>
                </tr>
                <tr>
                    <td class="font-semibold">Clinician</td>
                    <td>{{ $scan->results->clinician }}</td>
                </tr>
                <tr>
                    <td class="font-semibold">Investigation</td>
                    <td>{{ $scan->name }}</td>
                </tr>
                <tr>
                    <td class="font-semibold">Age</td>
                    <td>{{ $scan->results->age }}</td>
                </tr>
                <tr>
                    <td class="font-semibold">LMP</td>
                    <td>{{ $scan->results->lmp }}</td>
                </tr>
                <tr>
                    <td class="font-semibold">Gravidity</td>
                    <td>{{ $scan->results->gravidity }}</td>
                </tr>
                <tr>
                    <td class="font-semibold">Parity</td>
                    <td>{{ $scan->results->parity }}</td>
                </tr>
                <tr>
                    <td class="font-semibold">Number of Fetuses</td>
                    <td>{{ $scan->results->number_of_fetuses }}</td>
                </tr>
                <tr>
                    <td class="font-semibold">Viability</td>
                    <td>{{ $scan->results->viability }}</td>
                </tr>
                <tr>
                    <td class="font-semibold">Crown Rump Length (CRL)</td>
                    <td>{{ $scan->results->crown_rump_length }}</td>
                </tr>
                <tr>
                    <td class="font-semibold">Head Circumference</td>
                    <td>{{ $scan->results->head_circumference }}</td>
                </tr>
                <tr>
                    <td class="font-semibold">Abodminal Circumference</td>
                    <td>{{ $scan->results->abdominal_circumference }}</td>
                </tr>
                <tr>
                    <td class="font-semibold">Biparietal Diameter</td>
                    <td>{{ $scan->results->biparietal_diameter }}</td>
                </tr>
                <tr>
                    <td class="font-semibold">GSD</td>
                    <td>{{ $scan->results->gestational_sac_diameter }}</td>
                </tr>
                <tr>
                    <td class="font-semibold">Amniotic Fluid Volume</td>
                    <td>{{ $scan->results->amniotic_fluid_volume }}</td>
                </tr>
                <tr>
                    <td class="font-semibold">Femur Length</td>
                    <td>{{ $scan->results->femur_length }}</td>
                </tr>
                <tr>
                    <td class="font-semibold">Estimated Fetal Weight</td>
                    <td>{{ $scan->results->estimated_fetal_weight }}</td>
                </tr>
                <tr>
                    <td class="font-semibold">EGA</td>
                    <td>{{ $scan->results->estimated_gestational_age }}</td>
                </tr>
                <tr>
                    <td class="font-semibold">EDD</td>
                    <td>{{ $scan->results->estimated_date_of_delivery }}</td>
                </tr>
                <tr>
                    <td class="font-semibold">Placental Location</td>
                    <td>{{ $scan->results->placental_location }}</td>
                </tr>
                <tr>
                    <td class="font-semibold">Placental Maturity</td>
                    <td>{{ $scan->results->placental_maturity }}</td>
                </tr>
                <tr>
                    <td class="font-semibold">Fetal Lie</td>
                    <td>{{ $scan->results->fetal_lie }}</td>
                </tr>
                <tr>
                    <td class="font-semibold">Fetal Presentation</td>
                    <td>{{ $scan->results->fetal_presentation }}</td>
                </tr>
                <tr>
                    <td class="font-semibold">Other findings</td>
                    <td>{{ $scan->results->other_findings }}</td>
                </tr>
                <tr>
                    <td class="font-semibold">Conclusion</td>
                    <td>{{ $scan->results->conclusion }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
