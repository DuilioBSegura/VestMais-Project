#!/usr/bin/env bash
# ==============================================================================
# Vest+ Platform - Script de Backup Automatizado do Banco de Dados
# Caminho: scripts/backup_db.sh
# Uso: ./scripts/backup_db.sh (ou via agendamento cron diário)
# ==============================================================================

set -euo pipefail

# Diretório raiz do projeto (um nível acima da pasta scripts)
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "${SCRIPT_DIR}/.." && pwd)"

# Diretório de armazenamento dos backups
BACKUP_DIR="${PROJECT_ROOT}/backups"
mkdir -p "${BACKUP_DIR}"

# Carrega variáveis do arquivo .env caso exista
ENV_FILE="${PROJECT_ROOT}/.env"
if [ -f "${ENV_FILE}" ]; then
    # shellcheck disable=SC2046
    export $(grep -v '^#' "${ENV_FILE}" | grep '=' | xargs -d '\n')
fi

# Configurações padrão com fallback
DB_NAME="${DB_NAME:-vestplus}"
DB_USER="${DB_USER:-vestuser}"
DB_PASS="${DB_PASS:-senha_forte_aqui}"
DB_CONTAINER="${DB_CONTAINER:-vestmais_db}"

# Timestamp para nomenclatura do arquivo
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_FILENAME="backup_${DB_NAME}_${TIMESTAMP}.sql.gz"
BACKUP_FILEPATH="${BACKUP_DIR}/${BACKUP_FILENAME}"

echo "======================================================================="
echo "   Vest+ Platform - Backup Automatizado de Produção"
echo "======================================================================="
echo " [i] Data/Hora:       $(date +'%Y-%m-%d %H:%M:%S')"
echo " [i] Banco de Dados:  ${DB_NAME}"
echo " [i] Container:       ${DB_CONTAINER}"
echo " [i] Destino:         ${BACKUP_FILEPATH}"
echo "-----------------------------------------------------------------------"

# 1. Verifica se o container do banco está em execução
if ! docker ps --format '{{.Names}}' | grep -Eq "^${DB_CONTAINER}$"; then
    echo " [!] ERRO: Container '${DB_CONTAINER}' não encontrado ou não está em execução."
    echo "           Verifique o status com: docker compose ps"
    exit 1
fi

# 2. Executa o mysqldump de dentro do container e compacta via gzip em fluxo único
echo " [*] Gerando dump consistente e compactando..."
if docker exec -i "${DB_CONTAINER}" mysqldump \
    -u"${DB_USER}" \
    -p"${DB_PASS}" \
    --default-character-set=utf8mb4 \
    --single-transaction \
    --quick \
    --routines \
    --triggers \
    "${DB_NAME}" | gzip -9 > "${BACKUP_FILEPATH}"; then

    # Validação do arquivo gerado
    FILE_SIZE=$(du -h "${BACKUP_FILEPATH}" | cut -f1)
    echo " [✔] Backup gerado com sucesso: ${BACKUP_FILENAME} (${FILE_SIZE})"
else
    echo " [!] ERRO: Falha ao executar mysqldump ou compactação gzip."
    rm -f "${BACKUP_FILEPATH}"
    exit 2
fi

# 3. Política de Retenção: Remove backups com mais de 7 dias
echo " [*] Aplicando política de retenção (mantendo os últimos 7 dias)..."
DELETED_COUNT=0
while IFS= read -r old_file; do
    if [ -n "${old_file}" ]; then
        rm -f "${old_file}"
        echo "     - Removido backup expirado: $(basename "${old_file}")"
        DELETED_COUNT=$((DELETED_COUNT + 1))
    fi
done < <(find "${BACKUP_DIR}" -name "backup_${DB_NAME}_*.sql.gz" -type f -mtime +7)

if [ "${DELETED_COUNT}" -eq 0 ]; then
    echo "     Nenhum backup com mais de 7 dias para expirar."
fi

echo "-----------------------------------------------------------------------"
echo " [✔] Processo de backup concluído com êxito!"
echo "======================================================================="
exit 0
