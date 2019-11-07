<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Tag;

class TagsController extends Controller
{
	public function index()
	{
		$tags = Tag::all();
		return view('admin.tags.index', ['tags' => $tags]);
	}

	public function create()
	{
		return view('admin.tags.create');
	}

	//создание категории
	public function store(Request $request)
	{
		$this->validate($request, [
			'title' => 'required' //обязательно
		]); //валидация

		Tag::create($request->all());
		return redirect()->route('tags.index'); //переадресация
	}

	//изменение категории
	protected function edit($id)
	{
		$tag = Tag::find($id);

		return view('admin.tags.edit', ['tag'=>$tag]);
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'title' => 'required' //обязательно
		]);

		$tag = Tag::find($id);

		$tag->update($request->all());

		return redirect()->route('tags.index');
	}

	//удаление
	public function destroy($id)
	{
		Tag::find($id)->delete();

		return redirect()->route('tags.index');
	}
}
