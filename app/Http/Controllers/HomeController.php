<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Tag;
use App\Category;


class HomeController extends Controller
{
    public function index()
	{
		$posts = Post::where('status', Post::IS_PUBLIC)->paginate(2);
		// часть фунуций перенесена в Providers.AppServiceProvider, чтобы они работали на разных страницах

		return view('pages.index', [
			'posts' => $posts,
		]);
	}

	public function show($slug)
	{
		$post = Post::where('slug', $slug)->firstOrFail();

		return view('pages.show', compact('post'));
	}

	public function teg($slug)
	{
		$tag = Tag::where('slug', $slug)->firstOrFail();
		$posts = $tag->posts()->paginate(2);

		return view('pages.list', ['posts' => $posts]);
	}

	public function category($slug)
	{
		$category = Category::where('slug', $slug)->firstOrFail();
		$posts = $category->posts()->paginate(2);

		return view('pages.list', ['posts' => $posts]);
	}
}
