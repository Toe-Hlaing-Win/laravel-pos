<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        // @Error 6, See $total Income count, income today
        // Order List Page တွင် User နှင် သက်ဆိုင်သော Order များသာ ပြထားသညါ
        $orders = Order::where('user_id', $request->user()->id)->with(['items', 'payments'])->get();
        $customers_count = Customer::count();

        return view('home', [
            'orders_count' => $orders->count(),
            'income' => $orders->map(function ($i) {
                if ($i->receivedAmount() > $i->total()) {
                    return $i->total();
                }
                return $i->receivedAmount();
            })->sum(),
            'income_today' => $orders->where('created_at', '>=', date('Y-m-d') . ' 00:00:00')->map(function ($i) {
                if ($i->receivedAmount() > $i->total()) {
                    return $i->total();
                }
                return $i->receivedAmount();
            })->sum(),
            'customers_count' => $customers_count
        ]);
    }
}
