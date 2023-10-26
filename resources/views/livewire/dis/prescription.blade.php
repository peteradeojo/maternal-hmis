<div>
    <form action="" method="post">
        @csrf
        <table class="table table-list" id="p-table">
            <thead>
                <tr>
                    <th>Prescription</th>
                    <th>Available</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total = 0;
                @endphp
                @foreach ($doc->treatments as $t)
                    @php
                        $total += $t->amount;
                    @endphp
                    <tr>
                        <td>{{ $t->name }} {{ $t->dosage }} {{ $t->frequency }} {{ $t->duration }}</td>
                        <td><input type="checkbox" name="available[{{ $t->id }}]" data-id="{{ $t->id }}"
                                class="availability" @if ($t->available) checked @endif>
                        </td>
                        <td>
                            <input type="number" name="amount[{{ $t->id }}]" step="0.01" min="0"
                                data-id="{{ $t->id }}" class="amount form-control" value="{{ $t->amount }}" />
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">Total</td>
                    <td id="total">{{ $total }}</td>
                </tr>
                <tr>
                    <td>Are you done with this quote? <input type="checkbox" name="complete" @if ($doc->all_prescriptions_available)
                        checked
                    @endif></td>
                </tr>
            </tfoot>
        </table>

        <div class="pt-1"></div>
        <button class="form-control btn btn-blue">Submit</button>
    </form>

    @push('scripts')
        <script>
            $(() => {
                const totals = [];
                const totalEl = document.querySelector("#total");

                const checkAvailable = (id) => {
                    const checked = document.querySelector(`.availability[data-id="${id}"]`);

                    return checked.checked;
                }

                const updateTotal = (el) => {
                    const sum = totals.reduce((a, b) => {
                        const id = b.getAttribute('data-id');

                        if (checkAvailable(id))
                            return parseFloat(a) + b.value;

                        return a;
                    }, 0);
                    totalEl.innerHTML = parseFloat(sum);
                }

                document.querySelectorAll('.availability').forEach(function(el) {
                    el.addEventListener('change', updateTotal);
                });

                document.querySelectorAll('.amount').forEach(function(el) {
                    totals.push(el);
                    el.addEventListener('change', updateTotal);
                });

                updateTotal();
            });
        </script>
    @endpush
</div>
