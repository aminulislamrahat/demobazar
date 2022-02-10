<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Slider;
use App\Models\Product;
use App\Models\Category;
use App\Models\Client;
use App\Models\Order;
use App\Cart;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
use Stripe\Charge;
use Stripe\Stripe;

class ClientController extends Controller
{
    //
    public function home()
    {
        $sliders = Slider::All()->where('status',1);
        $products = Product::All()->where('status', 1);

        return view('client.home')->with('sliders', $sliders)->with('products', $products);
    }

    public function cart()
    {
        if(!Session::has('cart')){
            return view('client.cart');
        }
        $oldCart = Session::has('cart')? Session::get('cart'):null;
        $cart = new Cart($oldCart);
        return view('client.cart', ['products' => $cart->items]);
    }
    public function checkout()
    {
        if(!Session::has('client')){
            return view('client.login');
        }
        return view('client.checkout');
    }
    public function shop()
    {
        $categories = Category::All();
        $products = Product::All()->where('status', 1);
        return view('client.shop')->with('categories', $categories)->with('products', $products);
    }
    public function login()
    {
        return view('client.login');
    }
    public function signup()
    {
        return view('client.signup');
    }
    public function orders()
    {
        $orders = Order::All();

        $orders->transform(function($order, $key){
            $order->cart = unserialize($order->cart);
            return $order;
        });

        return view('admin.orders')->with('orders', $orders);
    }
    public function addtocart($id)
    {
        $product = Product::find($id);

        $oldCart = Session::has('cart')? Session::get('cart'):null;
        $cart = new Cart($oldCart);
        $cart->add($product, $id);
        Session::put('cart', $cart);

        //dd(Session::get('cart'));
        //return redirect::to('/shop');
        return back();
    }

    public function update_qty(Request $req, $id)
    {
        $oldCart = Session::has('cart')? Session::get('cart'):null;
        $cart = new Cart($oldCart);
        $cart->updateQty($id, $req->quantity);
        Session::put('cart', $cart);

        //dd(Session::get('cart'));
        //return redirect('/cart');
        return back();
    }

    public function remove_from_cart($id)
    {
        $oldCart = Session::has('cart')? Session::get('cart'):null;
        $cart = new Cart($oldCart);
        $cart->removeItem($id);

        if(count($cart->items) > 0){
            Session::put('cart', $cart);
        }
        else{
            Session::forget('cart');
        }

        //dd(Session::get('cart'));
        return redirect('/cart');
    }

    public function create_client(Request $req)
    {
        $this->validate($req, [ 'email' => 'required|email|unique:clients',
                                'password' => 'required|min:4']);

        $client = new Client();
        $client->email = $req->input('email');
        $client->password = bcrypt($req->input('password'));
        $client->save();

        return back()->with('status','Your Account has been successfully created!!');
    }


    public function access_client(Request $req)
    {
        $this->validate($req, [ 'email' => 'email|required',
                                'password' => 'required']);

        $client = Client::where('email' , $req->input('email'))->first();
        if($client)
        {
            if(Hash::check($req->input('password'), $client->password))
            {
                Session::put('client', $client);
                return redirect('/shop');
            }
            else{
                return back()->with('status','wrong email or password');
            }
        }
        else{
            return back()->with('status','You do not have account with this email');
        }
    }

    public function logout()
    {
        Session::forget('client');

        return redirect('/shop');
    }

    public function postcheckout(Request $req)
    {
        $oldCart = Session::has('cart')? Session::get('cart'):null;
        $cart = new Cart($oldCart);

        Stripe::setApiKey('sk_test_51KR1yxCkKJ3WtisOkUuuN7SNnbw2kCM4Lf8Obw4wFcRsi1VnpfhOjM5k3z17ayvNkSemd2a6uVhI74BsQhXjsi0k00fhuZo5Qo');

        try{

            $charge = Charge::create(array(
                "amount" => $cart->totalPrice * 100,
                "currency" => "usd",
                "source" => $req->input('stripeToken'), // obtainded with Stripe.js
                "description" => "Test Charge"
            ));



        } catch(\Exception $e){
            Session::put('error', $e->getMessage());
            return redirect('/checkout');
        }

        $payer_id = time();

        $order = new Order();

        $order->name = $req->input('name');
        $order->address = $req->input('address');
        $order->cart = serialize($cart);
        $order->payer_id = $payer_id;

        $order->save();
        Session::forget('cart');

        $orders = Order::where('payer_id', $payer_id)->get();

        $orders->transform(function($order, $key){
            $order->cart = unserialize($order->cart);
            return $order;
        });

        $email = Session::get('client')->email;

        Mail::to($email)->send(new SendMail($orders));

        return redirect('/cart')->with('status','Your purchase has been successfully accomplished !!!');
    }

}
