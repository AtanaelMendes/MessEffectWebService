<?php


namespace App\DTOs;


use App\User;

class AccountInfoDTO extends BaseDTO
{
    protected $nome;
    protected $email;
    protected $image_file_name;
    protected $roles;
    protected $created_at;
    protected $updated_at;

    /**
     * AccountInfoDTO constructor.
     * @param User $account
     */
    public function __construct(User $account)
    {
        $this->nome = $account->name;
        $this->email = $account->email;
        $this->image_file_name = $account->image->file_name ?? null;
        $this->roles = $account->roles;
        $this->created_at = $account->created_at;
        $this->updated_at = $account->updated_at;
    }
}
