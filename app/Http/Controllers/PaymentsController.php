<?php

namespace App\Http\Controllers;

use App\Models\Subscribe;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;

class PaymentsController extends Controller
{
    /**
     * @return SandboxEnvironment
     */
    public static function environment()
    {

        $clientId = env("PAYPAL_CLIENT_ID");
        $clientSecret = env("PAYPAL_SECRET");
        return new SandboxEnvironment($clientId, $clientSecret);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \PayPalHttp\HttpException
     * @throws \PayPalHttp\IOException
     */
    public function payment(Request $request)
    {
        $data = [];
        $data['purchase_units'] = [
            [
                'name' => 'example.com',
                'desc'  => 'Payment for subscription',
                'amount' => [
                    'currency_code' => 'USD',
                    'value' => $request->amount
                ]
            ]
        ];
        $data['intent'] = 'CAPTURE';
        $data['application_context'] = [
            'return_url' => route('payment.success'),
            'cancel_url' => route('payment.cancel')
        ];

        $paypal = new PayPalHttpClient(self::environment());
        $OrderRequest = new OrdersCreateRequest();
        $OrderRequest->prefer('return=representation');
        $OrderRequest->body = $data;
        $response = response()->json($paypal->execute($OrderRequest));
        $content = $response->getOriginalContent();

        return $response;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \PayPalHttp\HttpException
     * @throws \PayPalHttp\IOException
     */
    public function success(Request $request)
    {
        $paypal = new PayPalHttpClient(self::environment());
        $orderID = $request->input('orderId');
        $request = new OrdersCaptureRequest($orderID);
        $response = response()->json($paypal->execute($request));

        $content = $response->getOriginalContent();
        if($content->result->status === 'COMPLETED'){
            $subscribeId = $request->input('subscribeId');
            $user = Auth::user();
            $user->subscribe_id = $subscribeId;
            $user->subscribe_expire_at = Carbon::now()->addDays(Subscribe::EXPARE_DAYS);
            $user->save();
        }
        return $response;
    }


    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function cancel()
    {
        return view('client.paymentCancelled');
    }
}
