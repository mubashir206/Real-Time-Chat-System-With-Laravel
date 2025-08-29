@extends('layouts.app')

@section('title', 'Settings')

@section('header-title', 'Settings')

@section('content')
    <div class="content-card w-100">
        <div class="row g-0">
            <div class="col-md-12">
                <h2 class="mb-4">Account Settings</h2>
                <form id="update-user-form">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ auth()->user()->name }}" required>
                            <div class="invalid-feedback" id="name-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ auth()->user()->email }}" disabled>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary float-end" id="update-user-btn">Update</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const updateUserBtn = document.getElementById('update-user-btn');
            const updateUserForm = document.getElementById('update-user-form');
            const nameInput = document.getElementById('name');
            const nameError = document.getElementById('name-error');

            updateUserBtn.addEventListener('click', function() {
                nameError.textContent = '';
                nameInput.classList.remove('is-invalid');

                const formData = new FormData(updateUserForm);

                fetch("{{ route('settings.update') }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json().then(data => ({ status: response.status, data })))
                .then(({ status, data }) => {
                    if (status !== 200) throw data;
                    alert('Profile updated successfully!');
                    window.location.reload();
                })
                .catch(error => {
                    if (error.errors?.name) {
                        nameInput.classList.add('is-invalid');
                        nameError.textContent = error.errors.name[0];
                    } else {
                        console.error('Error updating profile:', error);
                        alert(error.message || 'Failed to update profile.');
                    }
                });
            });
        });
    </script>
@endsection
