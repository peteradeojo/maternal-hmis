<div>
    <table class="table">
        <thead>
            <tr>
                <th></th>
                <th class="py-1">12 AM - 3 AM</th>
                <th class="py-1">3 AM - 6 AM</th>
                <th class="py-1">6 AM - 9 AM</th>
                <th class="py-1">9 AM - 12 PM</th>
                <th class="py-1">12 PM - 3 PM</th>
                <th class="py-1">3 PM - 6 PM</th>
                <th class="py-1">6 PM - 9 PM</th>
                <th class="py-1">9 PM - 12 AM</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($admission->administrations->groupBy(fn($item, $key) => $item->created_at->format('Y-m-d')) as $dt => $adms)
                <tr>
                    <td colspan="9" class="text-center font-semibold">{{ $dt }}</td>
                </tr>
                @foreach ($adms->groupBy('treatment_id') as $treatment_id => $adm)
                    <tr>
                        <td>
                            {{ $adm->first()?->treatments }}
                        </td>
                        @foreach (range(0, 21, 3) as $hr)
                            @php
                                $lower_bound = $dt . ' ' . str_pad($hr, 2, '0', STR_PAD_LEFT) . ':00:00';
                                $upper_bound = $dt . ' ' . str_pad($hr + 3, 2, '0', STR_PAD_LEFT) . ':00:00';

                                $slots = $adm
                                    ->where('treatment_id', $treatment_id)
                                    ->where('created_at', '>=', $lower_bound)
                                    ->where('created_at', '<', $upper_bound)
                                    ->sortBy('created_at');
                                $count = $slots->count();
                            @endphp
                            <td>
                                @if ($count > 0)
                                    @foreach ($slots as $administration)
                                        <x-tooltip :content="$administration->created_at->format('h:i A') .
                                            ' - ' .
                                            $administration->minister?->name">
                                            <i class="fa fa-square text-green-500"></i>
                                        </x-tooltip>
                                    @endforeach
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</div>
