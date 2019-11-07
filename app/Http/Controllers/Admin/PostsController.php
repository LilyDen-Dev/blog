<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Post;
use App\Tag;

class PostsController extends Controller
{

    public function index()
    {
		$posts = Post::all();

		return view('admin.posts.index', ['posts' => $posts]);
    }

    public function create()
    {
    	$tags = Tag::pluck('title', 'id')->all();
    	$categories = Category::pluck('title', 'id')->all();

		return view('admin.posts.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
		$this->validate($request, [
			'title' => 'required',
			'content' => 'required',
			'image' => 'nullable|image',
		]);

		$post = Post::add($request->all());
		$post->uploadImage($request->file('image'));
		$post->setCategory($request->get('category_id'));
		$post->setTags($request->get('tags'));
		$post->toggleFeatured($request->get('is_featured'));

		return redirect()->route('posts.index');
	}

    public function edit($id)
    {
		$post = Post::find($id);

		$tags = Tag::pluck('title', 'id')->all();
		$categories = Category::pluck('title', 'id')->all();

		return view('admin.posts.edit', compact('categories', 'tags', 'post'));
    }

    public function update(Request $request, $id)
    {
		$this->validate($request, [
			'title' => 'required',
			'content' => 'required',
			'image' => 'nullable|image',
		]);

		$post = Post::find($id);
		$post->uploadImage($request->file('image'));
		$post->setCategory($request->get('category_id'));
		$post->setTags($request->get('tags'));
		$post->toggleFeatured($request->get('is_featured'));

		$post->edit($request->all());

		return redirect()->route('posts.index');
    }

    public function destroy($id)
    {
		Post::find($id)->delete();

		return redirect()->route('posts.index');
    }
}
