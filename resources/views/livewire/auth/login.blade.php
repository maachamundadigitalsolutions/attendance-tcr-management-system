<div class="login-box">
    <div class="login-logo">
        <a href="#"><b>Team</b>Tracker</a>
    </div>

    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Sign in to start your session</p>

            <form wire:submit.prevent="login">
                {{-- Email --}}
                <div class="input-group mb-3">
                    <input type="email" wire:model="email" class="form-control" placeholder="Email" required>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-envelope"></span></div>
                    </div>
                </div>
                @error('email') <span class="text-danger text-sm">{{ $message }}</span> @enderror

                {{-- Password --}}
                <div class="input-group mb-3">
                    <input type="password" wire:model="password" class="form-control" placeholder="Password" required>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-lock"></span></div>
                    </div>
                </div>
                @error('password') <span class="text-danger text-sm">{{ $message }}</span> @enderror

                {{-- Submit --}}
                <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <input type="checkbox" id="remember">
                            <label for="remember">Remember Me</label>
                        </div>
                    </div>
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                    </div>
                </div>
            </form>

            <!-- <p class="mb-1 mt-3">
                <a href="#">I forgot my password</a>
            </p> -->
        </div>
    </div>
</div>
