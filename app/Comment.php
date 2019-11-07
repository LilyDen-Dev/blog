<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    //определённый пост
	public function post()
	{
		return$this->belongsTo(Post::class);
	}

	//определённый автор
	public function author()
	{
		return$this->belongsTo(User::class, 'user_id');
	}

	//разрешить комментарий
	public function allow()
	{
		$this->status = 1;
		$this->save();
	}

	//запретить комментарий
	public function disAllow()
	{
		$this->status = 0;
		$this->save();
	}

	// переключатель
	public function toggleStatus()
	{
		if($this->status == 0)
		{
			return $this->allow(); //разрешить
		}

		return $this->disAllow();
	}

	//удаление комментария
	public function remove()
	{
		$this->delete();
	}
}
