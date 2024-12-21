<?php 

require_once 'model/Model.php';

class ClienteModel extends Model {
   
    
    public function __construct() {
        $this->table = 'clientes';

    }

    public function fields() {  
        return [
            'id',
            'usuario_id',
            'nome',
            'email',
            'senha',
            'telefone',
            'endereco',
            'bairro',
            'cidade',
            'cep',
            'uf',
            'complemento',
            'cpf_cnpj',
            'rg',
            'data_nascimento',
            'profissao',
            'estado_civil',
        ];
    }

    public function rules() {
        return [
            'nome' => 'required',
            'email' => 'required|email',
            'senha' => 'required',
            'telefone' => 'required',
            'endereco' => 'required',
            'bairro' => 'required',
            'cidade' => 'required',
            'cep' => 'required',
            'uf' => 'required',
            'cpf_cnpj' => 'required',
            'rg' => 'required',
            'data_nascimento' => 'required',
            'profissao' => 'required',
            'estado_civil' => 'required',
        ];
    }

    public function labels() {
        return [
            'nome' => 'Nome',
            'email' => 'Email',
            'senha' => 'Senha',
            'telefone' => 'Telefone',
            'endereco' => 'Endereço',
            'bairro' => 'Bairro',
            'cidade' => 'Cidade',
            'cep' => 'CEP',
            'uf' => 'UF',
            'complemento' => 'Complemento',
            'cpf_cnpj' => 'CPF/CNPJ',
            'rg' => 'RG',
            'data_nascimento' => 'Data de Nascimento',
            'profissao' => 'Profissão',
            'estado_civil' => 'Estado Civil',
        ];
    }

    

}