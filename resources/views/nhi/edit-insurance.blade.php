<div class="container">
    <form action="{{ route('nhi.edit-insurance', $profile) }}" method="post">
        @csrf
        @include('records.components.hmi-form', ['profile' => $profile])

        <div class="form-group">
            <button class="btn bg-primary">Submit</button>
        </div>
    </form>
</div>
