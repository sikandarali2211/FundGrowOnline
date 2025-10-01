@extends('layouts.user')

@section('content')
    @php($user = $user ?? auth()->user())
    <div class="main-panel" style="margin-top: 4rem; background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card shadow-lg"
                        style="background: rgba(7, 45, 66, 0.85); backdrop-filter: blur(10px); border-radius: 15px; border: 1px solid rgba(59, 209, 122, 0.3);">
                        <div class="card-body">
                            <h4 class="mb-4 text-center" style="color: #3bd17a;">Update Profile</h4>

                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                {{-- Profile Picture --}}
                                <div class="mb-3 text-center">
                                    <img id="profilePreview" 
                                        src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) . '?t=' . time() : asset('assets/images/default-avatar.png') }}"
                                        alt="Profile Picture" 
                                        class="rounded-circle mb-3" 
                                        width="100" 
                                        height="100"
                                        style="object-fit: cover; border: 2px solid #3bd17a;">

                                    <div class="d-flex justify-content-center" style="gap: 0.75rem;">
                                        <!-- Hidden file input -->
                                        <input type="file" id="profileInput" name="profile_picture" class="d-none" accept="image/*"
                                            onchange="previewImage(this)">

                                        <!-- Upload button -->
                                        <button type="button" class="btn btn-outline-light" style="border-radius: 10px;"
                                            onclick="document.getElementById('profileInput').click()">
                                            Upload Photo
                                        </button>

                                        <!-- Save button -->
                                        <button type="submit" name="action" value="save" class="btn btn-success"
                                            style="border-radius: 10px;">
                                            Save
                                        </button>
                                    </div>

                                    <!-- File name preview -->
                                    <small id="fileName" class="text-light d-block mt-2"></small>
                                </div>


                                {{-- Full Name --}}
                                <div class="mb-3">
                                    <label for="fullName" class="form-label" style="color: #e0e0e0;">Full Name</label>
                                    <input type="text" class="form-control" id="fullName" name="fullName"
                                        value="{{ old('fullName', $user->full_name) }}"
                                        style="background-color: rgba(0, 0, 0, 0.5); color: #e0e0e0; border: 1px solid rgba(59, 209, 122, 0.5); border-radius: 10px;">
                                </div>

                                {{-- Email --}}
                                <div class="mb-3">
                                    <label for="email" class="form-label" style="color: #e0e0e0;">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ old('email', $user->email) }}" readonly
                                        style="background-color: rgba(0, 0, 0, 0.5); color: #e0e0e0; border: 1px solid rgba(59, 209, 122, 0.5); border-radius: 10px;">
                                </div>

                                {{-- Phone --}}
                                <div class="mb-3">
                                    <label for="phone" class="form-label" style="color: #e0e0e0;">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone"
                                        value="{{ old('phone', $user->phone) }}"
                                        style="background-color: rgba(0, 0, 0, 0.5); color: #e0e0e0; border: 1px solid rgba(59, 209, 122, 0.5); border-radius: 10px;">
                                </div>

                                {{-- Country --}}
                                <div class="mb-3">
                                    <label for="country" class="form-label" style="color: #e0e0e0;">Country</label>
                                    <input type="text" class="form-control" id="country" name="country"
                                        value="{{ old('country', $user->country) }}"
                                        style="background-color: rgba(0, 0, 0, 0.5); color: #e0e0e0; border: 1px solid rgba(59, 209, 122, 0.5); border-radius: 10px;">
                                </div>

                                <div class="text-center">
                                    <button type="submit" name="action" value="update" class="btn"
                                        style="background: linear-gradient(90deg, #3bd17a, #00d4aa); color: #0d1b2a; font-weight: 600; border-radius: 10px; box-shadow: 0 0 12px rgba(59, 209, 122, 0.7);">
                                        Update Profile
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('profilePreview');
            const fileName = document.getElementById('fileName');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                }
                
                reader.readAsDataURL(input.files[0]);
                fileName.innerText = input.files[0].name;
            }
        }
    </script>
@endsection
