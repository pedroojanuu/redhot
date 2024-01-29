<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Admin;
use App\Models\Order;
use App\Models\Wishlist;
use App\Models\ProductCart;

use App\Http\Controllers\FileController;
use App\Http\Controllers\AdminController;

function verifyUser(User $user): void
{
    if ((Auth::user() == null || Auth::user()->id != $user->id) && Auth::guard('admin')->user() == null)
        abort(403);
}

function verifyToken(User $user, string $token)
{

    $validTokens = session('validTokens');

    if ($token == null || $token == "" || strlen($token) != 64 || !in_array($token, $validTokens) || $user->id != array_search($token, $validTokens))
        abort(403);
}

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function showProfileDetails(int $id): View
    {
        $user = User::findOrFail($id);

        if ($user->deleted) abort(404);

        verifyUser($user);

        $orders = Order::where('id_utilizador', $id)->get();

        return view('pages.user', [
            'user' => $user,
            'totalOrders' => $this->getTotalOrders($id),
            'totalReviews' => $this->getTotalReviews($id),
            'orders' => $orders,
            'unreadNotifications' => $this->getNumberOfUreadNotifications($id)
        ]);
    }

    public function editProfileForm(int $id): View
    {
        $user = User::findOrFail($id);

        verifyUser($user);

        return view('pages.editUser', [
            'user' => $user
        ]);
    }

    public function editProfile(Request $request, int $id)
    {
        $user = User::findOrfail($id);

        verifyUser($user);

        if ($request->password !== $request->password_confirmation)
            return back()->withErrors(['password' => 'As passwords introduzidas não coincidem']);

        if (!Hash::check($request->password, $user->password))
            return back()->withErrors(['password' => 'A password introduzida não corresponde à sua password atual']);

        $request->validate([
            'nome' => 'required|string|max:256',
            'email' => 'required|email|max:256',
        ]);

        if ($request->email != $user->email) {
            $request->validate([
                'email' => 'unique:utilizador',
                'email' => 'unique:administrador'
            ]);
        }

        if ($request->file && !($request->deletePhoto)) {
            $oldPhoto = $user->profile_image;
            $fileController = new FileController();
            $hash = $fileController->upload($request, 'profile', $id);
            $user->update(array('profile_image' => $hash));
            if ($oldPhoto) FileController::delete($oldPhoto);
        } else if ($request->deletePhoto) {
            FileController::delete($user->profile_image);
            $user->update(array('profile_image' => null));
        }

        User::where('id', '=', $id)->update(array('nome' => $request->nome, 'email' => $request->email));

        return redirect('/users/' . $id);
    }

    public function deleteAccountForm(int $id): View
    {
        $user = User::findOrFail($id);

        $this->authorize('deleteAccount', $user);

        return view('pages.delete_account', [
            'user' => $user
        ]);
    }

    public function deleteAccount(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        $this->authorize('deleteAccount', $user);

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'A password introduzida está incorreta.']);
        }

        if ($user->profile_image)
            FileController::delete($user->profile_image);

        $wishlistProducts = Wishlist::where('id_utilizador', '=', $id)->get();
        foreach($wishlistProducts as $wishlistProduct) $wishlistProduct->delete();

        $productCarts = ProductCart::where('id_utilizador', '=', $id)->get();
        foreach($productCarts as $productCart) $productCart->delete();

        $user->update(array(
            'nome' => null,
            'email' => null,
            'password' => null,
            'telefone' => null,
            'morada' => null,
            'codigo_postal' => null,
            'localidade' => null,
            'remember_token' => null,
            'profile_image' => null,
            'deleted' => true
        ));

        return redirect('/logout');
    }

    // edit password version
    public function editPasswordForm(Request $request, int $id): View
    {
        $user = User::findOrFail($id);

        return view('pages.editPassword', [
            'user' => $user,
        ]);
    }

    // edit password version
    public function editPassword(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'new_password' => 'required',
        ]);

        if (!Hash::check($request->old_password, $user->password))
            return back()->withErrors(['password' => 'A sua password atual está incorreta']);

        if (strlen($request->new_password) < 8)
            return back()->withErrors(['new_password' => 'A nova password deve ter, pelo menos, 8 caracteres']);

        if ($request->new_password !== $request->new_password_confirmation)
            return back()->withErrors(['password_confirmation' => 'As passwords introduzidas não coincidem']);

        User::where('id', '=', $id)->update(array('password' => Hash::make($request->new_password)));

        sleep(1);

        return redirect('/users/' . $id);
    }

    // forgot password version
    public function changePasswordForm(Request $request, int $id, string $token): View
    {
        $user = User::findOrFail($id);

        verifyToken($user, $token);

        return view('pages.changePassword', [
            'user' => $user,
            'token' => $token
        ]);
    }

    // forgot password version
    public function changePassword(Request $request, int $id, string $token)
    {
        $user = User::findOrFail($id);

        verifyToken($user, $token);

        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        User::where('id', '=', $id)->update(array('password' => Hash::make($request->password)));

        return redirect('/login');
    }

    public function getTotalOrders(int $id): int
    {
        $user = User::findOrFail($id);

        verifyUser($user);

        return $user->orders()->count();
    }

    public function getTotalReviews(int $id): int
    {
        $user = User::findOrFail($id);

        verifyUser($user);

        return $user->totalReviews();
    }

    public function getOrders(int $id)
    {
        $user = User::findOrFail($id);

        verifyUser($user);

        return $user->orders()->get();
    }

    public function getNumberOfUreadNotifications(int $id): int
    {
        $user = User::findOrFail($id);

        verifyUser($user);

        return $user->getNumberOfUreadNotifications();
    }

    public function banUser(Request $request, int $id)
    {
        AdminController::verifyAdmin2();

        $user = User::findOrFail($id);

        if ($user->hasPendingOrders()) {
            return back()->withErrors(['ban' => 'Não pode banir um utilizador com encomendas pendentes!']);
        }

        $user->ban();

        return redirect('/adminUsers');
    }

    public function unbanUser(Request $request, int $id)
    {
        AdminController::verifyAdmin2();

        $user = User::findOrFail($id);

        $user->unban();

        return redirect('/adminUsers');
    }

    public function becomeAdmin(Request $request, int $id)
    {
        AdminController::verifyAdmin2();

        $user = User::findOrFail($id);

        if ($user->hasPendingOrders()) {
            return back()->withErrors(['promote' => 'Não pode promover um utilizador com encomendas pendentes!']);
        }

        $admin = new Admin();
        $admin->nome = $user->nome;
        $admin->email = $user->email;
        $admin->password = $user->password;
        $admin->save();

        $user->became_admin = true;
        $user->save();

        return redirect('/adminUsers');
    }

    public function becomeUser(Request $request, int $id)
    {
        AdminController::verifyAdmin2();

        $user = User::findOrFail($id);

        $admin = Admin::where('email', '=', $user->email);
        $admin->delete();

        $user->became_admin = false;
        $user->save();

        return redirect('/adminUsers');
    }
}
