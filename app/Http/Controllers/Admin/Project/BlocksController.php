<?php

namespace App\Http\Controllers\Admin\Project;

use App\Account;
use App\Customer;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Ledger;
use App\Order;
use App\Payreceivable;
use App\Product;
use App\Block;
use App\project;
use App\Traits\TraitModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class BlocksController extends Controller
{
    use TraitModel;
    public function index()
    {
        abort_unless(\Gate::allows('user_access'), 403);
        // dd("sss");
        $blocks = Block::all();
        // dd($blocks);

        return view('admin.block.index', compact('blocks'));
    }

    public function create()
    {
        abort_unless(\Gate::allows('user_create'), 403);

        $projects = project::get();
        $last_code = $this->get_last_code('block');
        $code = acc_code_generate($last_code, 8, 3);
        // dd('ddd');
        return view('admin.block.create', compact('code', 'projects'));
    }

    public function store(Request $request)
    {
        abort_unless(\Gate::allows('user_create'), 403);

        $user = Block::create($request->all());

        return redirect()->route('admin.block.index');
    }

    public function edit(User $user)
    {
        abort_unless(\Gate::allows('user_edit'), 403);

        $roles = Role::all()->pluck('title', 'id');

        $user->load('roles');

        return view('admin.users.edit', compact('roles', 'user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        abort_unless(\Gate::allows('user_edit'), 403);

        $user->update($request->all());
        $user->roles()->sync($request->input('roles', []));

        return redirect()->route('admin.users.index');
    }

    public function show(User $user)
    {
        abort_unless(\Gate::allows('user_show'), 403);

        $user->load('roles');

        return view('admin.users.show', compact('user'));
    }

    public function destroy(User $user)
    {
        abort_unless(\Gate::allows('user_delete'), 403);

        $user->delete();

        return back();
    }

    public function massDestroy(MassDestroyUserRequest $request)
    {
        User::whereIn('id', request('ids'))->delete();

        return response(null, 204);
    }
}
