<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Payment\StorePaymentRequest;
use App\Http\Requests\Admin\Payment\UpdatePaymentRequest;
use App\Models\Payment;
use Exception;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index() {
        $payments = Payment::all();
        return view('admin.payment.index',[
            'payments' => $payments
        ]);
    }

    public function create() {
        return view('admin.payment.create');
    }

    public function store(StorePaymentRequest $request) {
        try {
            Payment::storePayment($request->validated());
            return redirect()->route('admin.payment.index')->with('success','payment Stored Successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('error',$e->getMessage());
        }
    }
    public function edit(Payment $payment) {
        return view('admin.payment.edit',[
            'payment' => $payment
        ]);
    }

    public function update(UpdatePaymentRequest $request,Payment $payment) {
        try {
            Payment::updatePayment($request->validated(),$payment);
            return redirect()->route('admin.payment.index')->with('success','Payment Updated Successfully');
        } catch (Exception $e) {
            // dd($e->getMessage());
            return redirect()->back()->with('error',$e->getMessage());
        }
    }

    public function delete(Payment $payment) {
        try {
            $payment->delete();
            return redirect()->route('admin.payment.index')->with('success','Payment Deleted Successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('error',$e->getMessage());
        }
    }
}
