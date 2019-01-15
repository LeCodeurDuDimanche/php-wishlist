<?php

namespace mywishlist\models;

class Item extends \Illuminate\Database\Eloquent\Model{

	protected $table = 'item';
	protected $primaryKey = 'id';


    protected $fillable = ['id' , "liste_id", "titre", "desc", "url", "tarif", "img", "imgLocale", "reservePar", "reserveParUser", "aCagnotte" ,"updated_at", "created_at"];

	public function liste()
	{
		return $this->belongsTo(Liste::class);
	}

	public function aCagnotte() : Bool
	{
		return $this->aCagnotte;
	}

	public function delete()
	{
		$this->supprimerImage();
		parent::delete();
	}

	public function supprimerImage()
	{
		if ($this->img && $this->estLocale)
		{
			$file = $_SERVER["DOCUMENT_ROOT"] . $this->img;
			if (\file_exists($file))
			 	return unlink($file);
		}
		return false;
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
		return $this->reserveParUser !== null || $this->reservePar !== null;
	}
}
