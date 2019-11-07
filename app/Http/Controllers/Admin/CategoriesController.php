<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Category;

class CategoriesController extends Controller
{
    public function index()
	{
		$categories = Category::all();
		return view('admin.categories.index', ['categories' => $categories]);
	}

	public function create()
	{
		return view('admin.categories.create');
	}

	//создание категории
	public function store(Request $request)
	{
		$this->validate($request, [
			'title' => 'required' //обязательно
		]); //валидация

		Category::create($request->all());
		return redirect()->route('categories.index'); //переадресация
	}

	//изменение категории
	protected function edit($id)
	{
		$category = Category::find($id);

		return view('admin.categories.edit', ['category'=>$category]);
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'title' => 'required' //обязательно
		]);

		$category = Category::find($id);

		$category->update($request->all());

		return redirect()->route('categories.index');
	}

	//удаление
	public function destroy($id)
	{
		Category::find($id)->delete();

		return redirect()->route('categories.index');
	}
}
