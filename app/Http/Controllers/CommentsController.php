<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Comment;

class CommentsController extends Controller
{
	public  function store(Request $request)
	{
		$this->validate($request, [
			'message' => 'required',
		]);

		$comment = new Comment;
		$comment->txt = $request->get('message');
		$comment->post_id =  $request->get('post_id');
		$comment->user_id = Auth::user()->id;
		$comment->save();

		return redirect()->back()->with('status', 'Ваш комментарий будет скоро добавлен!');
	}
}
