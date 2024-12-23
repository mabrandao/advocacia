<?php

require_once 'Model.php';

class NoticiasModel extends Model {

    protected $table;
    protected $rules;
    protected $fields;
    
    public function __construct() {
        $this->table = 'noticias';
        $this->rules = $this->rules();
        $this->fields = $this->fields();
    }

    public function fields() {
        return [
            'id',
            'slug',
            'categoria',
            'titulo',
            'image',
            'content',
            'galeria',
            'creat_at',
            'update_at',
            'deleted_at'
        ];
    }

    public function fields_data() {
        return [
            'id',            
            'categoria',
            'titulo'  ,
            'image',            
            'galeria', 
            'creat_at',       
        ];
    }

    public function rules() {
        return [            
            'categoria' => ['required'],
            'titulo' => ['required', 'max:255'],
            'image' => ['required', 'max:255'],
            'content' => ['required'],
            'galeria' => ['required']
        ];
    }

    public function getNoticia($slug) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT * FROM {$this->table} 
                    WHERE slug = :slug 
                    AND deleted_at IS NULL";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':slug', $slug);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar noticia: " . $e->getMessage());
            $this->session->setFlash('error', 'Erro ao buscar noticia: ' . $e->getMessage());
            return false;
        }
    }
}
