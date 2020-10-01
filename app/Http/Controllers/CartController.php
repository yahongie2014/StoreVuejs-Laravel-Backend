<?php

namespace App\Http\Controllers;

use App\Cart;
use App\CartItem;
use App\Http\Resources\CartItemCollection;
use App\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function index()
    {
        $userID = Auth::user()->id;
        $getCart = Cart::where('userID', $userID)->get()->toArray();
        $items = CartItem::where('cart_id', $getCart[0]['id'])->get();
        if (!count($items)) {
            return response()->json([
                "Success" => true,
                "cart" => null,
                "cartKey" => 0,
                "count" => 0,
            ]);
        }
        return response()->json([
            "Success" => true,
            "cart" => $items,
            "cartKey" => $items[0]['cart_id'],
            "count" => count($items),
        ]);


    }

    /**
     * Store a newly created Cart in storage and return the data to the user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if (Auth::guard('api')->check()) {
            $userID = auth('api')->user()->getKey();
        }
        $exist = Cart::where('userID', $userID)->first();
        if ($exist) {
            $old = $exist;
        } else {
            $old = Cart::updateOrCreate([
                'id' => md5(uniqid(rand(), true)),
                'key' => md5(uniqid(rand(), true)),
                'userID' => isset($userID) ? $userID : null,
            ]);
        }
        $Product = CartItem::where(['product_id' => $request->id])->first();
        if ($Product) {
            CartItem::where(['cart_id' => $old->id, 'product_id' => $request->id])->update(['quantity' => $Product->quantity + 1]);
        } else {
            CartItem::create(['cart_id' => $old->id, 'product_id' => $request->id, 'name' => $request->name, 'price' => $request->price, 'image' => $request->image, 'quantity' => 1]);
        }
        return response()->json([
            'Success' => true,
        ], 200);

    }

    /**
     * Display the specified Cart.
     *
     * @param \App\Cart $cart
     * @return \Illuminate\Http\Response
     */
    public function show(Cart $cart, Request $request)
    {

    }

    /**
     * Remove the specified Cart from storage.
     *
     * @param \App\Cart $cart
     * @return \Illuminate\Http\Response
     */

    public function destroy(Request $request, $id)
    {
        $c = CartItem::where('cart_id', $id)->first();
        if ($c) {
            $item = CartItem::find($c['id']);
            $item->delete();
            return response()->json(["success" => true], 200);
        } else {
            return response()->json([
                'message' => 'The CarKey you provided does not match the Cart Key for this Cart.',
            ], 400);
        }

    }

    public function emptyCart($key)
    {
        CartItem::whereIn('cart_id', [$key])->delete();
        return response()->json(["success" => true], 200);
    }

    public function updateQty(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'price' => 'required',
            'quantity' => 'required',
        ]);

        $Product = CartItem::where(['cart_id' => $request->key])->first();
        CartItem::where(['id' => $Product->id])->update(['quantity' => $request->quantity, 'price' => $request->price]);
        return response()->json(["success" => true], 200);
    }

    /**
     * Adds Products to the given Cart;
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Cart $cart
     * @return void
     */

}
