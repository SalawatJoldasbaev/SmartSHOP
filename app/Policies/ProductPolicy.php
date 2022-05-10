<?php

namespace App\Policies;

use Auth;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create()
    {
        return Auth::user()->role == 'admin' or Auth::user()->role == 'ceo';
    }

    /**
     * Determine whether the user can update the model.
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update()
    {
        return Auth::user()->role == 'admin' or Auth::user()->role == 'ceo';
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete()
    {
        return Auth::user()->role == 'admin' or Auth::user()->role == 'ceo';
    }
}
