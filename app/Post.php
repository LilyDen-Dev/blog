<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage; //для удаления картинки
use Cviebrock\EloquentSluggable\Sluggable; //используем установленный пакет

class Post extends Model
{
	use Sluggable; //используем установленный пакет
	//связь с постом, одна категория имеет один или более постов

	const IS_DRAFT = 0;
	const IS_PUBLIC = 1;

	protected $fillable = ['title', 'content', 'description']; //массовое присвоение полей

	// у каждой статьи есть категория
    public function category()
	{
		return $this->belongsTo(Category::class);
	}

	// есть автор
	public function author()
	{
		return $this->belongsTo(User::class, 'user_id');
	}

	public function comments()
	{
		return $this->hasMany(Comment::class);
	}

	//связь поста с тегами
	public function tags()
	{
		return $this->belongsToMany(
			Tag::class,
			'posts_tags',
			'post_id',
			'tag_id'
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

	// создание поста
	public static function add($fields) //пост состоит из заголовка и контента
	{
		$post = new static(); //новый экземпляр класса
		$post->fill($fields); //заполнение класса, которое указано выше (массовое присвоение полей)
		$post->user_id = Auth::user()->id; //не савсем понятно как, чтобы узер не указал админа, случайно
		$post->save();

		return $post;
	}

	//изменение и обновление статьи
	public function edit($fields)
	{
		$this->fill($fields);
		$this->save();
	}

	//удаление статьи
	public function remove()
	{
		$this->removeImage();

		$this->delete();
	}

	public function removeImage()
	{
		if($this->image !=null)
		{
			Storage::delete('uploads/' . $this->image);//удаление картинки
		}
	}

	//загрузка картинки
	public function uploadImage($image)
	{
		if($image == null) { return; }

		$this->removeImage();

		$filename = str_random(10) . '.' . $image->extension(); //генерация названия файла. он состоит из рандомной строки из 10ти символов далее точка и расширение
		$image->storeAs('uploads', $filename);//сохранить картинку в папу uploads в папке public
		$this->image = $filename;
		$this->save();
	}

	//вывод картинки
	public function getImage()
	{
		if($this->image == null)
		{
			return '/img/no-image.png';
		}
		return '/uploads/' .$this->image;
	}

	//метод сохранения категории
	public function setCategory($id)
	{
		if($id == null) { return; }
		$this->category_id = $id;
		$this->save();
	}

	//сохранение тегов
	public function setTags($ids)
	{
		if($ids == null) { return; }
		$this->tags()->sync($ids);//синхронизируем с id
	}

	//сохранение в черновик
	public function setDraft()
	{
		$this->status = Post::IS_DRAFT; //то есть 0
		$this->save();
	}

	//сохранение в публикации
	public function setPublic()
	{
		$this->status = Post::IS_PUBLIC; //то есть 1
		$this->save();
	}

	//куда сохранена статья, черновик или опубликована
	public function toggleStatus($value)
	{
		if($value == null)
		{
			return $this->setDraft();
		}

		return $this->setPublic();
	}

	//рекомендованная статья
	public function setFratured()
	{
		$this->is_featured = 1;
		$this->save();
	}

	//стандартная статья
	public function setStandart()
	{
		$this->is_featured = 0;
		$this->save();
	}

	//переключает между рекомендованной и стандартной статьёй
	public function toggleFeatured($value)
	{
		if($value == null)
		{
			return $this->setStandart();
		}

		return $this->setFratured();
	}

	//вывод категории
	public function getCategoryTitle()
	{
		if($this->category != null)
		{
			return $this->category->title;
		}

		return 'нет категорий';
	}

	//вывод ткгов
	public function getTagsTitle()
	{
		if($this->tags->isEmpty())
		{
			return implode(', ', $this->tags->pluck('title')->all());
		}

		return 'нет тегов';
	}

	public function getCategoryID()
	{
		return $this->category != null ? $this->category->id : null; //Здезь записано: если категория не равняется нулю, то выведи ID категории, иначе не выводить ничего
	}

	public function getDate()
	{
		return Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)-> format('F d, Y');
	}

	//предыдущий пост
	public function hasPrevious()
	{
		return self::where('id', '<', $this->id)->max('id'); //найди посты с id меньшими текущего и выбери из них максимальный
	}

	public function getPrevious()
	{
		$postID = $this->hasPrevious();
		return self::find($postID);
	}

	//следующий пост
	public function hasNext()
	{
		return self::where('id', '>', $this->id)->max('id'); //найди посты с id большими текущего и выбери из них минимальный
	}

	public function getNext()
	{
		$postID = $this->hasNext();
		return self::find($postID);
	}

	public function related()
	{
		return self::all()->except($this->id);
	}

	public function hasCategory()
	{
		return $this->category != null ? true : false;
	}

	public function getComments()
	{
		return $this->comments()->where('status', 1)->get();
	}

}
