<?php

namespace App\Http\Controllers;

use App\Mail\SubscribeEmail;
use App\Subscription;
use Illuminate\Http\Request;

class SudsController extends Controller
{
    public function subscribe(Request $request)
	{
		$this->validate($request, [
			'email' => 'required|email|unique:subscriptions'
		]);

		$subs = Subscription::add($request->get('email'));
		$subs->generateToken();

		\Mail::to($subs)->send(new SubscribeEmail($subs)); // отправка пользователю эмейла (см документацию)

		return redirect()->back()->with('status', 'Проверьте Вашу почту');
	}

	public function verify($token)
	{
		$subs = Subscription::where('token', $token)->firstOrFail();
		$subs->token = null;
		$subs->save();

		return redirect('/')->with('status', 'Ваша почта подтверждена');
	}
}
