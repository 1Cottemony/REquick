<?php
// index.php — Página principal do projeto (Requick)
 
require_once __DIR__ . '/config/database.php';
 
// Simula usuário logado (em produção, use sessão/autenticação real)
$usuario_logado = [
    'id'    => 1,
    'nome'  => 'Victor Koba',
    'papel' => 'administrador',
];
 
// Projeto selecionado (em produção virá da URL ou sessão)
$projeto_id = (int)($_GET['projeto'] ?? 1);
 
$db = getDB();
 
// Dados do projeto
$stmt = $db->prepare("SELECT p.*,
    (SELECT COUNT(*) FROM projeto_membros WHERE projeto_id = p.id) AS total_membros
    FROM projetos p WHERE p.id = :pid");
$stmt->execute([':pid' => $projeto_id]);
$projeto = $stmt->fetch();
 
if (!$projeto) {
    die('<h2 style="font-family:sans-serif;padding:2rem">Projeto não encontrado.</h2>');
}
 
// Todos os projetos (menu lateral)
$projetos_todos = $db->query("SELECT id, nome FROM projetos ORDER BY nome")->fetchAll();
 
// Requisitos
$stmt = $db->prepare("SELECT r.*, u.nome AS responsavel_nome, alt.nome AS alterado_por_nome
    FROM requisitos r
    LEFT JOIN usuarios u   ON u.id = r.responsavel_id
    LEFT JOIN usuarios alt ON alt.id = r.alterado_por
    WHERE r.projeto_id = :pid ORDER BY r.criado_em ASC");
$stmt->execute([':pid' => $projeto_id]);
$requisitos = $stmt->fetchAll();
 
// Comentários (últimos 3 + total)
$stmt = $db->prepare("SELECT c.*, u.nome AS usuario_nome, u.papel AS usuario_papel
    FROM comentarios c
    JOIN usuarios u ON u.id = c.usuario_id
    WHERE c.projeto_id = :pid ORDER BY c.criado_em DESC");
$stmt->execute([':pid' => $projeto_id]);
$comentarios = $stmt->fetchAll();
$total_comentarios_ocultos = max(0, count($comentarios) - 3);
 
// Atividades
$stmt = $db->prepare("SELECT a.*, u.nome AS usuario_nome
    FROM atividades a
    JOIN usuarios u ON u.id = a.usuario_id
    WHERE a.projeto_id = :pid ORDER BY a.criado_em DESC LIMIT 15");
$stmt->execute([':pid' => $projeto_id]);
$atividades = $stmt->fetchAll();
 
// Membros disponíveis para responsável
$stmt = $db->prepare("SELECT u.id, u.nome FROM projeto_membros pm
    JOIN usuarios u ON u.id = pm.usuario_id
    WHERE pm.projeto_id = :pid ORDER BY u.nome");
$stmt->execute([':pid' => $projeto_id]);
$membros = $stmt->fetchAll();
 
// Helpers
function statusLabel(string $s): array {
    return match($s) {
        'em_andamento'        => ['Em Andamento',      '#f59e0b', '#fff8ed'],
        'validado'            => ['Validado',           '#10b981', '#f0fdf4'],
        'esperando_validacao' => ['Esperando validação','#6366f1', '#eef2ff'],
        'pendente'            => ['Pendente',           '#64748b', '#f8fafc'],
        'cancelado'           => ['Cancelado',          '#ef4444', '#fef2f2'],
        default               => [$s,                   '#64748b', '#f8fafc'],
    };
}
function prioridadeLabel(string $p): string {
    return match($p) {
        'baixa'   => 'Baixa',
        'media'   => 'Média',
        'alta'    => 'Alta',
        'critica' => 'Crítica',
        default   => $p,
    };
}
function tempoRelativo(string $dt): string {
    $diff = time() - strtotime($dt);
    if ($diff < 60)         return 'agora mesmo';
    if ($diff < 3600)       return floor($diff/60)   . ' min atrás';
    if ($diff < 86400)      return floor($diff/3600)  . ' horas atrás';
    return floor($diff/86400) . ' dias atrás';
}
function iniciais(string $nome): string {
    $parts = explode(' ', trim($nome));
    return strtoupper(mb_substr($parts[0],0,1) . (isset($parts[1]) ? mb_substr($parts[1],0,1) : ''));
}
function avatarCor(string $nome): string {
    $cores = ['#3b82f6','#8b5cf6','#ec4899','#f59e0b','#10b981','#ef4444','#06b6d4','#f97316'];
    return $cores[crc32($nome) % count($cores)];
}
?>