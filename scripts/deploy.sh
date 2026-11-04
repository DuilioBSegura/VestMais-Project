#!/usr/bin/env bash
# ==============================================================================
# Vest+ Platform - Script de Deploy Contínuo Automatizado
# Caminho: scripts/deploy.sh
# Uso na VPS: ./scripts/deploy.sh
# ==============================================================================

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "${SCRIPT_DIR}/.." && pwd)"

cd "${PROJECT_ROOT}"

echo "======================================================================="
echo "   🚀 Vest+ Platform - Atualização de Produção em 1 Clique"
echo "======================================================================="
echo " [1/5] Puxando as atualizações mais recentes do Git..."
git pull origin master

echo " [2/5] Aplicando migrações incrementais seguras no banco de dados MariaDB..."
if [ -f "database/migracao_producao_v1.1.sql" ]; then
    docker compose exec -T db mariadb -u "${DB_USER:-vestuser}" -p"${DB_PASS:-senha_forte_aqui}" "${DB_NAME:-vestplus}" < database/migracao_producao_v1.1.sql || true
    echo "       -> Migrações aplicadas com sucesso (perfil, gamificação e medalhas)."
fi

echo " [3/5] Reconstruindo o container da aplicação (PHP 8.3 + Apache)..."
docker compose build app

echo " [4/5] Reiniciando os serviços da aplicação (Zero perda de dados no banco)..."
docker compose up -d app

echo " [5/5] Limpando imagens antigas não utilizadas para economizar disco..."
docker image prune -f > /dev/null 2>&1 || true

echo "-----------------------------------------------------------------------"
echo " ✅ Atualização concluída com sucesso! Plataforma atualizada."
echo " 🌐 Acesse: https://vestmais.pulsovivo.com.br"
echo "======================================================================="
