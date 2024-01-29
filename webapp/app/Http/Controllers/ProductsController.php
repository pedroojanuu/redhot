<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

use App\Models\Product;
use App\Models\ProductCart;
use App\Models\Wishlist;
use App\Models\Visit;
use App\Models\Review;

use App\Events\ChangeInProductsPrice;
use App\Events\WishlistProductAvailable;
use App\Events\WishlistProductNotAvailable;

use Illuminate\Support\Facades\Log;

use App\Http\Controllers\FileController;

function verifyAdmin(): void
{
    if (Auth::guard('admin')->user() == null)
        abort(403);
}

class ProductsController extends Controller
{
    public function productsDetails(int $id)
    {
        $product = Product::findorfail($id);
        if ($product->deleted) abort(404);

        return view('pages.productDetails', [
            'product' => Product::findorfail($id),
            'discountFunction' => function ($price, $discount) {
                return $price * (1 - $discount);
            },
            
        ]);
    }

    public function listProducts(Request $request)
    {
        return view('pages.products', [
            'maxPrice' => Product::getMostExpensiveProductPrice(),
            'products' => Product::where('deleted', '=', false)->orderBy('id')->get(),
            'discountFunction' => function ($price, $discount) {
                return $price * (1 - $discount);
            },
            'allCategories' => Product::getAllCategories(),
            'visit' => Visit::create(['ip_address' => strval($request->ip()), 'timestamp' => now()])
        ]);
    }

    public function searchAndFilterProductsAPI(string $stringToSearch, string $filter)
    {
        if ($stringToSearch == '*')
            $products = Product::filterProducts(json_decode($filter));
        else
            $products = Product::searchAndFilterProducts($stringToSearch, json_decode($filter));
        foreach ($products as $product) {
            $product->avaliacao_media = $product->getProductRating();
            $product->numero_reviews = $product->getProductNumberOfReviews();
            $product->url_imagem = FileController::get('product', $product->id);
        }
        return json_encode($products);
    }

    public function addProductForm() : View
    {
        verifyAdmin();
        return view('pages.productsAdd');
    }

    public function addProduct(Request $request)
    {
        verifyAdmin();

        $request->validate([
            'name' => 'required|string|max:256',
            'price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:1',
            'stock' => 'required|numeric|min:0',
            'description' => 'required|string|max:256',
            'category' => 'nullable|string|max:256',
        ]);

        $newProduct = Product::create([
            'nome' => $request->name,
            'precoatual' => $request->price,
            'desconto' => ($request->discount ? $request->discount : 0),
            'stock' => $request->stock,
            'id_administrador' => 1,
            'descricao' => $request->description,
            'categoria' => ucfirst($request->category),
        ]);

        if ($request->file) {
            $fileController = new FileController();
            $hash = $fileController->upload($request, 'product', $newProduct->id);
            $newProduct->update(array('product_image' => $hash));
        }

        return redirect('/products/' . $newProduct->id);
    }

    public function editProductForm(int $id) : View
    {
        verifyAdmin();

        return view('pages.productsEdit', [
            'product' => Product::findorfail($id),
        ]);
    }

    public function editProduct(Request $request, int $id)
    {
        verifyAdmin();

        $request->validate([
            'name' => 'required|string|max:256',
            'price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'description' => 'required|string|max:256',
            'category' => 'string|max:256',
        ]);

        $oldProduct = Product::findorfail($id);
        if ($oldProduct->precoatual != $request->price || $oldProduct->desconto != $request->discount) {
            $usersWithProductInCart = ProductCart::where('id_produto', '=', $id)->get();
            foreach ($usersWithProductInCart as $userWithProductInCart)
                event(new ChangeInProductsPrice($userWithProductInCart->id_utilizador,
                    $oldProduct->id, $oldProduct->nome,
                    $oldProduct->precoatual * (1 - $oldProduct->desconto),
                    $request->price * (1 - $request->discount)));
        }

        if ($request->file && !($request->deletePhoto)) {
            $product = Product::findorfail($id);
            $oldPhoto = $product->product_image;
            $fileController = new FileController();
            $hash = $fileController->upload($request, 'product', $id);
            $product->update(array('product_image' => $hash));
            if ($oldPhoto) FileController::delete($oldPhoto);
        } else if ($request->deletePhoto) {
            $product = Product::findorfail($id);
            FileController::delete($product->product_image);
            $product->update(array('product_image' => null));
        }

        if ($request->stock <= 0 && $oldProduct->stock > 0) {
            $usersWithProductInWishlist = Wishlist::where('id_produto', '=', $id)->get();
            foreach ($usersWithProductInWishlist as $userWithProductInWishlist)
                event(new WishlistProductNotAvailable($userWithProductInWishlist->id_utilizador,
                    $oldProduct->id, $oldProduct->nome));
        } else if ($oldProduct->stock <= 0 && $request->stock > 0) {
            $usersWithProductInWishlist = Wishlist::where('id_produto', '=', $id)->get();
            foreach ($usersWithProductInWishlist as $userWithProductInWishlist)
                event(new WishlistProductAvailable($userWithProductInWishlist->id_utilizador,
                    $oldProduct->id, $oldProduct->nome));
        }

        Product::where('id', '=', $id)->update(array('nome' => $request->name,
            'precoatual' => $request->price,
            'desconto' => $request->discount,
            'stock' => $request->stock,
            'descricao' => $request->description,
            'categoria' => ucfirst($request->category)));

        return redirect('/products/' . $id);
    }

    public function changeStockProductForm(int $id) : View
    {
        verifyAdmin();

        return view('pages.productsChangeStock', [
            'product' => Product::findorfail($id),
        ]);
    }

    public function changeStockProduct(Request $request, int $id)
    {
        verifyAdmin();

        $request->validate([
            'stock' => 'required|numeric|min:0',
        ]);

        Product::where('id', '=', $id)->update(array('stock' => $request->stock));

        return redirect('/products/' . $id);
    }

    public static function deleteProduct(Request $request, int $id)
    {
        verifyAdmin();
        
        $product = Product::findorfail($id);
        if ($product->product_image) FileController::delete($product->product_image);
        
        $wishlistProducts = Wishlist::where('id_produto', '=', $id)->get();
        foreach($wishlistProducts as $wishlistProduct) $wishlistProduct->delete();

        $productCarts = ProductCart::where('id_produto', '=', $id)->get();
        foreach($productCarts as $productCart) $productCart->delete();

        $productReviews = Review::where('id_produto', '=', $id)->get();
        foreach($productReviews as $productReview) $productReview->delete();

        $product->deleted = true;
        $product->precoatual = 0;
        $product->save();

        return redirect('/adminProductsManage');
    }

    public function getProductById(int $id)
    {
        return Product::findorfail($id);
    }

    public function getSimilarProducts(array $productIds)
    {
        $similarProducts = [];
        foreach ($productIds as $productId) {
            $product = Product::findorfail($productId);
            $similarProducts[] = Product::where('categoria', '=', $product->categoria)->where('id', '!=', $productId)->get();
        }
        return $similarProducts;
    }
}
