<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable; //используем установленный пакет

class Category extends Model
{
	use Sluggable; //используем установленный пакет
    //связь с постом, одна категория имеет один или более постов

	protected $fillable = ['title']; //указываем какие данные запроса нужно сохранять в таблицу

	public function posts()
	{
		return $this->hasMany(Post::class);
	}

	//функция сохранения ссылки при создании записи в таблице
	public function sluggable()
	{
		return [
			'slug' => [
				'source' => 'title'
			]
		];
	}
}
