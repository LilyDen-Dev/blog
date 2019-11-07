<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable; //используем установленный пакет

class Tag extends Model
{
	use Sluggable; //используем установленный пакет
	//связь с постом, одна категория имеет один или более постов

	protected $fillable = ['title']; //указываем какие данные запроса нужно сохранять в таблицу

	//связь птегов с постами
	public function posts()
	{
		return $this->belongsToMany(
			Tag::class,
			'post_tags',
			'tag_id',
			'post_id'
		);
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
