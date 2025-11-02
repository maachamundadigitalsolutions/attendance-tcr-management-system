<div class="card">
    <div class="card-body login-card-body">
        <p class="login-box-msg">Sign in to start your session</p>

        <form wire:submit.prevent="login">
            <div class="input-group mb-3">
                <input type="text" wire:model="loginField" class="form-control" placeholder="Email or User ID">
                <div class="input-group-append">
                    <div class="input-group-text"><span class="fas fa-user"></span></div>
                </div>
            </div>
            @error('loginField') <span class="text-danger">{{ $message }}</span> @enderror

            <div class="input-group mb-3">
                <input type="password" wire:model="password" class="form-control" placeholder="Password">
                <div class="input-group-append">
                    <div class="input-group-text"><span class="fas fa-lock"></span></div>
                </div>
            </div>
            @error('password') <span class="text-danger">{{ $message }}</span> @enderror

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
    </div>
</div>
