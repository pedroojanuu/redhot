<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Illuminate\View\View;

use App\Models\User;

use App\Http\Controllers\ProductCartController;

function verifyNotAutenticated(): void
{
    if (Auth::check() || Auth::guard('admin')->check())
        abort(403);
}

class RegisterController extends Controller
{
    /**
     * Display a login form.
     */
    public function showRegistrationForm(): View
    {
        verifyNotAutenticated();
        return view('auth.register');
    }

    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        verifyNotAutenticated();
        $credentials = $request->validate([
            'nome' => 'required|string|max:256',
            'email' => 'required|email|max:256',
            'password' => 'required|confirmed'
        ], [
            'confirmed' => 'As passwords introduzidas não coincidem.'
        ]);

        if (User::where('email', $request->email)->first() != null) {
            return redirect('/register')
                ->withInput($request->only('nome', 'email'))
                ->withErrors(['email' => 'O endereço de e-mail que introduziu já se encontra registado.']);
        }
        
        if (strlen($request->password) < 8) {
            return back()
                ->withInput($request->only('nome', 'email'))
                ->withErrors(['password' => 'A password deve ter, pelo menos, 8 caracteres.']);
        }
        
        $newUser = User::create([
            'nome' => $request->nome,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $guestId = session('guestID');

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            if ($guestId) {
                ProductCartController::transferGuestCart($guestId);
                return redirect()->intended('/cart');
            }

            return redirect()->intended('/products');
        }

        sleep(1);

        return back()->withErrors([
            'email' => 'Erro ao criar conta. Por favor tente novamente.',
        ])->onlyInput('nome', 'email');
    }
}
