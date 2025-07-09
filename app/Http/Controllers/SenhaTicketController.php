<?php

namespace App\Http\Controllers;

use App\Models\SenhaTicket;
use App\Models\User;
use Illuminate\Http\Request;

class SenhaTicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tipo' => 'required',
        ]);
        $novaSenhaTicket = new SenhaTicket();
        $novaSenhaTicket->tipo = $request->tipo;

        $ultimaSenha = SenhaTicket::where('tipo', $request->tipo)
            ->orderByDesc('created_at')
            ->first();

        $novaSenhaTicket->numero = $ultimaSenha ? $ultimaSenha->numero + 1 : 1;
        $novaSenhaTicket->id_user_criador = $request->user()->id;
        $novaSenhaTicket->save();

        return response()->json([
            'status' => 200,
            'message' => 'senha gerada com sucesso!',
            'data' => $novaSenhaTicket
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $ultimasSenha = SenhaTicket::whereNotNull('id_user_resolvedor')
            ->orderByDesc('created_at')
            ->take(3)
            ->get(); // <- importante

        $formatadas = [];
        foreach ($ultimasSenha as $iten) {
            $usuario = User::find($iten->id_user_criador); // <- funciona se $iten for um objeto
            $formatadas[] = [
                'senha' => $this->formatarSenha($iten),
                'usuario' => $usuario->name,
            ];
        }

        return response()->json([
            'status' => 200,
            'mensagem' => 'retorno das senhas localizadas',
            'data' => $formatadas
        ], 200);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SenhaTicket $senhaTicket) {}

    /**
     * Update the specified resource in storage.
     */
    public function call(Request $request)
    {
        $request->validate([
            'tipo' => 'required',
        ]);
        $ultimaSenha = SenhaTicket::where('id_user_resolvedor', '=', null)
            ->where('tipo', '=', $request->tipo)
            ->orderBy('created_at')
            ->first();

        if (!$ultimaSenha) {
            return response()->json([
                'status' => 404,
                'message' => "nao foi encontrado senhas do tipo $request->tipo"
            ], 404);
        }
        $usuario = $request->user();
        $ultimaSenha->id_user_resolvedor = $usuario->id;
        $ultimaSenha->save();


        $senhaFormatada = $this->formatarSenha($ultimaSenha);


        return response()->json([
            'status' => '200',
            'message' => "Senha $senhaFormatada foi chamada por $usuario->name",
            'data' => $senhaFormatada
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SenhaTicket $senhaTicket)
    {
        //
    }

    private function formatarSenha(SenhaTicket $senha): string
    {
        return strtoupper($senha->tipo) . str_pad($senha->numero, 3, '0', STR_PAD_LEFT);
    }
}
