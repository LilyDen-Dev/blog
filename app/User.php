<?php

namespace App;
use \Storage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    const IS_BANNED = 1;
    const IS_ACTIVE = 0;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    //каждый пользователь имеет статьи, которые он написал
	public function posts()
	{
		return $this->hasMany(Post::class);
	}
    //каждый пользователь оставляет комментарии
	public function comments()
	{
		return $this->hasMany(Comment::class);
	}

	//добавление пользователя
	public static function add($fields)
	{
		$user = new static;
		$user->fill($fields);
		$user->password = bcrypt($fields['password']);
		$user->save();

		return $user;
	}

	//изменение пользователя
	public function edit($fields)
	{
		$this->fill($fields);

		$this->save();
	}

	public function generatePassword($password)
	{
		if($password != null)
		{
			$this->password = bcrypt($password);
			$this->save();
		}
	}

	//удаление пользователя
	public function remove()
	{
		$this->removeAvatar();

		Storage::delete('uploads/' . $this->avatar);//удаление картинки
		$this->delete();
	}

	//загрузка аватара
	public function uploadAvatar($image)
	{
		if($image == null) { return; }

		$this->removeAvatar();

		$filename = str_random(10) . '.' . $image->extension(); //генерация названия файла. он состоит из рандомной строки из 10ти символов далее точка и расширение
		$image->storeAs('uploads', $filename);//сохранить картинку в папу uploads в папке public
		$this->avatar = $filename;
		$this->save();
	}

	public function removeAvatar()
	{
		if($this->avatar != null)
		{
			Storage::delete('uploads/' . $this->avatar);//удаление картинки
		}
	}

	//вывод картинки
	public function getAvatar()
	{
		if($this->avatar == null)
		{
			return '/img/no-user-image.png';
		}
		return '/uploads/' .$this->avatar;
	}

	//назначение пользователя админом
	public function makeAdmin()
	{
		$this->is_admin = 1;
		$this->save();
	}

	//назначение пользователя не админом
	public function makeNormal()
	{
		$this->is_admin = 0;
		$this->save();
	}

	// переключение с пользователя на админа
	public function toggleAdmin($value)
	{
		if($value == null)
		{
			return $this->makeNormal();
		}

		return $this->makeAdmin();
	}

	//бан пользователя
	public function ban()
	{
		$this->status = User::IS_BANNED;
		$this->save();
	}

	//снятие бана
	public function unban()
	{
		$this->status = User::IS_ACTIVE;
		$this->save();
	}

	// переключатель
	public function toggleBan($value)
	{
		if($value == null)
		{
			return $this->unban();
		}

		return $this->ban();
	}
}
