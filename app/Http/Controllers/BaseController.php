<?php

namespace App\Http\Controllers;

use App\Facades\Acl;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class BaseController extends Controller
{
    protected User $user;
    protected string $role;
    protected Collection $userCompaniesSubsetsId;

    public function __construct()
    {
        $this->user = auth()->user();
        $this->role = Acl::getRole();

        $this->userCompaniesSubsetsId = $this->user->subsets()->pluck('id');
    }
}
