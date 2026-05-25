<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        abort_if(! auth()->user()->isAdmin(), 403, 'Acesso restrito a administradores.');
        $users = User::orderBy('name')->get();
        return view('usuarios.index', compact('users'));
    }

    public function store(Request $request)
    {
        abort_if(! auth()->user()->isAdmin(), 403);

        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:6|confirmed',
            'role'          => 'required|in:admin,faturamento,producao',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        User::create([
            'name'        => $data['name'],
            'email'       => $data['email'],
            'password'    => Hash::make($data['password']),
            'role'        => $data['role'],
            'permissions' => $data['role'] === 'admin' ? null : ($data['permissions'] ?? []),
            'ativo'       => true,
        ]);

        return back()->with('success', "Usuário \"{$data['name']}\" criado com sucesso.");
    }

    public function update(Request $request, User $user)
    {
        abort_if(! auth()->user()->isAdmin(), 403);

        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,' . $user->id,
            'role'          => 'required|in:admin,faturamento,producao',
            'password'      => 'nullable|string|min:6|confirmed',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        $update = [
            'name'        => $data['name'],
            'email'       => $data['email'],
            'role'        => $data['role'],
            'permissions' => $data['role'] === 'admin' ? null : ($data['permissions'] ?? []),
        ];

        if (! empty($data['password'])) {
            $update['password'] = Hash::make($data['password']);
        }

        $user->update($update);

        return back()->with('success', "Usuário \"{$user->name}\" atualizado.");
    }

    public function toggleAtivo(User $user)
    {
        abort_if(! auth()->user()->isAdmin(), 403);
        abort_if($user->id === auth()->id(), 422, 'Você não pode desativar seu próprio usuário.');

        $user->update(['ativo' => ! $user->ativo]);
        $msg = $user->ativo ? "Usuário \"{$user->name}\" ativado." : "Usuário \"{$user->name}\" desativado.";

        return back()->with('success', $msg);
    }
}
