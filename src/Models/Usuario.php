<?php

declare(strict_types=1);

namespace App\Models;

use Config\Database;
use PDO;

/**
 * Model para gerenciamento de Usuários (Alunos e Administradores).
 */
final class Usuario
{
    /**
     * Busca um usuário pelo seu identificador primário.
     *
     * @return array<string, mixed>|null
     */
    public static function getById(int $id): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'SELECT id, nome, email, senha, tipo, vestibular_foco, bio, telefone, criado_em 
             FROM usuarios 
             WHERE id = :id 
             LIMIT 1'
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        return $usuario ?: null;
    }

    /**
     * Busca um usuário pelo seu endereço de e-mail.
     *
     * @return array<string, mixed>|null
     */
    public static function getByEmail(string $email): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'SELECT id, nome, email, senha, tipo, vestibular_foco, bio, telefone, criado_em 
             FROM usuarios 
             WHERE email = :email 
             LIMIT 1'
        );
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        return $usuario ?: null;
    }

    /**
     * Verifica se um e-mail já pertence a outro usuário cadastrado.
     */
    public static function emailExistsForOther(string $email, int $excludeId): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'SELECT COUNT(*) FROM usuarios WHERE email = :email AND id != :id'
        );
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':id', $excludeId, PDO::PARAM_INT);
        $stmt->execute();

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Atualiza os dados de perfil do usuário.
     */
    public static function updatePerfil(
        int $id,
        string $nome,
        string $email,
        string $vestibularFoco = 'ENEM',
        ?string $bio = null,
        ?string $telefone = null
    ): bool {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'UPDATE usuarios 
             SET nome = :nome, 
                 email = :email, 
                 vestibular_foco = :vestibular_foco, 
                 bio = :bio, 
                 telefone = :telefone 
             WHERE id = :id'
        );

        $stmt->bindValue(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':vestibular_foco', $vestibularFoco, PDO::PARAM_STR);
        $stmt->bindValue(':bio', $bio, $bio !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':telefone', $telefone, $telefone !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Atualiza o hash seguro da senha do usuário.
     */
    public static function updateSenha(int $id, string $senhaHash): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('UPDATE usuarios SET senha = :senha WHERE id = :id');
        $stmt->bindValue(':senha', $senhaHash, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Retorna a lista de usuários com suporte a filtro de busca e tipo.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function getAll(?string $busca = null, ?string $tipo = null): array
    {
        $pdo = Database::getConnection();
        $sql = 'SELECT id, nome, email, tipo, vestibular_foco, bio, telefone, criado_em 
                FROM usuarios 
                WHERE 1=1';
        $params = [];

        if ($busca !== null && trim($busca) !== '') {
            $sql .= ' AND (nome LIKE :busca OR email LIKE :busca)';
            $params[':busca'] = '%' . trim($busca) . '%';
        }

        if ($tipo !== null && in_array($tipo, ['aluno', 'admin'], true)) {
            $sql .= ' AND tipo = :tipo';
            $params[':tipo'] = $tipo;
        }

        $sql .= ' ORDER BY criado_em DESC';

        $stmt = $pdo->prepare($sql);
        foreach ($params as $param => $val) {
            $stmt->bindValue($param, $val, PDO::PARAM_STR);
        }
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retorna o total de usuários cadastrados.
     */
    public static function countTotal(): int
    {
        $pdo = Database::getConnection();
        return (int) $pdo->query('SELECT COUNT(*) FROM usuarios')->fetchColumn();
    }

    /**
     * Retorna o total de usuários por tipo.
     */
    public static function countByTipo(string $tipo): int
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM usuarios WHERE tipo = :tipo');
        $stmt->bindValue(':tipo', $tipo, PDO::PARAM_STR);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    /**
     * Remove um usuário pelo ID.
     */
    public static function delete(int $id): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('DELETE FROM usuarios WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Atualiza o papel/tipo do usuário ('aluno' ou 'admin').
     */
    public static function updateTipo(int $id, string $tipo): bool
    {
        if (!in_array($tipo, ['aluno', 'admin'], true)) {
            return false;
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('UPDATE usuarios SET tipo = :tipo WHERE id = :id');
        $stmt->bindValue(':tipo', $tipo, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
