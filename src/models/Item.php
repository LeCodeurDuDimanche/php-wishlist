<?php

namespace mywishlist\models;

class Item extends BaseModel{

	protected $table = 'item';
	protected $primaryKey = 'id';


    protected $fillable = ['id' , "liste_id", "titre", "desc", "url", "tarif", "img", "imgLocale", "reservePar", "reserveParUser", "aCagnotte" ,"updated_at", "created_at"];

	public function liste()
	{
		return $this->belongsTo(Liste::class);
	}

	public function cagnottes(){
		return $this->hasMany(Cagnotte::class);
	}

	public function aCagnotte() : bool
	{
		return $this->aCagnotte;
	}

	protected function doDelete() : bool
	{
		foreach($this->cagnottes as $c)
		{
			if (!$c->delete())
				return false;
		}

		return true;
	}

    protected function postDelete() : bool
    {
        return $this->supprimerImage();
    }

	public function supprimerImage() : bool
	{
		if ($this->img && $this->imgLocale)
		{
			$file = $_SERVER["DOCUMENT_ROOT"] . $this->img;
			if (\file_exists($file))
			{
				return unlink($file);
			}
		}
		return true;
	}

	public function reserver($user) : bool
	{
		if ($this->liste->estExpiree())
			return false;

		$this->reserveParUser = null;
		$this->reservePar = null;
		if ($user != null)
		{
			if ($user instanceof Utilisateur)
				$this->reserveParUser = $user->id;
			else
				$this->reservePar = $user;
		}

		return $this->save();
	}

	public function reservePar() : string
	{
		if ($this->reserveParUser)
		{
			$user = Utilisateur::where("id", "=", $this->reserveParUser)->first();
			return $user ? $user->prenom . " " . $user->nom : "";
		}
		return $this->reservePar;
	}

	public function estReserve() : bool
	{
		return $this->cagnottes->isNotEmpty() || $this->reserveParUser !== null || $this->reservePar !== null;
	}
}
