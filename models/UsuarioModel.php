<?php
class UsuarioModel {
    private $db;
    public function __construct() { $this->db = getDB(); }

    public function listar() {
        return $this->db->query(
            "SELECT u.*, a.nome AS academia_nome
             FROM usuarios u
             LEFT JOIN academias a ON a.id = u.academia_id
             ORDER BY u.perfil ASC, u.nome ASC"
        )->fetchAll();
    }

    public function buscarPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE id=:id");
        $stmt->execute(['id'=>$id]);
        $r = $stmt->fetch(); return $r ?: null;
    }

    public function buscarPorEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email=:e LIMIT 1");
        $stmt->execute(['e'=>$email]);
        $r = $stmt->fetch(); return $r ?: null;
    }

    public function criar($dados) {
        $stmt = $this->db->prepare(
            "INSERT INTO usuarios (nome, email, senha_hash, perfil, academia_id, ativo)
             VALUES (:nome, :email, :senha_hash, :perfil, :academia_id, :ativo)"
        );
        $stmt->execute([
            'nome'        => $dados['nome'],
            'email'       => $dados['email'],
            'senha_hash'  => password_hash($dados['senha'], PASSWORD_BCRYPT),
            'perfil'      => $dados['perfil'],
            'academia_id' => $dados['academia_id'] ?: null,
            'ativo'       => 1,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function atualizar($id, $dados) {
        // Atualiza sem senha
        $stmt = $this->db->prepare(
            "UPDATE usuarios SET nome=:nome, email=:email, perfil=:perfil,
             academia_id=:academia_id, ativo=:ativo WHERE id=:id"
        );
        return $stmt->execute([
            'id'          => $id,
            'nome'        => $dados['nome'],
            'email'       => $dados['email'],
            'perfil'      => $dados['perfil'],
            'academia_id' => $dados['academia_id'] ?: null,
            'ativo'       => isset($dados['ativo']) ? (int)$dados['ativo'] : 1,
        ]);
    }

    public function alterarSenha($id, $novaSenha) {
        $stmt = $this->db->prepare(
            "UPDATE usuarios SET senha_hash=:hash WHERE id=:id"
        );
        return $stmt->execute([
            'hash' => password_hash($novaSenha, PASSWORD_BCRYPT),
            'id'   => $id,
        ]);
    }

    public function excluir($id) {
        $stmt = $this->db->prepare("DELETE FROM usuarios WHERE id=:id");
        return $stmt->execute(['id'=>$id]);
    }

    public function alterarPropriaSenha($id, $senhaAtual, $novaSenha) {
        $usuario = $this->buscarPorId($id);
        if (!$usuario) return ['ok'=>false, 'msg'=>'Usuario nao encontrado.'];
        if (!password_verify($senhaAtual, $usuario['senha_hash'])) {
            return ['ok'=>false, 'msg'=>'Senha atual incorreta.'];
        }
        if (strlen($novaSenha) < 6) {
            return ['ok'=>false, 'msg'=>'A nova senha deve ter ao menos 6 caracteres.'];
        }
        $this->alterarSenha($id, $novaSenha);
        return ['ok'=>true, 'msg'=>'Senha alterada com sucesso!'];
    }
}
