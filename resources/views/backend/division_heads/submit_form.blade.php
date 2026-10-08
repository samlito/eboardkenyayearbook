@extends('admin_dashboard')
@section('admin')

    <div class="content">
        <div class="container-fluid">

            <h4 class="page-title">Submit Board Paper for: {{ $meeting->title }}</h4>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('division.head.store', $meeting->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group mb-3">
                    <label>Title of Board Paper</label>
                    <input type="text" name="title" class="form-control" required>
                </div>

                <div class="form-group mb-3">
                    <label>Main Paper </label>
                    <input type="file" name="file" class="form-control" required>
                </div>

{{--                <div class="form-group mb-3">--}}
{{--                    <label>Supporting Documents (optional)</label>--}}
{{--                    <input type="file" name="supporting_docs" class="form-control">--}}
{{--                </div>--}}
                <div class="form-group mb-3">
                    <label>Annexures</label>
                    <div id="annexures-wrapper">
                        <div class="annexure-item border rounded p-3 mb-2">
{{--                            <input type="text" name="annexures[0][title]" class="form-control mb-2" placeholder="Annexure Title" required>--}}
{{--                            <textarea name="annexures[0][description]" class="form-control mb-2" placeholder="Description (optional)"></textarea>--}}
{{--                            <input type="file" name="annexures[0][file]" class="form-control" accept=".pdf,.xls,.xlsx" required>--}}
                            <input type="text" name="annexures[0][title]" class="form-control mb-2" placeholder="Annexure Title">
                            <textarea name="annexures[0][description]" class="form-control mb-2" placeholder="Description (optional)"></textarea>
                            <input type="file" name="annexures[0][file]" class="form-control" accept=".pdf,.xls,.xlsx">

                        </div>
                    </div>
                    <button type="button" id="add-annexure" class="btn btn-sm btn-secondary mt-2">+ Add Annexure</button>
                </div>

{{--                <button type="submit" class="btn btn-success">Submit Paper</button>--}}

                <button type="submit" id="submitBtn" class="btn btn-success">
                    <span id="btnText">Submit Paper</span>
                    <span id="btnSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>




            </form>

        </div>
    </div>
{{--    <script>--}}
{{--        let annexureIndex = 1;--}}
{{--        document.getElementById('add-annexure').addEventListener('click', function() {--}}
{{--            let wrapper = document.getElementById('annexures-wrapper');--}}
{{--            let item = document.createElement('div');--}}
{{--            item.classList.add('annexure-item','border','rounded','p-3','mb-2');--}}
{{--            item.innerHTML = `--}}
{{--        <input type="text" name="annexures[${annexureIndex}][title]" class="form-control mb-2" placeholder="Annexure Title" required>--}}
{{--        <textarea name="annexures[${annexureIndex}][description]" class="form-control mb-2" placeholder="Description (optional)"></textarea>--}}
{{--        <input type="file" name="annexures[${annexureIndex}][file]" class="form-control" accept=".pdf,.xls,.xlsx" required>--}}
{{--    `;--}}
{{--            wrapper.appendChild(item);--}}
{{--            annexureIndex++;--}}
{{--        });--}}
{{--    </script>--}}
    <script>
        // Annexure add logic remains unchanged
        let annexureIndex = 1;
        document.getElementById('add-annexure').addEventListener('click', function() {
            let wrapper = document.getElementById('annexures-wrapper');
            let item = document.createElement('div');
            item.classList.add('annexure-item','border','rounded','p-3','mb-2');
            item.innerHTML = `
            <input type="text" name="annexures[${annexureIndex}][title]" class="form-control mb-2" placeholder="Annexure Title" required>
            <textarea name="annexures[${annexureIndex}][description]" class="form-control mb-2" placeholder="Description (optional)"></textarea>
            <input type="file" name="annexures[${annexureIndex}][file]" class="form-control" accept=".pdf,.xls,.xlsx" required>
        `;
            wrapper.appendChild(item);
            annexureIndex++;
        });

        // Submit button logic
        const form = document.querySelector('form');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnSpinner = document.getElementById('btnSpinner');

        form.addEventListener('submit', function() {
            submitBtn.disabled = true;
            btnText.textContent = "Submitting, please wait...";
            btnSpinner.classList.remove('d-none');
        });
    </script>

@endsection
