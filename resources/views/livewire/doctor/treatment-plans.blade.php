<div class="grid gap-y-4">
    {{-- Be like water. --}}

    <form wire:submit.prevent="savePlan" method="post">
        <div class="form-group">
            <label>Describe your treatment plan</label>
            <textarea wire:model="plan" name="plan" class="form-control" rows="10" required></textarea>
        </div>
        <div class="form-group">
            <button class="btn bg-primary">Submit <i class="fa fa-save"></i></button>
        </div>
    </form>

    <div>
        @forelse ($origin->treatment_plans as $plan)
            <div class="p-2 bg-gray-100">
                <p><b>Plan:</b><br /> @nl2br($plan)</p>
                <p><b>Attending: </b>{{ $plan->recorder?->name ?? $plan->user->name }}</p>
                <p><small>{{ $plan->created_at?->format('Y-m-d h:i A') }}</small></p>
                <p class="flex-center justify-between">
                    <span>Status: {{ $plan->status->name }}</span>

                    @if ($plan->status == Status::active)
                        <button wire:click="togglePlan({{ $plan->id }})" class="btn bg-red-400">Deactivate</button>
                    @else
                        <button wire:click="togglePlan({{ $plan->id }})" class="btn bg-blue-400">Activate</button>
                    @endif
                </p>
            </div>
        @empty
            <p>No plan has been added.</p>
        @endforelse
    </div>
</div>
