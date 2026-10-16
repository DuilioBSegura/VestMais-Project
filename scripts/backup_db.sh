#!/usr/bin/env bash
# ==============================================================================
# PROJETO: Vest+ - Plataforma de CMS e Banco de Questões
# ARQUIVO: scripts/backup_db.sh
# FINALIDADE: Script automatizado de backup do MariaDB em container Docker
# RETENÇÃO: Mantém os backups dos últimos 7 dias na pasta backups/
#
# COMO AGENDAR NO CRONTAB DA VPS (Exemplo: todo dia às 03:00 da manhã):
# 0 3 * * * /var/www/VestMais-Project/scripts/backup_db.sh >> /var/www/VestMais-Project/backups/backup.log 2>&1
# ==============================================================================

set -euo pipefail

# Resolução de diretórios
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$(cd "${SCRIPT_DIR}/.." && pwd)"
BACKUP_DIR="${PROJECT_DIR}/backups"

# Timestamp formatado para o arquivo de backup
TIMESTAMP="$(date +"%Y%m%d_%H%M%S")"
DATE_LABEL="$(date +"%Y-%m-%d %H:%M:%S")"

echo "============================================================"
echo "[${DATE_LABEL}] Iniciando rotina de backup do banco Vest+..."
echo "============================================================"

# Carrega variáveis do arquivo .env caso exista
if [ -f "${PROJECT_DIR}/.env" ]; then
    echo "[INFO] Carregando credenciais do arquivo .env..."
    # Exporta variáveis ignorando linhas comentadas e em branco
    export $(grep -v '^#' "${PROJECT_DIR}/.env" | grep -v '^$' | xargs)
fi

# Definições com fallback padrão
CONTAINER_NAME="${DB_CONTAINER:-vestplus_db}"
DB_NAME="${DB_NAME:-vestplus}"
DB_USER="${DB_USER:-vestuser}"
DB_PASS="${DB_PASS:-senha_forte_aqui}"
DB_ROOT_PASS="${DB_ROOT_PASS:-root_senha_segura_vestmais}"
RETENTION_DAYS=7

# Garante a existência do diretório de backups
mkdir -p "${BACKUP_DIR}"

# Verifica se o Docker está em execução
if ! command -v docker >/dev/null 2>&1; then
    echo "[ERRO] Docker CLI não encontrada no sistema host!" >&2
    exit 1
fi

# Verifica se o container do banco está ativo
if ! docker ps --format '{{.Names}}' | grep -q "^${CONTAINER_NAME}$"; then
    echo "[ERRO] Container '${CONTAINER_NAME}' não está em execução no momento." >&2
    exit 1
fi

BACKUP_FILE="${BACKUP_DIR}/vestplus_backup_${TIMESTAMP}.sql.gz"

echo "[INFO] Container ativo: ${CONTAINER_NAME}"
echo "[INFO] Banco alvo: ${DB_NAME}"
echo "[INFO] Destino: ${BACKUP_FILE}"

# Seleciona credenciais preferenciais (root se disponível para dump completo com permissões)
if [ -n "${DB_ROOT_PASS}" ]; then
    AUTH_PARAM=("-uroot" "-p${DB_ROOT_PASS}")
else
    AUTH_PARAM=("-u${DB_USER}" "-p${DB_PASS}")
fi

# Executa o dump diretamente no container e compacta via gzip no host
# --single-transaction: garante consistência sem travar tabelas InnoDB
# --quick: reduz consumo de memória bufferizando linha a linha
# --default-character-set=utf8mb4: preserva acentuação e caracteres especiais
echo "[INFO] Extraindo dump e compactando com gzip..."
if docker exec "${CONTAINER_NAME}" mariadb-dump \
    "${AUTH_PARAM[@]}" \
    --single-transaction \
    --quick \
    --default-character-set=utf8mb4 \
    "${DB_NAME}" | gzip -9 > "${BACKUP_FILE}"; then
    
    # Valida se o arquivo gerado não está vazio
    if [ -s "${BACKUP_FILE}" ]; then
        BACKUP_SIZE="$(du -h "${BACKUP_FILE}" | cut -f1)"
        echo "[SUCESSO] Backup concluído com sucesso!"
        echo "[SUCESSO] Arquivo gerado: ${BACKUP_FILE} (${BACKUP_SIZE})"
    else
        echo "[ERRO] Arquivo de backup gerado com tamanho 0 bytes. Removendo arquivo corrompido..." >&2
        rm -f "${BACKUP_FILE}"
        exit 1
    fi
else
    echo "[ERRO] Falha durante a execução do mariadb-dump dentro do container!" >&2
    rm -f "${BACKUP_FILE}"
    exit 1
fi

# Rotação de backups: mantém apenas os últimos 7 dias
echo "[INFO] Aplicando política de retenção de ${RETENTION_DAYS} dias..."
DELETED_COUNT=0
while IFS= read -r old_file; do
    if [ -n "${old_file}" ]; then
        echo "[INFO] Removendo backup expirado: $(basename "${old_file}")"
        rm -f "${old_file}"
        DELETED_COUNT=$((DELETED_COUNT + 1))
    fi
done < <(find "${BACKUP_DIR}" -type f -name "vestplus_backup_*.sql.gz" -mtime +"${RETENTION_DAYS}")

echo "[INFO] Total de backups expirados removidos: ${DELETED_COUNT}"
echo "[$(date +"%Y-%m-%d %H:%M:%S")] Rotina de backup finalizada com êxito."
exit 0
