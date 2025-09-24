<x-guest-layout>
    <div class="text-center mb-4">
        <h2 class="fw-bold text-dark mb-2">Welcome Back</h2>
        <p class="text-muted">Please select your user type to continue</p>
    </div>

    <div class="d-grid gap-3">
        <a href="{{ route('login.client') }}" class="btn btn-outline-success btn-lg text-start p-4 text-decoration-none">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="icon-circle me-3">
                        <i class="bi bi-person-fill text-success"></i>
                    </div>
                    <div>
                        <h5 class="mb-1 fw-semibold text-dark">Client Login</h5>
                        <small class="text-muted">Access your account and manage services</small>
                    </div>
                </div>
                <i class="bi bi-arrow-right text-success"></i>
            </div>
        </a>

        <a href="{{ route('login.contractor') }}" class="btn btn-outline-success btn-lg text-start p-4 text-decoration-none">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="icon-circle me-3">
                        <i class="bi bi-truck text-success"></i>
                    </div>
                    <div>
                        <h5 class="mb-1 fw-semibold text-dark">Contractor Login</h5>
                        <small class="text-muted">Manage your business operations</small>
                    </div>
                </div>
                <i class="bi bi-arrow-right text-success"></i>
            </div>
        </a>

        <a href="{{ route('login.admin') }}" class="btn btn-outline-success btn-lg text-start p-4 text-decoration-none">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="icon-circle me-3">
                        <i class="bi bi-gear-fill text-success"></i>
                    </div>
                    <div>
                        <h5 class="mb-1 fw-semibold text-dark">Admin Login</h5>
                        <small class="text-muted">Access system administration</small>
                    </div>
                </div>
                <i class="bi bi-arrow-right text-success"></i>
            </div>
        </a>
    </div>

    <div class="text-center mt-4">
        <p class="text-muted mb-0">Don't have an account? 
            <a href="/" class="text-success text-decoration-none fw-medium">Register here</a>
        </p>
    </div>
</x-guest-layout>